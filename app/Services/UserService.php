<?php

namespace App\Services;

use App\Models\CategoryModel;
use App\Models\ExpenseModel;
use App\Models\UserModel;
use App\Repositories\CategoryRepository;
use App\Repositories\ExpenseRepository;
use App\Repositories\UserRepository;
use App\Requests\CreateUserRequest;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private ExpenseRepository $expenseRepository,
        private CategoryRepository $categoryRepository
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

    /**
     * Get all users
     *
     * @return array<UserModel> All users
    */
    public function getAllUsers(): array
    {
        $users = $this->userRepository->select('*')->fetch();
        return array_map(fn ($user) => UserModel::constructFromArray($user), $users);
    }

    /**
     * Update a user
     *
     * @param UserModel $user The user to update
     * @return bool True if the user was updated, false otherwise
    */
    public function updateUser(UserModel $user): bool
    {
        return $this->userRepository->update(get_object_vars($user), ['id' => $user->id]);
    }

    /**
     * Delete a user
     *
     * @param UserModel $user The user to delete
     * @return bool True if the user was deleted, false otherwise
    */
    public function deleteUser(UserModel $user): bool
    {
        return $this->userRepository->delete(['id' => $user->id]);
    }

    /**
     * Delete a user by their ID
     *
     * @param int $id The ID of the user to delete
     * @return bool True if the user was deleted, false otherwise
    */
    public function deleteUserById(int $id): bool
    {
        return $this->userRepository->delete(['id' => $id]);
    }

    /**
     * Get all expenses for a given user ID
     *
     * @param int $id The ID of the user to get expenses for
     * @return array<ExpenseModel> All expenses for the given user
    */
    public function getExpensesForUser(int $id): array
    {
        $expenses = $this->expenseRepository->select('*')->where(['user_id' => $id])->fetch();
        return array_map(fn ($expense) => ExpenseModel::constructFromArray($expense), $expenses);
    }

    /**
     * Get all the user created categories for a given user ID
     *
     * @param int $id The ID of the user to get categories for
     * @return array<CategoryModel> All categories for the given user
    */
    public function getCategoriesForUser(int $id): array
    {
        $categories = $this->categoryRepository->select('*')->where(['user_id' => $id])->fetch();
        return array_map(fn ($category) => CategoryModel::constructFromArray($category), $categories);
    }

    /**
     * Get all the complete data of user expenses
     *
     * @param int $id The ID of the user to get expenses for
     * @return array All expenses for the given user
    */
    public function getExpensesFullDataForUser(int $userID, string $date = null): array
    {
        $expenses = $this
            ->expenseRepository
            ->select('expenses.*, categories.name as category, categories.color as category_color')
            ->join('categories', 'categories.id = expenses.category_id')
            ->where([
                'expenses.user_id' => $userID,
            ])
            ->fetch();
        // HACK: This is a hack to filter expenses by date
        if ($date) {
            $expenses = array_filter($expenses, fn ($expense) => strpos($expense['date'], $date) === 0);
        }
        return $expenses;
    }
}
