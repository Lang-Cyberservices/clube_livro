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
                    store_user_session($user);

                    return;
                }
                delete_cookie('remember_me');
            }

            return redirect()->to('/auth/login')->with('error', 'Faça login para continuar.');
        }

        // A sessao guarda apenas uma copia dos dados: confere no banco se o
        // usuario continua ativo (removidos sao derrubados na hora) e de
        // quebra atualiza nome/perfil alterados pelo admin.
        $user = (new UserModel())->find((int) current_user_id());

        if ($user === null) {
            delete_cookie('remember_me');
            session()->remove('user');

            return redirect()->to('/auth/login')->with('error', 'Sua conta não está mais disponível. Fale com um administrador.');
        }

        store_user_session($user);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
