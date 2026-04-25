<?php

namespace App\Controllers;

use App\Models\BookModel;
use App\Models\CommentModel;
use App\Models\CommentReplyModel;

class CommentController extends BaseController
{
    public function store()
    {
        $bookModel = new BookModel();
        $bookId = (int) $this->request->getPost('book_id');
        $book = $bookId > 0 ? $bookModel->find($bookId) : $bookModel->getCurrentBook();

        if ($book === null) {
            return redirect()->to('/')->with('error', 'Nenhum livro em destaque foi encontrado.');
        }

        $rules = [
            'content' => 'required|min_length[3]|max_length[1000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/')->withInput()->with('errors', $this->validator->getErrors());
        }

        $commentModel = new CommentModel();
        $commentModel->insert([
            'book_id'  => $book['id'],
            'user_id'  => current_user_id(),
            'content'  => $this->request->getPost('content'),
        ]);

        return redirect()->back()->with('success', 'Comentário enviado.');
    }

    public function reply(int $commentId)
    {
        $bookModel = new BookModel();
        $commentModel = new CommentModel();
        $comment = $commentModel->find($commentId);

        if ($comment === null) {
            return redirect()->to('/')->with('error', 'Comentário não encontrado.');
        }

        $book = $bookModel->find((int) $comment['book_id']);

        if ($book === null || (! $book['meeting_happened'] && (int) $comment['user_id'] !== current_user_id())) {
            return redirect()->to('/')->with('error', 'Você só pode responder comentários visíveis para o seu perfil neste momento.');
        }

        $rules = [
            'content' => 'required|min_length[2]|max_length[1000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $replyModel = new CommentReplyModel();
        $replyModel->insert([
            'comment_id' => $comment['id'],
            'user_id'    => current_user_id(),
            'content'    => $this->request->getPost('content'),
        ]);

        return redirect()->back()->with('success', 'Resposta enviada.');
    }
}
