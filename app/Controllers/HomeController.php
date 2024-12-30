<?php

namespace App\Controllers;

use App\Helpers\Redirect;
use App\Helpers\Session;

class HomeController extends BaseController
{
    /**
     * Render the home page
    */
    public function index(): void
    {
        echo $this->render('Home/index.twig');
    }

    /**
     * Render the about page
    */
    public function about(): void
    {
        echo $this->render('Home/about.twig');
    }

    /**
     * Render the dashboard page
    */
    public function dashboard(): void
    {
        if (!Session::has('email')) {
            Redirect::withFlash('You must be logged in to view the dashboard')->to('login');
            return;
        }

        $items = [
            ['name' => 'Eggs', 'category' => 'Groceries', 'price' => 15000],
            ['name' => 'Milk', 'category' => 'Groceries', 'price' => 5000],
            ['name' => 'Fuel', 'category' => 'Auto', 'price' => 50000],
            ['name' => 'Tires', 'category' => 'Auto', 'price' => 200000],
        ];
        $labels = array_unique(array_column($items, 'category'));
        // sum prices by category
        $prices = array_reduce($items, function ($carry, $item) {
            $carry[$item['category']] = ($carry[$item['category']] ?? 0) + $item['price'];
            return $carry;
        }, []);
        $chartJsData = [
          'labels' => array_values($labels),
          'prices' => array_values($prices),
        ];
        // print_r($chartJsData['labels']);
        // exit;

        echo $this->render('Home/dashboard.twig', [
            'items' => $items,
            'chart_js_data' => $chartJsData,
        ]);
    }

    /**
     * Render the 404 page
    */
    public function notFound(): void
    {
        echo $this->render('_404.twig');
    }
}
