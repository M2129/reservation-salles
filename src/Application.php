<?php

declare(strict_types=1);

namespace App;

final class Application
{
    public function phase(): string
    {
        return 'Phase 2 — Composer et autoload PSR-4';
    }
}
