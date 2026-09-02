<?php

namespace App\Libraries;

use App\Models\BookModel;
use App\Models\BookSuggestionModel;
use App\Models\BookVoteLogModel;
use App\Models\BookVoteModel;
use App\Models\UserModel;
use App\Models\VotingSessionModel;
use DateTimeImmutable;
use RuntimeException;

class BookVotingService
{
    public function __construct(
        private ?BookModel $bookModel = null,
        private ?VotingSessionModel $sessionModel = null,
        private ?BookSuggestionModel $suggestionModel = null,
        private ?BookVoteModel $voteModel = null,
        private ?BookVoteLogModel $voteLogModel = null,
        private ?UserModel $userModel = null
    ) {
        $this->bookModel = $this->bookModel ?? new BookModel();
        $this->sessionModel = $this->sessionModel ?? new VotingSessionModel();
        $this->suggestionModel = $this->suggestionModel ?? new BookSuggestionModel();
        $this->voteModel = $this->voteModel ?? new BookVoteModel();
        $this->voteLogModel = $this->voteLogModel ?? new BookVoteLogModel();
        $this->userModel = $this->userModel ?? new UserModel();
    }

    public function isSuggestionWindowAvailable(): bool
    {
        return ! $this->bookModel->hasOngoingBook();
    }

    public function getOrCreateOpenSession(?int $userId = null): ?array
    {
        if (! $this->isSuggestionWindowAvailable()) {
            return $this->sessionModel->getOpenSession();
        }

        $session = $this->sessionModel->getOpenSession();

        if ($session !== null) {
            return $session;
        }

        $sessionId = $this->sessionModel->insert([
            'status'     => VotingSessionModel::STATUS_COLLECTING,
            'created_by' => $userId,
        ], true);

        return $this->sessionModel->find($sessionId);
    }

    public function buildVotingData(?int $userId = null): array
    {
        $session = $this->getOrCreateOpenSession($userId);
        $suggestions = $session === null ? [] : $this->suggestionModel->getSessionSuggestionsWithStats((int) $session['id']);
        $userVotedIds = [];
        $userSuggestionCount = 0;

        if ($session !== null && $userId !== null) {
            $votes = $this->voteModel->findUserVotes((int) $session['id'], $userId);
            $userVotedIds = array_map('intval', array_column($votes, 'suggestion_id'));
            $userSuggestionCount = $this->suggestionModel->countForUserInSession((int) $session['id'], $userId);
        }

        $votersBySuggestion = $session !== null
            ? $this->voteModel->getVotersBySuggestion((int) $session['id'])
            : [];

        return [
            'canManageSuggestions' => $this->isSuggestionWindowAvailable(),
            'session'              => $session,
            'suggestions'          => $suggestions,
            'userVotedIds'         => $userVotedIds,
            'userSuggestionCount'  => $userSuggestionCount,
            'votersBySuggestion'   => $votersBySuggestion,
        ];
    }

    /**
     * Sincroniza as sugestões votadas por um membro (visão da página de votação).
     */
    public function syncUserVotes(int $sessionId, int $userId, array $suggestionIds, int $actorId, string $actorRole): void
    {
        $this->assertVotingIsOpen($sessionId);

        $validIds = array_map(
            static fn (array $suggestion): int => (int) $suggestion['id'],
            $this->suggestionModel->where('session_id', $sessionId)->findAll()
        );

        $targetIds = array_values(array_unique(array_intersect(array_map('intval', $suggestionIds), $validIds)));
        $currentIds = array_map(
            'intval',
            array_column($this->voteModel->findUserVotes($sessionId, $userId), 'suggestion_id')
        );

        $this->runVoteChanges(
            $targetIds,
            $currentIds,
            fn (int $suggestionId, bool $shouldVote) => $this->applyVoteChange(
                $sessionId,
                $suggestionId,
                $userId,
                $shouldVote,
                $actorId,
                $actorRole
            )
        );
    }

    /**
     * Sincroniza os membros que votaram em uma sugestão (visão do admin).
     */
    public function syncSuggestionVoters(int $sessionId, int $suggestionId, array $userIds, int $actorId): void
    {
        $this->assertVotingIsOpen($sessionId);

        $suggestion = $this->suggestionModel->find($suggestionId);

        if ($suggestion === null || (int) $suggestion['session_id'] !== $sessionId) {
            throw new RuntimeException('Sugestão não encontrada neste ciclo.');
        }

        $submittedIds = array_values(array_unique(array_map('intval', $userIds)));
        $targetIds = $submittedIds === [] ? [] : array_map(
            'intval',
            array_column($this->userModel->whereIn('id', $submittedIds)->findAll(), 'id')
        );
        $currentIds = $this->voteModel->findSuggestionVoterIds($sessionId, $suggestionId);

        $this->runVoteChanges(
            $targetIds,
            $currentIds,
            fn (int $userId, bool $shouldVote) => $this->applyVoteChange(
                $sessionId,
                $suggestionId,
                $userId,
                $shouldVote,
                $actorId,
                BookVoteLogModel::ACTOR_ADMIN
            )
        );
    }

