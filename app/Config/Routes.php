<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('HomeController');
$routes->setDefaultMethod('index');
$routes->setAutoRoute(false);

$routes->get('/', 'HomeController::index');
$routes->get('sobre', 'HomeController::about');
$routes->get('livros-anteriores', 'HomeController::previousBooks');
$routes->get('livros/(:num)', 'HomeController::showBook/$1');

$routes->group('auth', ['filter' => 'guest'], static function ($routes) {
    $routes->get('login', 'AuthController::login');
    $routes->post('login', 'AuthController::attemptLogin');
});

$routes->group('auth', ['filter' => 'auth'], static function ($routes) {
    $routes->get('primeiro-acesso', 'AuthController::firstAccess');
    $routes->post('primeiro-acesso', 'AuthController::updateFirstAccessPassword');
});

$routes->get('logout', 'AuthController::logout', ['filter' => 'auth']);

$routes->group('', ['filter' => 'authpass'], static function ($routes) {
    $routes->post('comments', 'CommentController::store');
    $routes->post('comments/(:num)/replies', 'CommentController::reply/$1');
    $routes->get('perfil', 'ProfileController::edit');
    $routes->post('perfil', 'ProfileController::update');
    $routes->get('votacao', 'VotingController::index');
    $routes->post('votacao/sugestoes', 'VotingController::storeSuggestion');
    $routes->post('votacao/votar', 'VotingController::vote');
});

$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'authpassadmin'], static function ($routes) {
    $routes->get('/', 'DashboardController::index');

    $routes->get('books', 'BooksController::index');
    $routes->get('books/new', 'BooksController::new');
    $routes->post('books', 'BooksController::create');
    $routes->get('books/(:num)/edit', 'BooksController::edit/$1');
    $routes->post('books/(:num)', 'BooksController::update/$1');
    $routes->post('books/(:num)/highlight', 'BooksController::highlight/$1');
    $routes->get('votacao', 'VotingController::index');
    $routes->post('votacao/ativar', 'VotingController::activate');
    $routes->post('votacao/finalizar', 'VotingController::finalize');

    $routes->get('users', 'UsersController::index');
    $routes->get('users/new', 'UsersController::new');
    $routes->post('users', 'UsersController::create');
    $routes->get('users/(:num)/edit', 'UsersController::edit/$1');
    $routes->post('users/(:num)', 'UsersController::update/$1');
});
