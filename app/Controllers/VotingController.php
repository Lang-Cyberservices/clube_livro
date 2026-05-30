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
            'cover_image' => 'permit_empty|valid_url_strict',
            'description' => 'required|min_length[20]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $coverImage = trim((string) $this->request->getPost('cover_image'));

        (new BookSuggestionModel())->insert([
            'session_id'  => $session['id'],
            'user_id'     => current_user_id(),
            'title'       => trim((string) $this->request->getPost('title')),
            'author'      => trim((string) $this->request->getPost('author')),
            'cover_image' => $coverImage !== '' ? $coverImage : null,
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

        $suggestionId = (int) $this->request->getPost('suggestion_id');
        $suggestionIds = array_map(static fn (array $suggestion): int => (int) $suggestion['id'], $data['suggestions']);

        if (! in_array($suggestionId, $suggestionIds, true)) {
            return redirect()->to('/votacao')->with('error', 'Selecione uma sugestão válida para votar.');
        }

        $voteModel = new BookVoteModel();
        $existingVote = $voteModel->findUserVote((int) $session['id'], (int) current_user_id());

        if ($existingVote !== null) {
            $voteModel->update((int) $existingVote['id'], [
                'suggestion_id' => $suggestionId,
            ]);
        } else {
            $voteModel->insert([
                'session_id'    => $session['id'],
                'suggestion_id' => $suggestionId,
                'user_id'       => current_user_id(),
            ]);
        }

        return redirect()->to('/votacao')->with('success', 'Seu voto foi registrado.');
    }
}
