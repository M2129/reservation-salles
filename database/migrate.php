<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

$bootDatabase = require dirname(__DIR__) . '/config/database.php';
/** @var Capsule $capsule */
$capsule = $bootDatabase();

$files = glob(__DIR__ . '/migrations/*.php');
sort($files);

if ($files === false || $files === []) {
    echo "Aucune migration trouvée dans database/migrations/." . PHP_EOL;
    exit(0);
}

foreach ($files as $file) {
    $name = basename($file);

    $migration = require $file;

    if (!is_callable($migration)) {
        echo "[ERREUR] {$name} : ne retourne pas une closure exécutable." . PHP_EOL;
        exit(1);
    }

    $migration($capsule);

    echo "[OK] {$name} exécutée (ou déjà à jour)." . PHP_EOL;
}

echo 'Migrations terminées.' . PHP_EOL;
