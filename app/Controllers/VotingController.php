<?php

namespace App\Controllers;

use App\Libraries\BookVotingService;
use App\Models\BookSuggestionModel;
use App\Models\BookVoteModel;
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

        $currentIds = $data['userVotedIds'];
        $voteModel  = new BookVoteModel();
        $userId     = (int) current_user_id();
        $sessionId  = (int) $session['id'];

        foreach (array_diff($submittedIds, $currentIds) as $id) {
            $voteModel->insert([
                'session_id'    => $sessionId,
                'suggestion_id' => $id,
                'user_id'       => $userId,
            ]);
        }

        foreach (array_diff($currentIds, $submittedIds) as $id) {
            $existing = $voteModel->findUserVoteForSuggestion($sessionId, $userId, $id);
            if ($existing !== null) {
                $voteModel->delete((int) $existing['id']);
            }
        }

        return redirect()->to('/votacao')->with('success', 'Seus votos foram atualizados.');
    }
}
