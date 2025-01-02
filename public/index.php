<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\ExpenseController;
use App\Controllers\HomeController;
use App\Controllers\UserController;
use App\Repositories\CategoryRepository;
use App\Repositories\ExpenseRepository;
use App\Repositories\UserRepository;
use App\Services\CategoryService;
use App\Services\ExpenseService;
use App\Services\UserService;
use Bramus\Router\Router;
use Dotenv\Dotenv;

ini_set('session.cookie_secure', '0');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Strict');

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$userRepository = new UserRepository();
$expenseRepository = new ExpenseRepository();
$categoryRepository = new CategoryRepository();
$userService = new UserService($userRepository, $expenseRepository, $categoryRepository);
$expenseService = new ExpenseService($expenseRepository);
$categoryService = new CategoryService($categoryRepository);
$homeController = new HomeController($userService, $expenseService);
$authController = new AuthController($userService);
$expenseController = new ExpenseController($userService, $expenseService);
$userController = new UserController($userService, $expenseService);
$categoryController = new CategoryController($userService, $categoryService);

$router = new Router();

$router->get('/', [$homeController, 'index']);
$router->get('/about', [$homeController, 'about']);
$router->get('/register', [$authController, 'viewRegister']);
$router->post('/register', [$authController, 'register']);
$router->get('/login', [$authController, 'viewLogin']);
$router->post('/login', [$authController, 'login']);
$router->get('/logout', [$authController, 'logout']);
$router->get('/dashboard', [$homeController, 'dashboard']);
$router->post('/add-expense', [$expenseController, 'addExpense']);
$router->post('/add-category', [$categoryController, 'addCategory']);
$router->delete('/categories/{id}', [$categoryController, 'removeCategory']);
$router->post('/categories/update', [$categoryController, 'updateCategory']);
$router->put('/profile', [$userController, 'updateAccount']);
$router->delete('/profile', [$userController, 'deleteAccount']);
$router->get('/profile/{page}', [$userController, 'userDashboard']);

$router->set404([$homeController, 'notFound']);

$router->run();
