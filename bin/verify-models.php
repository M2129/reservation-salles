<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Model\Salle;
use App\Model\Reservation;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Dotenv\Dotenv;

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

// Démarre Eloquent (Capsule\Manager) — indispensable avant toute utilisation
// des Models, même juste pour construire une relation.
$bootDatabase = require dirname(__DIR__) . '/config/database.php';
$bootDatabase();

$checks = [
    'Salle existe'              => class_exists(Salle::class),
    'Reservation existe'        => class_exists(Reservation::class),
    'Salle::reservations() OK'  => (new Salle())->reservations() instanceof HasMany,
    'Reservation::salle() OK'   => (new Reservation())->salle() instanceof BelongsTo,
];

foreach ($checks as $label => $result) {
    echo ($result ? '[OK] ' : '[FAIL] ') . $label . PHP_EOL;
}
