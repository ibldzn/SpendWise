<?php

namespace App\Requests;

class LoginRequest
{
    /**
     * LoginRequest constructor.
     *
     * @param string $email
     * @param string $password
     */
    public function __construct(
        public string $email,
        public string $password,
    ) {
    }

    /**
     * Validate the request
     *
     * @throws \Exception If the request is invalid
     */
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