    private function assertVotingIsOpen(int $sessionId): void
    {
        $session = $this->sessionModel->find($sessionId);

        if ($session === null || $session['status'] !== VotingSessionModel::STATUS_ACTIVE) {
            throw new RuntimeException('A votação não está aberta no momento.');
        }
    }

    /**
     * Aplica as inclusões e remoções em transação, delegando cada mudança ao callback.
     */
    private function runVoteChanges(array $targetIds, array $currentIds, callable $apply): void
    {
        $db = $this->voteModel->db;
        $db->transStart();

        foreach (array_diff($targetIds, $currentIds) as $id) {
            $apply((int) $id, true);
        }

        foreach (array_diff($currentIds, $targetIds) as $id) {
            $apply((int) $id, false);
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            throw new RuntimeException('Não foi possível atualizar os votos.');
        }
    }

    private function applyVoteChange(int $sessionId, int $suggestionId, int $userId, bool $shouldVote, int $actorId, string $actorRole): void
    {
        $existing = $this->voteModel->findUserVoteForSuggestion($sessionId, $userId, $suggestionId);

        if ($shouldVote && $existing === null) {
            $this->voteModel->insert([
                'session_id'    => $sessionId,
                'suggestion_id' => $suggestionId,
                'user_id'       => $userId,
            ]);

            $this->voteLogModel->log($sessionId, $suggestionId, $userId, BookVoteLogModel::ACTION_ADDED, $actorId, $actorRole);

            return;
        }

        if (! $shouldVote && $existing !== null) {
            $this->voteModel->delete((int) $existing['id']);

            $this->voteLogModel->log($sessionId, $suggestionId, $userId, BookVoteLogModel::ACTION_REMOVED, $actorId, $actorRole);
        }
    }

    public function activateVoting(int $adminUserId): void
    {
        $session = $this->getOrCreateOpenSession($adminUserId);

        if ($session === null) {
            throw new RuntimeException('Nenhum ciclo de sugestões disponível para ativar.');
        }

        if ($session['status'] !== VotingSessionModel::STATUS_COLLECTING) {
            throw new RuntimeException('A votação já está ativa ou já foi encerrada.');
        }

        $suggestionCount = $this->suggestionModel->where('session_id', $session['id'])->countAllResults();

        if ($suggestionCount === 0) {
            throw new RuntimeException('Cadastre ao menos uma sugestão antes de ativar a votação.');
        }

        $this->sessionModel->update((int) $session['id'], [
            'status'       => VotingSessionModel::STATUS_ACTIVE,
            'activated_by' => $adminUserId,
            'activated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function finalizeVoting(int $adminUserId): array
    {
        $session = $this->sessionModel->getActiveSession();

        if ($session === null) {
            throw new RuntimeException('Nenhuma votação ativa para finalizar.');
        }

        $suggestions = $this->suggestionModel->getSessionSuggestionsWithStats((int) $session['id']);

        if ($suggestions === []) {
            throw new RuntimeException('Não há sugestões para encerrar esta votação.');
        }

        $maxVotes            = (int) $suggestions[0]['vote_count'];
        $tied                = array_values(array_filter($suggestions, fn ($s) => (int) $s['vote_count'] === $maxVotes));
        $winner              = count($tied) > 1 ? $tied[array_rand($tied)] : $tied[0];
        $winner['_tie_broken'] = count($tied) > 1;
        $startDate = new DateTimeImmutable('today');
        $meetingDate = $this->firstSundayAfterReadingPeriod($startDate);

        $this->bookModel->db->transStart();

        $bookId = $this->bookModel->insert([
            'title'                  => $winner['title'],
            'author'                 => $winner['author'],
            'cover_image'            => $winner['cover_image'],
            'description'            => $winner['description'],
            'start_reading_date'     => $startDate->format('Y-m-d'),
            'scheduled_meeting_date' => $meetingDate->format('Y-m-d'),
            'actual_meeting_date'    => null,
            'meeting_happened'       => 0,
            'is_current'             => 1,
        ], true);

        $this->bookModel->setCurrentBook((int) $bookId);

        $this->sessionModel->update((int) $session['id'], [
            'status'                => VotingSessionModel::STATUS_FINISHED,
            'finished_by'           => $adminUserId,
            'finished_at'           => date('Y-m-d H:i:s'),
            'winning_suggestion_id' => $winner['id'],
        ]);

        $this->bookModel->db->transComplete();

        if (! $this->bookModel->db->transStatus()) {
            throw new RuntimeException('Nao foi possivel finalizar a votação.');
        }

        return $winner;
    }

    /**
     * Primeiro domingo a partir de 25 dias apos o inicio da leitura (inclusive).
     */
    private function firstSundayAfterReadingPeriod(DateTimeImmutable $startDate): DateTimeImmutable
    {
        $date = $startDate->modify('+25 days');

        // 7 = domingo no formato ISO-8601 ('N')
        if ((int) $date->format('N') === 7) {
            return $date;
        }

        return $date->modify('next sunday');
    }
}
