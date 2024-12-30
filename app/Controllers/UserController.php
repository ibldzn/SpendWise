<?php

namespace App\Controllers;

use App\Services\UserService;

class UserController extends BaseController
{
    /**
     * UserController constructor.
     * @param UserService $userService
    */
    public function __construct(
        private UserService $userService
    ) {
        parent::__construct();
    }

    /**
     * Render the user dashboard
    */
    public function viewUserDashboard(): void
    {

    }
}
