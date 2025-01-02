<?php

namespace App\Controllers;

use App\Helpers\Redirect;
use App\Helpers\Session;
use App\Requests\CreateExpenseRequest;
use App\Services\ExpenseService;
use App\Services\UserService;

class ExpenseController extends BaseController
{
    /**
     * ExpenseController constructor.
    */
    public function __construct(
        private UserService $userService,
        private ExpenseService $expenseService,
    ) {
        parent::__construct();
    }

    /**
     * Add a new expense
    */
    public function addExpense(): void
    {
        if (!Session::has('email')) {
            Redirect::withFlash('You must be logged in to access that page')->to('login');
            return;
        }

        $user = $this->userService->getUserByEmail(Session::get('email'));
        if (!$user) {
            Session::remove('email');
            Redirect::withFlash('User not found')->to('login');
            return;
        }

        try {
            $name = $_POST['name'];
            $amount = $_POST['amount'];
            $categoryID = $_POST['category'];
            $date = $_POST['date'];

            $payload = new CreateExpenseRequest(
                user_id: $user->id,
                name: $name,
                amount: $amount,
                category_id: $categoryID,
                date: $date
            );
            $payload->validate();

            $this->expenseService->createExpense($payload);

            Redirect::back();
        } catch (\Exception $e) {
            Redirect::withFlash($e->getMessage())->back();
            return;
        }
    }

    public function deleteExpense(): void
    {
        if (!Session::has('email')) {
            Redirect::withFlash('You must be logged in to access that page')->to('login');
            return;
        }

        $user = $this->userService->getUserByEmail(Session::get('email'));
        if (!$user) {
            Session::remove('email');
            Redirect::withFlash('User not found')->to('login');
            return;
        }

        $expenseID = $_POST['expense-id'];
        $expense = $this->expenseService->getExpenseById($expenseID);
        if (!$expense) {
            Redirect::withFlash('Expense not found')->back();
            return;
        }

        if ($expense->user_id !== $user->id) {
            Redirect::withFlash('You do not have permission to delete this expense')->back();
            return;
        }

        $this->expenseService->deleteExpense($expenseID);
        Redirect::back();
    }
}
