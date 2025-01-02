<?php

namespace App\Requests;

class CreateCategoryRequest
{
    public function __construct(
        public int $user_id,
        public string $name,
        public string $color
    ) {
    }

    public function validate(): void
    {
        if (empty($this->user_id)) {
            throw new \Exception('User ID is required');
        }
        if (empty($this->name)) {
            throw new \Exception('Name is required');
        }
        if (empty($this->color)) {
            throw new \Exception('Color is required');
        }
    }
}
