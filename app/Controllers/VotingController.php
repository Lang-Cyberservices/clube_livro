<?php

namespace App\Controllers;

use App\Libraries\BookVotingService;
use App\Models\BookSuggestionModel;
use App\Models\BookVoteLogModel;
use App\Models\VotingSessionModel;

class VotingController extends BaseController
{
    public function index()
    {
        $service = new BookVotingService();
        $data = $service->buildVotingData(current_user_id());

        return view('voting/index', [
            'title' => 'Votação do próximo livro',
            ...$data,
        ]);
    }

    public function storeSuggestion()
    {
        $service = new BookVotingService();
        $data = $service->buildVotingData(current_user_id());
        $session = $data['session'];

        if (! $data['canManageSuggestions'] || $session === null || $session['status'] !== VotingSessionModel::STATUS_COLLECTING) {
            return redirect()->to('/votacao')->with('error', 'As sugestões estão fechadas neste momento.');
        }

        if ($data['userSuggestionCount'] >= 2) {
            return redirect()->to('/votacao')->with('error', 'Cada usuário pode cadastrar no máximo duas sugestões por ciclo.');
        }

        $rules = [
            'title'       => 'required|min_length[3]|max_length[255]',
            'author'      => 'required|min_length[3]|max_length[255]',
            'description' => 'required|min_length[20]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $coverImage = $this->resolveUploadedCover(null);
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('errors', ['cover_image_file' => $e->getMessage()]);
        }

        (new BookSuggestionModel())->insert([
            'session_id'  => $session['id'],
            'user_id'     => current_user_id(),
            'title'       => trim((string) $this->request->getPost('title')),
            'author'      => trim((string) $this->request->getPost('author')),
            'cover_image' => $coverImage,
            'description' => trim((string) $this->request->getPost('description')),
        ]);

        return redirect()->to('/votacao')->with('success', 'Sugestão cadastrada com sucesso.');
    }

    public function vote()
    {
        $service = new BookVotingService();
        $data = $service->buildVotingData(current_user_id());
        $session = $data['session'];

        if ($session === null || $session['status'] !== VotingSessionModel::STATUS_ACTIVE) {
            return redirect()->to('/votacao')->with('error', 'A votação não está aberta no momento.');
        }

        $validIds = array_map(static fn (array $s): int => (int) $s['id'], $data['suggestions']);
        $submittedIds = array_values(array_intersect(
            array_map('intval', (array) ($this->request->getPost('suggestion_id') ?? [])),
            $validIds
        ));

        $userId    = (int) current_user_id();
        $sessionId = (int) $session['id'];

        try {
            $service->syncUserVotes($sessionId, $userId, $submittedIds, $userId, BookVoteLogModel::ACTOR_USER);
        } catch (\RuntimeException $exception) {
            return redirect()->to('/votacao')->with('error', $exception->getMessage());
        }

        return redirect()->to('/votacao')->with('success', 'Seus votos foram atualizados.');
    }
}
