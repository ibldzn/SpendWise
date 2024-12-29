<?php

namespace App\Models;

class ExpenseModel
{
    public function __construct(
        public int $id = 0,
        public int $user_id = 0,
        public float $amount,
        public string $description,
        public string $created_at = '',
        public string $updated_at = '',
    ) {
    }

    /**
     * @param array<string,mixed> $array
     */
    public static function constructFromArray(array $array): ExpenseModel
    {
        return new ExpenseModel(
            id: $array['id'],
            user_id: $array['user_id'],
            amount: $array['amount'],
            description: $array['title'],
            created_at: $array['created_at'] ?? '',
            updated_at: $array['updated_at'] ?? '',
        );
    }
}
