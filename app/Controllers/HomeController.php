<?php

namespace App\Controllers;

use App\Helpers\Redirect;
use App\Helpers\Session;
use App\Services\ExpenseService;
use App\Services\UserService;

class HomeController extends BaseController
{
    /**
     * HomeController constructor.
    */
    public function __construct(
        private UserService $userService,
        private ExpenseService $expenseService
    ) {
        parent::__construct();
    }

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

        $user = $this->userService->getUserByEmail(Session::get('email'));
        if (!$user) {
            Session::remove('email');
            Redirect::withFlash('User not found')->to('login');
            return;
        }


        $dates = $this->expenseService->getDistinctExpenseDatesForUser($user->id);
        if (count($dates) === 0) {
            $dates = [date('Y-m')];
        }

        if (!isset($_GET['date'])) {
            Redirect::to('dashboard', ['date' => $dates[0]]);
            return;
        }

        $categories = $this->userService->getCategoriesForUser($user->id);
        $items = $this->userService->getExpensesFullDataForUser($user->id, $_GET['date']);
        $labels = array_unique(array_column($items, 'category'));
        $colors = array_unique(array_column($items, 'category_color'));
        // sum prices by category
        $prices = array_reduce($items, function ($carry, $item) {
            $carry[$item['category']] = ($carry[$item['category']] ?? 0) + $item['amount'];
            return $carry;
        }, []);
        $chartJsData = [
          'labels' => array_values($labels),
          'prices' => array_values($prices),
          'colors' => array_values($colors),
        ];

        echo $this->render('Home/dashboard.twig', [
            'categories' => $categories,
            'items' => $items,
            'dates' => $dates,
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
