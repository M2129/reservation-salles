<?php

declare(strict_types=1);

namespace App\View;

use App\Exception\ViewNotFoundException;

final class ViewRenderer
{
    public function __construct(private readonly string $templatesPath)
    {
    }

    /** @param array<string, mixed> $data */
    public function render(string $template, array $data = []): string
    {
        $path = $this->templatesPath . '/' . ltrim($template, '/') . '.php';

        if (!is_file($path)) {
            throw new ViewNotFoundException(sprintf('Vue introuvable : %s', $template));
        }

        $escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

        extract($data, EXTR_SKIP);
        ob_start();
        include_once $path;

        return (string) ob_get_clean();
    }
}
