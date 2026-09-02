<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CountryModel;
use App\Models\UserModel;

class UsersController extends BaseController
{
    public function index()
    {
        $countries   = (new CountryModel())->findAll();
        $showDeleted = (bool) $this->request->getGet('show_deleted');

        $userModel = new UserModel();

        if ($showDeleted) {
            $userModel->withDeleted();
        }

        return view('admin/users/index', [
            'users'         => $userModel->orderBy('name', 'ASC')->findAll(),
            'countriesById' => array_column($countries, null, 'id'),
            'showDeleted'   => $showDeleted,
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

        $userModel = new UserModel();
        $existing  = $userModel->findByPhoneWithDeleted($data['phone']);

        // Telefone de um usuario removido: reaproveita o registro em vez de
        // inserir (o indice UNIQUE de phone nao permitiria uma linha nova).
        if ($existing !== null && $existing['deleted_at'] !== null) {
            $userModel->restore((int) $existing['id'], $data);

            return redirect()->to('/admin/users')
                ->with('success', 'Usuário reativado com os novos dados. Comentários e votos anteriores continuam vinculados a ele.');
        }

        $userModel->insert($data);

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

    public function resetPassword(int $id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if ($user === null) {
            return redirect()->to('/admin/users')->with('error', 'Usuário não encontrado.');
        }

        if (! $this->validate(['password' => 'required|min_length[6]'])) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $userModel->update($id, [
            'password'             => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'must_change_password' => true,
        ]);

        return redirect()->to("/admin/users/{$id}/edit")->with('success', 'Senha resetada com sucesso. O usuário deverá trocá-la no primeiro acesso.');
    }

    public function delete(int $id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if ($user === null) {
            return redirect()->to('/admin/users')->with('error', 'Usuário não encontrado.');
        }

        if ($id === 1) {
            return redirect()->to('/admin/users')->with('error', 'O usuário principal não pode ser removido.');
        }

        if ($id === (int) current_user_id()) {
            return redirect()->to('/admin/users')->with('error', 'Você não pode remover o próprio usuário.');
        }

        if ($user['role'] === UserModel::ROLE_ADMIN && $userModel->countActiveAdmins() <= 1) {
            return redirect()->to('/admin/users')->with('error', 'É necessário manter ao menos um administrador ativo.');
        }

        $userModel->update($id, [
            'remember_token'            => null,
            'remember_token_expires_at' => null,
        ]);

        $userModel->delete($id);

        return redirect()->to('/admin/users')->with('success', 'Usuário removido. O histórico dele foi preservado.');
    }

    public function restore(int $id)
    {
        $userModel = new UserModel();
        $user = $userModel->withDeleted()->find($id);

        if ($user === null || $user['deleted_at'] === null) {
            return redirect()->to('/admin/users')->with('error', 'Usuário removido não encontrado.');
        }

        $userModel->restore($id);

        return redirect()->to('/admin/users')->with('success', 'Usuário reativado.');
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

        $existing = $userModel->findByPhoneWithDeleted($phone);

        if ($existing !== null && ($id === null || (int) $existing['id'] !== $id)) {
            if ($existing['deleted_at'] === null) {
                $this->validator->setError('phone', 'Este telefone ja esta em uso.');
                return null;
            }

            // Na criacao o registro removido e reaproveitado por create();
            // ao editar outro usuario nao da, colidiria com o UNIQUE.
            if ($id !== null) {
                $this->validator->setError('phone', 'Este telefone pertence a um usuário removido. Reative-o na listagem de usuários.');
                return null;
            }
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
