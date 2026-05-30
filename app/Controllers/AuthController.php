<?php

namespace App\Controllers;

use App\Models\CountryModel;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        return view('auth/login', [
            'title'     => 'Entrar no clube',
            'countries' => (new CountryModel())->orderBy('name', 'ASC')->findAll(),
        ]);
    }

    public function attemptLogin()
    {
        $rules = [
            'phone'    => 'required',
            'password' => 'required|min_length[6]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $countryId = (int) $this->request->getPost('country_id');
        $phone = UserModel::normalizePhone((string) $this->request->getPost('phone'));
        $user = $userModel->findByCountryAndPhone($countryId, $phone);

        if ($user === null || ! password_verify((string) $this->request->getPost('password'), $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Telefone ou senha inválidos.');
        }

        $this->storeUserInSession($user);

        if ($user['must_change_password']) {
            return redirect()->to('/auth/primeiro-acesso')->with('success', 'No primeiro acesso, atualize sua senha para continuar.');
        }

        return redirect()->to('/')->with('success', 'Login realizado com sucesso.');
    }

    public function firstAccess()
    {
        return view('auth/first_access', [
            'title' => 'Trocar senha',
        ]);
    }

    public function updateFirstAccessPassword()
    {
        $rules = [
            'password'              => 'required|min_length[6]',
            'password_confirmation' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $userId = (int) current_user_id();
        $userModel->update($userId, [
            'password'             => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'must_change_password' => 0,
        ]);

        $user = $userModel->find($userId);

        if ($user !== null) {
            $this->storeUserInSession($user);
        }

        return redirect()->to('/')->with('success', 'Senha atualizada com sucesso.');
    }

    public function logout()
    {
        session()->remove('user');
        session()->destroy();

        return redirect()->to('/auth/login')->with('success', 'Sessão encerrada com sucesso.');
    }

    private function storeUserInSession(array $user): void
    {
        session()->set('user', [
            'id'                   => $user['id'],
            'name'                 => $user['name'],
            'phone'                => $user['phone'],
            'role'                 => $user['role'],
            'must_change_password' => (bool) $user['must_change_password'],
        ]);
    }
}
