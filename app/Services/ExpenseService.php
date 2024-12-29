<?php

namespace App\Services;

use App\Repositories\ExpenseRepository;

class ExpenseService
{
    public function __construct(
        private ExpenseRepository $expenseRepository
    ) {
    }
}
