<?php

namespace App\Requests;

class LoginRequest
{
    public function __construct(
        public string $email,
        public string $password,
    ) {
    }

    public function validate(): void
    {
        if (empty($this->email)) {
            throw new \Exception('Email is required');
        }
        if (empty($this->password)) {
            throw new \Exception('Password is required');
        }
    }
}
