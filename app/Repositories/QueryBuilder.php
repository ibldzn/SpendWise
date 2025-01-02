<?php

namespace App\Repositories;

class QueryBuilder
{
    private string $query;

    public function __construct(
        private string $tableName
    ) {
    }
}
