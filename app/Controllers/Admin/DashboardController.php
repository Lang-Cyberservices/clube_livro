<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BookModel;
use App\Models\BookSuggestionModel;
use App\Models\CommentModel;
use App\Models\CommentReplyModel;
use App\Models\UserModel;
use App\Models\VotingSessionModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $bookModel = new BookModel();
        $currentBook = $bookModel->getCurrentBook();

        return view('admin/dashboard', [
            'stats' => [
                'users'       => (new UserModel())->countAllResults(),
                'books'       => $bookModel->countAllResults(),
                'comments'    => (new CommentModel())->countAllResults(),
                'replies'     => (new CommentReplyModel())->countAllResults(),
                'suggestions' => (new BookSuggestionModel())->countAllResults(),
            ],
            'currentBook' => $currentBook,
            'votingSession' => (new VotingSessionModel())->getOpenSession(),
        ]);
    }
}
