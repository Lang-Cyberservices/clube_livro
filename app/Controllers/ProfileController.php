<?php

namespace App\Controllers;

use App\Models\CountryModel;
use App\Models\UserModel;

class ProfileController extends BaseController
{
    public function edit()
    {
        $user = (new UserModel())->find((int) current_user_id());

        if ($user === null) {
            return redirect()->to('/')->with('error', 'Usuario nao encontrado.');
        }

        $country   = ! empty($user['country_id']) ? (new CountryModel())->find((int) $user['country_id']) : null;
        $phoneMask = $country['phone_mask'] ?? null;

        return view('profile/edit', [
            'title'     => 'Meu cadastro',
            'user'      => $user,
            'phoneMask' => $phoneMask,
        ]);
    }

    public function update()
    {
        $userModel = new UserModel();
        $userId = (int) current_user_id();
        $user = $userModel->find($userId);

        if ($user === null) {
            return redirect()->to('/')->with('error', 'Usuario nao encontrado.');
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[120]',
        ];

        $password = (string) $this->request->getPost('password');
        $passwordConfirmation = (string) $this->request->getPost('password_confirmation');

        if ($password !== '' || $passwordConfirmation !== '') {
            $rules['password'] = 'required|min_length[6]';
            $rules['password_confirmation'] = 'required|matches[password]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $phone = UserModel::normalizePhone((string) $this->request->getPost('phone'));

        if (! preg_match('/^\d{9,11}$/', $phone)) {
            return redirect()->back()->withInput()->with('error', 'Informe um telefone com 9 a 11 dígitos.');
        }

        $existing = $userModel->findByPhone($phone);

        if ($existing !== null && (int) $existing['id'] !== $userId) {
            return redirect()->back()->withInput()->with('error', 'Este telefone ja esta em uso.');
        }

        $data = [
            'name'  => $this->request->getPost('name'),
            'phone' => $phone,
        ];

        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $userModel->update($userId, $data);
        $updatedUser = $userModel->find($userId);

        if ($updatedUser !== null) {
            session()->set('user', [
                'id'                   => $updatedUser['id'],
                'name'                 => $updatedUser['name'],
                'phone'                => $updatedUser['phone'],
                'role'                 => $updatedUser['role'],
                'must_change_password' => (bool) $updatedUser['must_change_password'],
            ]);
        }

        return redirect()->to('/perfil')->with('success', 'Cadastro atualizado com sucesso.');
    }
}
