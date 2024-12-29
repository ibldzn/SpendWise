<?php

namespace App\Repositories;

use App\Models\UserModel;

class UserRepository extends BaseRepository
{
    protected string $table = 'users';

    public function __construct()
    {
        parent::__construct();
    }

    public function findByPk(int $id): UserModel
    {
        return UserModel::constructFromArray(
            $this->select('*')->where(['id' => $id])->limit(1)->fetch()
        );
    }
}
