<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\BookVotingService;
use App\Models\BookSuggestionModel;
use App\Models\UserModel;
use App\Models\VotingSessionModel;

class VotingController extends BaseController
{
    public function index()
    {
        $service = new BookVotingService();
        $data = $service->buildVotingData(current_user_id());

        return view('admin/voting/index', [
            'title' => 'Gerenciar votação',
            'users' => (new UserModel())->findAll(),
            ...$data,
        ]);
    }

    public function activate()
    {
        try {
            (new BookVotingService())->activateVoting((int) current_user_id());
        } catch (\RuntimeException $exception) {
            return redirect()->to('/admin/votacao')->with('error', $exception->getMessage());
        }

        return redirect()->to('/admin/votacao')->with('success', 'Votação ativada com sucesso.');
    }

    public function finalize()
    {
        try {
            $winner = (new BookVotingService())->finalizeVoting((int) current_user_id());
        } catch (\RuntimeException $exception) {
            return redirect()->to('/admin/votacao')->with('error', $exception->getMessage());
        }

        return redirect()->to('/admin/books')->with('success', 'Votação encerrada. O livro "' . $winner['title'] . '" foi criado como leitura atual.');
    }

    public function storeSuggestion()
    {
        $service = new BookVotingService();
        $session = $service->getOpenSession();

        if ($session === null || $session['status'] !== VotingSessionModel::STATUS_COLLECTING) {
            return redirect()->to('/admin/votacao')->with('error', 'A coleta de sugestões não está aberta.');
        }

        $targetUserId = (int) $this->request->getPost('user_id');
        $userModel    = new UserModel();

        if ($userModel->find($targetUserId) === null) {
            return redirect()->to('/admin/votacao')->with('error', 'Usuário inválido.');
        }

        $suggestionModel = new BookSuggestionModel();
        $count = $suggestionModel->countForUserInSession((int) $session['id'], $targetUserId);

        if ($count >= 2) {
            return redirect()->to('/admin/votacao')->with('error', 'Este usuário já atingiu o limite de 2 sugestões.');
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
            return redirect()->to('/admin/votacao')->with('error', $e->getMessage());
        }

        $suggestionModel->insert([
            'session_id'  => $session['id'],
            'user_id'     => $targetUserId,
            'title'       => trim((string) $this->request->getPost('title')),
            'author'      => trim((string) $this->request->getPost('author')),
            'cover_image' => $coverImage,
            'description' => trim((string) $this->request->getPost('description')),
        ]);

        return redirect()->to('/admin/votacao')->with('success', 'Sugestão cadastrada com sucesso.');
    }
}
