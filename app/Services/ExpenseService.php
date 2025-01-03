<?php

namespace App\Services;

use App\Models\ExpenseModel;
use App\Repositories\ExpenseRepository;
use App\Requests\CreateExpenseRequest;
use App\Requests\UpdateExpenseRequest;

class ExpenseService
{
    /**
     * ExpenseService constructor.
    */
    public function __construct(
        private ExpenseRepository $expenseRepository
    ) {
    }

    /**
     * Create a new expense
     *
     * @param CreateExpenseRequest $expense Expense data
     * @return int The ID of the newly created expense
    */
    public function createExpense(CreateExpenseRequest $expense): int
    {
        return $this->expenseRepository->create(get_object_vars($expense));
    }

    public function getExpenseById(int $expenseId): ?ExpenseModel
    {
        $expense = $this->expenseRepository->select('*')->where(['id' => $expenseId])->first();
        if ($expense) {
            return ExpenseModel::constructFromArray($expense);
        }
        return null;
    }

    public function deleteExpense(int $expenseId): bool
    {
        return $this->expenseRepository->delete(['id' => $expenseId]);
    }

    public function updateExpense(UpdateExpenseRequest $expense): bool
    {
        return $this->expenseRepository->update(get_object_vars($expense), ['id' => $expense->id]);
    }

    /**
     * Get all expenses for a user
     *
     * @param int $userId The ID of the user
     * @return array The expenses
    */
    public function getExpensesForUser(int $userId): array
    {
        $expenses = $this->expenseRepository->select('*')->where(['user_id' => $userId])->fetch();
        return array_map(fn ($expense) => ExpenseModel::constructFromArray($expense), $expenses);
    }

    /**
     * Get distinct expense (Year-Month) dates of a user
     *
     * @param int $userId The ID of the user
     * @return array The distinct expense dates
    */
    public function getDistinctExpenseDatesForUser(int $userId): array
    {
        $dates = $this->expenseRepository->select('DISTINCT DATE_FORMAT(date, "%Y-%m") as date')
            ->where(['user_id' => $userId])
            ->orderBy('date', 'DESC')
            ->fetch();
        return array_column($dates, 'date');
    }
}
