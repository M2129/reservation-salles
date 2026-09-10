<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Model\Reservation;
use Dotenv\Dotenv;

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

$bootDatabase = require dirname(__DIR__) . '/config/database.php';
$bootDatabase();

echo "Avant instanciation\n";
$reservation = new Reservation();
echo "Apres instanciation\n";

$relation = $reservation->salle();
echo "Apres appel salle()\n";

var_dump($relation);
