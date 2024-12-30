<?php

namespace App\Controllers;

use App\Helpers\Redirect;
use App\Helpers\Session;
use App\Requests\CreateUserRequest;
use App\Requests\LoginRequest;
use App\Services\UserService;

class AuthController extends BaseController
{
    /**
     * AuthController constructor.
     * @param UserService $userService
    */
    public function __construct(
        private UserService $userService
    ) {
        parent::__construct();
    }

    /**
     * Render the login view
    */
    public function viewLogin(): void
    {
        echo $this->render('Auth/login.twig');
    }

    /**
     * Handle the login request
    */
    public function login(): void
    {
        try {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $payload = new LoginRequest(email: $email, password: $password);
            $usr = $this->userService->getUserByEmail($email);

            if (!$usr || !password_verify($password, $usr->password)) {
                Redirect::withFlash('Invalid credentials')->back();
                return;
            }

            Session::set('email', $usr->email);
            Redirect::to('dashboard');
        } catch (\Exception $e) {
            echo $this->render('Auth/login.twig', ['flash' => $e->getMessage()]);
            return;
        }
    }

    /**
     * Render the register view
    */
    public function viewRegister(): void
    {
        echo $this->render('Auth/register.twig');
    }

    /**
     * Handle the register request
    */
    public function register(): void
    {
        try {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];

            $payload = new CreateUserRequest(
                name: $name,
                email: $email,
                password: $password,
                confirm_password: $confirmPassword,
            );

            $payload->validate();

            $usr = $this->userService->getUserByEmail($email);
            if ($usr) {
                throw new \Exception('User with this email already exists');
                return;
            }

            $this->userService->createUser($payload);
            Redirect::with('flash', 'User created successfully')->to('login');
        } catch (\Exception $e) {
            echo $this->render('Auth/register.twig', ['flash' => $e->getMessage()]);
            return;
        }
    }

    /**
     * Handle the logout request
    */
    public function logout(): void
    {
        Session::destroy();
        Redirect::to('login');
    }
}
