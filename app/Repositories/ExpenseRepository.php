<?php

namespace App\Repositories;

class ExpenseRepository extends BaseRepository
{
    protected string $table = 'expenses';

    public function __construct()
    {
        parent::__construct();
    }
}
