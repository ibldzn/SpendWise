<?php

namespace App\Requests;

use App\Models\UserModel;

class UpdateUserRequest
{
    /**
     * UpdateUserRequest constructor.
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @param string $confirm_password
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public ?string $current_password,
        public ?string $new_password,
        private ?string $confirm_password,
    ) {
    }

    /**
     * Validate the request
     *
     * @throws \Exception If the request is invalid
     */
    public function validate(UserModel $user): void
    {
        if ($this->id !== $user->id) {
            throw new \Exception('User ID does not match');
        }

        if (empty($this->name)) {
            throw new \Exception('Name is required');
        }

        if (empty($this->email)) {
            throw new \Exception('Email is required');
        }

        if (isset($this->current_password) && isset($this->new_password)) {
            if (!password_verify($this->current_password, $user->password)) {
                throw new \Exception('Current password is incorrect');
            }

            if ($this->new_password !== $this->confirm_password) {
                throw new \Exception('Passwords do not match');
            }
        }
    }
}
