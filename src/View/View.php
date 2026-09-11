<?php

declare(strict_types=1);

namespace App\View;

final class View
{
    public function __construct(
        private readonly string $templatesPath,
    ) {
    }

    public function render(string $template, array $data = []): string
    {
        $chemin = $this->templatesPath . '/' . $template . '.php';

        extract($data, EXTR_SKIP);
        ob_start();
        require_once $chemin;

        return (string) ob_get_clean();
    }

    public function renderWithLayout(string $template, array $data = [], string $title = ''): string
    {
        $content = $this->render($template, $data);

        return $this->render('layout/base', [
            'content' => $content,
            'title'   => $title,
        ]);
    }
}
