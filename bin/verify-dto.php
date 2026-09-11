<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\DTO\CreerSalleDTO;
use App\DTO\CreerReservationDTO;

function check(string $label, bool $condition): void
{
    echo ($condition ? '[OK] ' : '[FAIL] ') . $label . PHP_EOL;
}

$salleDto = CreerSalleDTO::fromArray([
    'nom' => 'Salle B12', 'batiment' => 'Bâtiment B', 'capacite' => '40',
    'type' => 'cours', 'active' => '1',
]);
check('CreerSalleDTO::capacite est un int', is_int($salleDto->capacite) && $salleDto->capacite === 40);
check('CreerSalleDTO::active est un bool', $salleDto->active === true);

$reservationDto = CreerReservationDTO::fromArray([
    'salle_id' => '2', 'responsable' => 'Awa Ndiaye', 'email' => 'awa.ndiaye@universite.sn',
    'motif' => "Cours d'architecture logicielle",
    'date_debut' => '2026-09-10 10:00', 'date_fin' => '2026-09-10 12:00',
]);
check('CreerReservationDTO::salleId est un int', $reservationDto->salleId === 2);
check('CreerReservationDTO::dateDebut est un DateTimeImmutable', $reservationDto->dateDebut instanceof DateTimeImmutable);
check('CreerReservationDTO::dateDebut < dateFin', $reservationDto->dateDebut < $reservationDto->dateFin);
