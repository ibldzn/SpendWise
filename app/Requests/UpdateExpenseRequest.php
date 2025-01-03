<?php

namespace App\Requests;

use App\Models\ExpenseModel;

class UpdateExpenseRequest
{
    public function __construct(
        public int $id,
        public string $name,
        public float $amount,
        public int $category_id,
        public string $date
    ) {
    }

    public function validate(ExpenseModel $expense): void
    {
        if ($this->id !== $expense->id) {
            throw new \Exception('Expense ID does not match');
        }

        if (empty($this->name)) {
            throw new \Exception('Name is required');
        }

        if (empty($this->amount)) {
            throw new \Exception('Amount is required');
        }

        if (empty($this->category_id)) {
            throw new \Exception('Category is required');
        }

        if (empty($this->date)) {
            throw new \Exception('Date is required');
        }
    }
}
