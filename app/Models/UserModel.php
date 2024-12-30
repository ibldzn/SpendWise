<?php

namespace App\Models;

class UserModel extends BaseModel
{
    /**
     * UserModel constructor.
     * @param int $id ID of the user
     * @param string $name Name of the user
     * @param string $email Email of the user
     * @param string $password Password of the user
     * @param string $created_at Date and time the user was created
     * @param string $updated_at Date and time the user was last updated
     */
    public function __construct(
        public int $id = 0,
        public string $name = '',
        public string $email = '',
        public string $password = '',
        public string $created_at = '',
        public string $updated_at = '',
    ) {
    }

    /**
     * Construct a UserModel object from an array
     *
     * @param array<string,mixed> $arr The array to construct the object from
     * @return UserModel The object constructed from the array
     */
    public static function constructFromArray(array $array): UserModel
    {
        return new UserModel(
            id: $array['id'],
            name: $array['name'],
            email: $array['email'],
            password: $array['password'],
            created_at: $array['created_at'] ?? '',
            updated_at: $array['updated_at'] ?? '',
        );
    }
}
