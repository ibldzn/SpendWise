<?php

namespace App\Models;

class UserModel extends BaseModel
{
    public function __construct(
        public int $id = 0,
        public string $name,
        public string $email,
        public string $password,
        public string $created_at = '',
        public string $updated_at = '',
    ) {
    }

    public static function constructFromArray(array $arr): UserModel
    {
        return new UserModel(
            id: $arr['id'],
            name: $arr['name'],
            email: $arr['email'],
            password: $arr['password'],
            created_at: $arr['created_at'] ?? '',
            updated_at: $arr['updated_at'] ?? '',
        );
    }
}
