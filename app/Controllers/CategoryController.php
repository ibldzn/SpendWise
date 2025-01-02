<?php

namespace App\Controllers;

use App\Helpers\Redirect;
use App\Helpers\Session;
use App\Requests\CreateCategoryRequest;
use App\Requests\UpdateCategoryRequest;
use App\Services\CategoryService;
use App\Services\UserService;

class CategoryController extends BaseController
{
    public function __construct(
        private UserService $userService,
        private CategoryService $categoryService
    ) {
        parent::__construct();
    }

    public function addCategory(): void
    {
        try {
            if (!Session::has('email')) {
                Redirect::withFlash('You must be logged in to perform this action')->to('login');
                return;
            }

            $user = $this->userService->getUserByEmail(Session::get('email'));
            if (!$user) {
                Session::remove('email');
                Redirect::withFlash('User not found')->to('login');
                return;
            }

            $userID = $user->id;
            $name = $_POST['name'];
            $color = $_POST['color'];
            $payload = new CreateCategoryRequest(user_id: $userID, name: $name, color: $color);
            $payload->validate();

            $this->categoryService->createCategory($payload);
            Redirect::back();
        } catch (\Exception $e) {
            Redirect::withFlash($e->getMessage())->back();
            return;
        }
    }

    public function removeCategory(string $id): void
    {
        try {
            if (!Session::has('email')) {
                Redirect::withFlash('You must be logged in to perform this action')->to('login');
                return;
            }

            $user = $this->userService->getUserByEmail(Session::get('email'));
            if (!$user) {
                Session::remove('email');
                Redirect::withFlash('User not found')->to('login');
                return;
            }

            $category = $this->categoryService->getCategoryById((int) $id);
            if (!$category) {
                Redirect::withFlash('Category not found')->back();
                return;
            }

            if ($category->user_id !== $user->id) {
                Redirect::withFlash('You are not authorized to perform this action')->back();
                return;
            }

            $this->categoryService->deleteCategory((int) $id);
            Redirect::back();
        } catch (\Exception $e) {
            Redirect::withFlash($e->getMessage())->back();
            return;
        }
    }

    public function updateCategory(): void
    {
        try {
            $categoryID = $_POST['update-id'];
            $category = $this->categoryService->getCategoryById((int) $categoryID);
            if (!$category) {
                Redirect::withFlash('Category not found')->back();
                return;
            }

            $categoryName = $_POST['update-name'];
            $categoryColor = $_POST['update-color'];

            $payload = new UpdateCategoryRequest(
                id: $category->id,
                name: $categoryName,
                color: $categoryColor
            );
            $payload->validate($category);

            if (!$this->categoryService->updateCategory($category->id, $payload)) {
                Redirect::withFlash('Failed to update category')->back();
                return;
            }

            Redirect::withFlash('Category updated')->back();
        } catch (\Exception $e) {
            Redirect::withFlash($e->getMessage())->back();
            return;
        }
    }
}
