<?php

namespace App\Services;

use App\Models\UserModel;
use App\Repositories\UserRepository;
use App\Requests\CreateUserRequest;

class UserService
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    /**
      * Create a new user with encrypted password
      *
      * @param UserModel $user The user to create
      * @return int The ID of the newly created user
    */
    public function createUser(CreateUserRequest $user): int
    {
        $user->password = password_hash($user->password, PASSWORD_DEFAULT);
        return $this->userRepository->create(get_object_vars($user));
    }

    /**
      * Get a user by their ID
      *
      * @param int $id The ID of the user to get
      * @return UserModel|null The user with the given ID, or null if not found
    */
    public function getUserById(int $id): ?UserModel
    {
        $usr = $this->userRepository->select('*')->where(['id' => $id])->limit(1)->fetch();
        if ($usr) {
            return UserModel::constructFromArray($usr);
        }
        return null;
    }

    /**
      * Get a user by their email
      *
      * @param string $email The email of the user to get
      * @return UserModel|null The user with the given email, or null if not found
    */
    public function getUserByEmail(string $email): ?UserModel
    {
        $usr = $this->userRepository->select('*')->where(['email' => $email])->limit(1)->fetch();
        if ($usr) {
            return UserModel::constructFromArray($usr[0]);
        }
        return null;
    }
}
