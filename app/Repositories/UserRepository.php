<?php

namespace App\Repositories;

class UserRepository extends BaseRepository
{
    protected string $table = 'users';

    /**
     * UserRepository constructor.
    */
    public function __construct()
    {
        parent::__construct();
    }
}
