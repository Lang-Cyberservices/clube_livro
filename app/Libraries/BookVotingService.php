<?php

namespace App\Libraries;

use App\Models\BookModel;
use App\Models\BookSuggestionModel;
use App\Models\BookVoteModel;
use App\Models\VotingSessionModel;
use DateTimeImmutable;
use RuntimeException;

class BookVotingService
{
    public function __construct(
        private ?BookModel $bookModel = null,
        private ?VotingSessionModel $sessionModel = null,
        private ?BookSuggestionModel $suggestionModel = null,
        private ?BookVoteModel $voteModel = null
    ) {
        $this->bookModel = $this->bookModel ?? new BookModel();
        $this->sessionModel = $this->sessionModel ?? new VotingSessionModel();
        $this->suggestionModel = $this->suggestionModel ?? new BookSuggestionModel();
        $this->voteModel = $this->voteModel ?? new BookVoteModel();
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

        return [
            'canManageSuggestions' => $this->isSuggestionWindowAvailable(),
            'session'              => $session,
            'suggestions'          => $suggestions,
            'userVotedIds'         => $userVotedIds,
            'userSuggestionCount'  => $userSuggestionCount,
        ];
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

        $winner = $suggestions[0];
        $startDate = new DateTimeImmutable('today');
        $meetingDate = $startDate->modify('+30 days');

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
}
