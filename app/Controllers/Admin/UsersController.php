<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CountryModel;
use App\Models\UserModel;

class UsersController extends BaseController
{
    public function index()
    {
        $countries = (new CountryModel())->findAll();

        return view('admin/users/index', [
            'users'        => (new UserModel())->orderBy('name', 'ASC')->findAll(),
            'countriesById' => array_column($countries, null, 'id'),
        ]);
    }

    public function new()
    {
        return view('admin/users/form', [
            'user'      => null,
            'action'    => '/admin/users',
            'title'     => 'Cadastrar novo usuário',
            'countries' => (new CountryModel())->orderBy('name', 'ASC')->findAll(),
        ]);
    }

    public function create()
    {
        $data = $this->getValidatedUserData();

        if ($data === null) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        (new UserModel())->insert($data);

        return redirect()->to('/admin/users')->with('success', 'Usuário cadastrado com sucesso.');
    }

    public function edit(int $id)
    {
        $user = (new UserModel())->find($id);

        if ($user === null) {
            return redirect()->to('/admin/users')->with('error', 'Usuário não encontrado.');
        }

        return view('admin/users/form', [
            'user'      => $user,
            'action'    => "/admin/users/{$id}",
            'title'     => 'Editar usuário',
            'countries' => (new CountryModel())->orderBy('name', 'ASC')->findAll(),
        ]);
    }

    public function update(int $id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if ($user === null) {
            return redirect()->to('/admin/users')->with('error', 'Usuário não encontrado.');
        }

        $data = $this->getValidatedUserData($id);

        if ($data === null) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if ($data['password'] === null) {
            unset($data['password']);
        }

        $userModel->update($id, $data);

        return redirect()->to('/admin/users')->with('success', 'Usuário atualizado com sucesso.');
    }

    private function getValidatedUserData(?int $id = null): ?array
    {
        $userModel = new UserModel();
        $rules = [
            'name'  => 'required|min_length[3]|max_length[120]',
            'role'  => 'required|in_list[admin,user]',
        ];

        $phone = UserModel::normalizePhone((string) $this->request->getPost('phone'));
        $password = (string) $this->request->getPost('password');

        if ($id === null || $password !== '') {
            $rules['password'] = 'required|min_length[6]';
        }

        if (! $this->validate($rules)) {
            return null;
        }

        if (! preg_match('/^\d{9,11}$/', $phone)) {
            $this->validator->setError('phone', 'Informe um telefone com 9 a 11 dígitos.');
            return null;
        }

        $existing = $userModel->findByPhone($phone);

        if ($existing !== null && ($id === null || (int) $existing['id'] !== $id)) {
            $this->validator->setError('phone', 'Este telefone ja esta em uso.');
            return null;
        }

        $mustChangePassword = true;

        if ($id !== null && $password === '') {
            $existingUser = $userModel->find($id);
            $mustChangePassword = (bool) ($existingUser['must_change_password'] ?? false);
        }

        return [
            'name'                 => $this->request->getPost('name'),
            'country_id'           => (int) $this->request->getPost('country_id'),
            'phone'                => $phone,
            'role'                 => $this->request->getPost('role'),
            'must_change_password' => $mustChangePassword,
            'password'             => $password !== '' ? password_hash($password, PASSWORD_DEFAULT) : null,
        ];
    }
}
