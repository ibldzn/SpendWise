<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Bramus\Router\Router;
use Dotenv\Dotenv;

ini_set('session.cookie_secure', '0');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Strict');

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$userRepository = new UserRepository();
$userService = new UserService($userRepository);
$homeController = new HomeController();
$authController = new AuthController($userService);

$router = new Router();

$router->setNamespace('\App\Controllers');

$router->get('/', [$homeController, 'index']);
$router->get('/register', [$authController, 'viewRegister']);
$router->post('/register', [$authController, 'register']);
$router->get('/login', [$authController, 'viewLogin']);
$router->post('/login', [$authController, 'login']);
$router->get('/logout', [$authController, 'logout']);

$router->set404([$homeController, 'notFound']);

$router->run();
