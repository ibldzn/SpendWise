<?php

namespace App\Controllers;

use App\Helpers\Redirect;
use App\Helpers\Requests;
use App\Helpers\Session;
use App\Models\UserModel;
use App\Requests\UpdateUserRequest;
use App\Services\ExpenseService;
use App\Services\UserService;

class UserController extends BaseController
{
    /**
     * UserController constructor.
     * @param UserService $userService
    */
    public function __construct(
        private UserService $userService,
        private ExpenseService $expenseService
    ) {
        parent::__construct();
    }

    public function userDashboard(string $page): void
    {
        if (!Session::has('email')) {
            Redirect::withFlash('You must be logged in to view the dashboard')->to('login');
            return;
        }

        $user = $this->userService->getUserByEmail(Session::get('email'));
        if (!$user) {
            Session::remove('email');
            Redirect::withFlash('User not found')->to('login');
            return;
        }

        if ($page === 'account') {
            $this->profile($user);
        } elseif ($page === 'categories') {
            $this->categories($user);
        } elseif ($page === 'expenses') {
            $this->expenses($user);
        }
    }

    public function deleteAccount(): void
    {
        if (!Session::has('email')) {
            Redirect::withFlash('You must be logged in to perform this action')->to('login');
            return;
        }

        $user = $this->userService->getUserByEmail(Session::get('email'));
        if (!$user) {
            Session::remove('email');
            Redirect::withFlash('User not found')->to('login');
            return;
        }

        $this->userService->deleteUser($user);
        Session::destroy();
        Redirect::withFlash('Account deleted')->to('login');
    }

    public function updateAccount(): void
    {
        try {
            if (!Session::has('email')) {
                Redirect::withFlash('You must be logged in to perform this action')->to('login');
                return;
            }

            $user = $this->userService->getUserByEmail(Session::get('email'));
            if (!$user) {
                Session::remove('email');
                Redirect::withFlash('User not found')->to('login');
                return;
            }

            $data = (Requests::parsePutData())['fields'];
            $payload = new UpdateUserRequest(
                id: $user->id,
                name: $data['name'],
                email: $data['email'],
                current_password: $data['current_password'],
                new_password: $data['new_password'],
                confirm_password: $data['confirm_password']
            );
            $payload->validate($user);

            if (!$this->userService->updateUser($user->id, $payload)) {
                // Redirect::withFlash('Failed to update account')->back();
                echo 'Failed to update account';
                return;
            }

            // Redirect::withFlash('Account updated')->back();
            echo 'Account updated';
        } catch (\Exception $e) {
            // Redirect::withFlash($e->getMessage())->back();
            echo $e->getMessage();
            return;
        }
    }

    private function profile(UserModel $user): void
    {
        echo $this->render('User/profile.twig', ['user' => $user]);
    }

    private function categories(UserModel $user): void
    {
        $categories = $this->userService->getCategoriesForUser($user->id);
        echo $this->render('User/categories.twig', [
            'categories' => $categories,
            'user' => $user,
        ]);
    }

    private function expenses(UserModel $user): void
    {
        $dates = $this->expenseService->getDistinctExpenseDatesForUser($user->id);
        if (count($dates) === 0) {
            $dates = [date('Y-m')];
        }

        if (!isset($_GET['date'])) {
            Redirect::to('profile/expenses', ['date' => $dates[0]]);
            return;
        }

        $choosenDate = $_GET['date'] ?? $dates[0];
        $expenses = $this->userService->getExpensesFullDataForUser($user->id, $choosenDate);
        $categories = $this->userService->getCategoriesForUser($user->id);

        echo $this->render('User/expenses.twig', [
            'categories' => $categories,
            'expenses' => $expenses,
            'dates' => $dates,
        ]);
    }
}
