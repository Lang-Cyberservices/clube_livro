<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\BookVotingService;

class VotingController extends BaseController
{
    public function index()
    {
        $service = new BookVotingService();
        $data = $service->buildVotingData(current_user_id());

        return view('admin/voting/index', [
            'title' => 'Gerenciar votação',
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
}
