<?php

namespace App\Models;

class CategoryModel
{
    /**
     * CategoryModel constructor.
     * @param int $id ID of the category
     * @param int $user_id ID of the user who created the category
     * @param string $name Name of the category
     * @param string $created_at Date and time the category was created
     * @param string $updated_at Date and time the category was last updated
    */
    public function __construct(
        public int $id = 0,
        public int $user_id = 0,
        public string $name = '',
        public string $created_at = '',
        public string $updated_at = '',
    ) {
    }

    /**
     * Construct a CategoryModel object from an array
     *
     * @param array<string,mixed> $array The array to construct the object from
     * @return CategoryModel The object constructed from the array
    */
    public static function constructFromArray(array $array): CategoryModel
    {
        return new CategoryModel(
            id: $array['id'],
            user_id: $array['user_id'],
            name: $array['name'],
            created_at: $array['created_at'] ?? '',
            updated_at: $array['updated_at'] ?? '',
        );
    }
}
