<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Model\Salle;
use Dotenv\Dotenv;

$projectRoot = dirname(__DIR__);
Dotenv::createImmutable($projectRoot)->safeLoad();

$bootDatabase = require_once $projectRoot . '/config/database.php';
$bootDatabase();

$salles = [
    [
        'nom' => 'Amphithéâtre A',
        'batiment' => 'Bâtiment principal',
        'capacite' => 250,
        'type' => 'amphitheatre',
    ],
    [
        'nom' => 'Salle B12',
        'batiment' => 'Bâtiment B',
        'capacite' => 40,
        'type' => 'cours',
    ],
    [
        'nom' => 'Laboratoire Chimie',
        'batiment' => 'Bâtiment scientifique',
        'capacite' => 24,
        'type' => 'laboratoire',
    ],
    [
        'nom' => 'Salle Informatique 1',
        'batiment' => 'Bâtiment informatique',
        'capacite' => 30,
        'type' => 'informatique',
    ],
    [
        'nom' => 'Salle de réunion',
        'batiment' => 'Bâtiment administratif',
        'capacite' => 12,
        'type' => 'reunion',
    ],
];

foreach ($salles as $salleData) {
    $salle = Salle::query()->updateOrCreate(
        [
            'nom' => $salleData['nom'],
            'batiment' => $salleData['batiment'],
        ],
        [
            'capacite' => $salleData['capacite'],
            'type' => $salleData['type'],
            'active' => true,
        ],
    );

    printf("[OK] Salle disponible : %s (ID %d)\n", $salle->nom, $salle->id);
}

echo 'Seed terminé.' . PHP_EOL;
