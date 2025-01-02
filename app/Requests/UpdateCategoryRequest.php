<?php

namespace App\Requests;

use App\Models\CategoryModel;

class UpdateCategoryRequest
{
    public function __construct(
        public int $id,
        public string $name,
        public string $color
    ) {
    }

    public function validate(CategoryModel $category): void
    {
        if ($this->id !== $category->id) {
            throw new \Exception('Category ID does not match');
        }
        if (empty($this->name)) {
            throw new \Exception('Name is required');
        }
        if (empty($this->color)) {
            throw new \Exception('Color is required');
        }
    }
}
