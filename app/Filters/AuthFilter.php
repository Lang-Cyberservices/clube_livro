<?php

namespace App\Filters;

use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('cookie');

        if (! is_logged_in()) {
            $token = get_cookie('remember_me');
            if ($token !== null && $token !== '') {
                $user = (new UserModel())->findByRememberToken(hash('sha256', $token));
                if ($user !== null) {
                    session()->set('user', [
                        'id'                   => $user['id'],
                        'name'                 => $user['name'],
                        'phone'                => $user['phone'],
                        'role'                 => $user['role'],
                        'must_change_password' => (bool) $user['must_change_password'],
                    ]);

                    return;
                }
                delete_cookie('remember_me');
            }

            return redirect()->to('/auth/login')->with('error', 'Faça login para continuar.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
