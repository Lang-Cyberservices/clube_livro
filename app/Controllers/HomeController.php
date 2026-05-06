<?php

namespace App\Controllers;

use App\Models\BookModel;
use App\Models\CommentModel;
use App\Models\CommentReplyModel;
use App\Models\VotingSessionModel;

class HomeController extends BaseController
{
    public function index()
    {
        $bookModel = new BookModel();
        $currentBook = $bookModel->getCurrentBook();
        $discussion = $this->buildDiscussionData($currentBook);

        return view('home/index', [
            'title'    => 'Livro atual',
            'book'     => $currentBook,
            'comments' => $discussion['comments'],
            'replies'  => $discussion['replies'],
            'votingSession' => (new VotingSessionModel())->getOpenSession(),
        ]);
    }

    public function about()
    {
        return view('pages/about', [
            'title' => 'Sobre o grupo',
        ]);
    }

    public function previousBooks()
    {
        $bookModel = new BookModel();

        return view('books/index', [
            'title' => 'Livros anteriores',
            'books' => $bookModel->getPreviousBooks(),
        ]);
    }

    public function showBook(int $bookId)
    {
        $bookModel = new BookModel();
        $book = $bookModel->find($bookId);

        if ($book === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Livro não encontrado.');
        }

        $discussion = $this->buildDiscussionData($book);

        return view('books/show', [
            'title'    => $book['title'],
            'book'     => $book,
            'comments' => $discussion['comments'],
            'replies'  => $discussion['replies'],
        ]);
    }

    private function buildDiscussionData(?array $book): array
    {
        $comments = [];
        $replies = [];

        if ($book === null || ! is_logged_in()) {
            return [
                'comments' => $comments,
                'replies'  => $replies,
            ];
        }

        $commentModel = new CommentModel();
        $replyModel = new CommentReplyModel();

        $comments = $commentModel->getVisibleComments(
            (int) $book['id'],
            current_user_id(),
            (bool) $book['meeting_happened']
        );

        $replies = $replyModel->getRepliesForComments(
            array_column($comments, 'id'),
            current_user_id(),
            (bool) $book['meeting_happened']
        );

        return [
            'comments' => $comments,
            'replies'  => $replies,
        ];
    }
}
