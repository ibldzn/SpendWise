<?php

namespace App\Requests;

class CreateExpenseRequest
{
    public function __construct(
        public int $user_id,
        public string $name,
        public float $amount,
        public int $category_id,
        public string $date
    ) {
    }

    public function validate(): void
    {
        if (empty($this->user_id)) {
            throw new \Exception('User ID is required');
        }

        if (empty($this->name)) {
            throw new \Exception('Name is required');
        }

        if (empty($this->amount)) {
            throw new \Exception('Amount is required');
        }

        // check if amount is a number
        if (!is_numeric($this->amount)) {
            throw new \Exception('Amount must be a number');
        }

        if (empty($this->category_id)) {
            throw new \Exception('Category is required');
        }

        if (empty($this->date)) {
            throw new \Exception('Date is required');
        }
    }
}
