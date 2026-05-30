<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['url', 'form', 'auth'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
    }

    /**
     * Resolves cover image from an uploaded file or a URL field.
     * File takes priority. Returns $existing if neither is provided.
     *
     * @throws \RuntimeException if uploaded file has invalid type or exceeds 5 MB
     */
    protected function resolveUploadedCover(?string $existing = null): ?string
    {
        $file = $this->request->getFile('cover_image_file');

        if ($file !== null && $file->isValid() && ! $file->hasMoved()) {
            $allowed = ['image/jpeg', 'image/png', 'image/webp'];

            if (! in_array($file->getMimeType(), $allowed, true)) {
                throw new \RuntimeException('Formato de imagem inválido. Use JPG, PNG ou WEBP.');
            }

            if ($file->getSize() > 5 * 1024 * 1024) {
                throw new \RuntimeException('A imagem não pode ter mais de 5 MB.');
            }

            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/covers', $newName);

            return base_url('uploads/covers/' . $newName);
        }

        $url = trim((string) $this->request->getPost('cover_image_url'));

        return $url !== '' ? $url : $existing;
    }

}
