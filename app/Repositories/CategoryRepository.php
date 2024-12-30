<?php

namespace App\Repositories;

class CategoryRepository extends BaseRepository
{
    private string $table = 'categories';

    /**
     * CategoryRepository constructor.
    */
    public function __construct()
    {
        parent::__construct();
    }
}
