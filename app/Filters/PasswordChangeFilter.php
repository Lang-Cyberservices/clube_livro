<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PasswordChangeFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (must_change_password()) {
            return redirect()->to('/auth/primeiro-acesso')->with('error', 'Atualize sua senha antes de continuar.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
