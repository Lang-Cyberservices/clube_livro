<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BookModel;

class BooksController extends BaseController
{
    public function index()
    {
        $bookModel = new BookModel();

        return view('admin/books/index', [
            'books' => $bookModel->orderBy('is_current', 'DESC')->orderBy('created_at', 'DESC')->findAll(),
        ]);
    }

    public function new()
    {
        return view('admin/books/form', [
            'book'   => null,
            'action' => '/admin/books',
            'title'  => 'Cadastrar novo livro',
        ]);
    }

    public function create()
    {
        $data = $this->getValidatedBookData();

        if ($data === null) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $bookModel = new BookModel();
        $bookId = $bookModel->insert($data, true);

        if ((int) ($data['is_current'] ?? 0) === 1) {
            $bookModel->setCurrentBook((int) $bookId);
        }

        return redirect()->to('/admin/books')->with('success', 'Livro cadastrado com sucesso.');
    }

    public function edit(int $id)
    {
        $bookModel = new BookModel();
        $book = $bookModel->find($id);

        if ($book === null) {
            return redirect()->to('/admin/books')->with('error', 'Livro não encontrado.');
        }

        return view('admin/books/form', [
            'book'   => $book,
            'action' => "/admin/books/{$id}",
            'title'  => 'Editar livro',
        ]);
    }

    public function update(int $id)
    {
        $bookModel = new BookModel();
        $book = $bookModel->find($id);

        if ($book === null) {
            return redirect()->to('/admin/books')->with('error', 'Livro não encontrado.');
        }

        $data = $this->getValidatedBookData();

        if ($data === null) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $bookModel->update($id, $data);

        if ((int) ($data['is_current'] ?? 0) === 1) {
            $bookModel->setCurrentBook($id);
        }

        return redirect()->to('/admin/books')->with('success', 'Livro atualizado com sucesso.');
    }

    public function highlight(int $id)
    {
        $bookModel = new BookModel();

        if ($bookModel->find($id) === null) {
            return redirect()->to('/admin/books')->with('error', 'Livro não encontrado.');
        }

        $bookModel->setCurrentBook($id);

        return redirect()->to('/admin/books')->with('success', 'Livro definido como destaque atual.');
    }

    private function getValidatedBookData(): ?array
    {
        $rules = [
            'title'                  => 'required|min_length[3]|max_length[255]',
            'author'                 => 'required|min_length[3]|max_length[255]',
            'cover_image'            => 'required|valid_url_strict',
            'description'            => 'required|min_length[20]',
            'start_reading_date'     => 'required|valid_date[Y-m-d]',
            'scheduled_meeting_date' => 'required|valid_date[Y-m-d]',
            'actual_meeting_date'    => 'permit_empty|valid_date[Y-m-d]',
        ];

        if (! $this->validate($rules)) {
            return null;
        }

        $meetingHappened = $this->request->getPost('meeting_happened') === '1';
        $actualDate = $this->request->getPost('actual_meeting_date');

        if ($meetingHappened && empty($actualDate)) {
            $actualDate = date('Y-m-d');
        }

        if (! $meetingHappened) {
            $actualDate = null;
        }

        return [
            'title'                  => $this->request->getPost('title'),
            'author'                 => $this->request->getPost('author'),
            'cover_image'            => $this->request->getPost('cover_image'),
            'description'            => $this->request->getPost('description'),
            'start_reading_date'     => $this->request->getPost('start_reading_date'),
            'scheduled_meeting_date' => $this->request->getPost('scheduled_meeting_date'),
            'actual_meeting_date'    => $actualDate,
            'meeting_happened'       => $meetingHappened ? 1 : 0,
            'is_current'             => $this->request->getPost('is_current') === '1' ? 1 : 0,
        ];
    }
}
