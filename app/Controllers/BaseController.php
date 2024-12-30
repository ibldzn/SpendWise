<?php

namespace App\Controllers;

use App\Helpers\Session;
use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;

class BaseController
{
    private Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../Views');
        $this->twig = new Environment($loader);

        $this->twig->addExtension(new IntlExtension());
        $this->twig->addGlobal('app_url', $_ENV['APP_URL']);
    }

    /**
     * @param array<mixed,mixed> $data The data to be passed to the view
     */
    protected function render(string|TemplateWrapper $view, array $data = []): void
    {
        $data = array_merge($data, [
            'logged_in' => Session::has('email'),
            'flash' => Session::get('flash') ?? null
        ]);
        echo $this->twig->render($view, $data);
        Session::remove('flash');
    }
}
