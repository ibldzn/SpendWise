<?php

namespace App\Requests;

class CreateUserRequest
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        private string $confirm_password,
    ) {
    }

    public function validate(): void
    {
        if (empty($this->name)) {
            throw new \Exception('Name is required');
        }

        if (empty($this->email)) {
            throw new \Exception('Email is required');
        }

        if (empty($this->password)) {
            throw new \Exception('Password is required');
        }

        if ($this->password !== $this->confirm_password) {
            throw new \Exception('Passwords do not match');
        }
    }
}
