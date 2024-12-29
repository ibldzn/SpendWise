<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    public function index(): void
    {
        echo $this->render('Home/index.twig');
    }

    public function notFound(): void
    {
        echo $this->render('_404.twig');
    }
}
