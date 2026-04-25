<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! is_logged_in()) {
            return redirect()->to('/auth/login')->with('error', 'Faça login para acessar a área administrativa.');
        }

        if (! is_admin()) {
            return redirect()->to('/')->with('error', 'Você não tem permissão para acessar essa área.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
