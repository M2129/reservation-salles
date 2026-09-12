<?php

declare(strict_types=1);

namespace App\Controller;

use App\View\ViewRenderer;

final class HomeController
{
    public function __construct(private readonly ViewRenderer $view)
    {
    }

    public function index(): string
    {
        return $this->view->render('home/index');
    }
}

