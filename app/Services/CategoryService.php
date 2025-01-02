<?php

namespace App\Services;

use App\Models\CategoryModel;
use App\Repositories\CategoryRepository;
use App\Requests\CreateCategoryRequest;
use App\Requests\UpdateCategoryRequest;

class CategoryService
{
    public function __construct(
        private CategoryRepository $categoryRepository
    ) {
    }

    public function createCategory(CreateCategoryRequest $payload): int
    {
        return $this->categoryRepository->create(get_object_vars($payload));
    }

    public function getCategoryById(int $id): ?CategoryModel
    {
        $category = $this->categoryRepository->select('*')->where(['id' => $id])->first();
        if ($category) {
            return CategoryModel::constructFromArray($category);
        }
        return null;
    }

    public function deleteCategory(int $id): void
    {
        $this->categoryRepository->delete(['id' => $id]);
    }

    public function updateCategory(int $id, UpdateCategoryRequest $payload): bool
    {
        return $this->categoryRepository->update(get_object_vars($payload), ['id' => $id]);
    }
}
