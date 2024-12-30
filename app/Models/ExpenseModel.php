<?php

namespace App\Models;

class ExpenseModel extends BaseModel
{
    /**
     * ExpenseModel constructor.
     * @param int $id ID of the expense
     * @param int $user_id ID of the user who created the expense
     * @param float $amount Amount of the expense
     * @param string $created_at Date and time the expense was created
     * @param string $updated_at Date and time the expense was last updated
    */
    public function __construct(
        public int $id = 0,
        public int $user_id = 0,
        public float $amount = 0.0,
        public string $created_at = '',
        public string $updated_at = '',
    ) {
    }

    /**
     * Construct an ExpenseModel object from an array
     *
     * @param array<string,mixed> $array The array to construct the object from
     * @return ExpenseModel The object constructed from the array
    */
    public static function constructFromArray(array $array): ExpenseModel
    {
        return new ExpenseModel(
            id: $array['id'],
            user_id: $array['user_id'],
            amount: $array['amount'],
            created_at: $array['created_at'] ?? '',
            updated_at: $array['updated_at'] ?? '',
        );
    }
}
