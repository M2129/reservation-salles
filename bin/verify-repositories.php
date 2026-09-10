<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Repository\EloquentSalleRepository;
use App\Repository\EloquentReservationRepository;
use App\Model\Reservation;
use Dotenv\Dotenv;

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
$bootDatabase = require dirname(__DIR__) . '/config/database.php';
$bootDatabase();

function check(string $label, bool $condition): void
{
    echo ($condition ? '[OK] ' : '[FAIL] ') . $label . PHP_EOL;
}

$salleRepo = new EloquentSalleRepository();
$reservationRepo = new EloquentReservationRepository();

$salles = $salleRepo->all();
check('all() retourne au moins 5 salles (seed)', count($salles) >= 5);

$salleB12 = null;
foreach ($salles as $salle) {
    if ($salle->nom === 'Salle B12') {
        $salleB12 = $salle;
        break;
    }
}
check("Salle B12 trouvée via le seed", $salleB12 !== null);

if ($salleB12 !== null) {
    $salleRepo->setActive($salleB12, false);
    $recharge = $salleRepo->findById($salleB12->id);
    check('setActive(false) -> active devient false', $recharge->active === false);

    $salleRepo->setActive($salleB12, true);
    $recharge = $salleRepo->findById($salleB12->id);
    check('setActive(true) -> active redevient true', $recharge->active === true);
}

if ($salleB12 !== null) {
    Reservation::query()->where('motif', '[TEST] verify-repositories')->delete();

    $debut = new DateTimeImmutable('+1 day 10:00');
    $fin = new DateTimeImmutable('+1 day 12:00');

    $reservation = new Reservation([
        'salle_id'    => $salleB12->id,
        'responsable' => 'Test Repository',
        'email'       => 'test@example.com',
        'motif'       => '[TEST] verify-repositories',
        'date_debut'  => $debut,
        'date_fin'    => $fin,
        'statut'      => 'confirmee',
    ]);
    $reservation = $reservationRepo->save($reservation);

    $chevauchement = new DateTimeImmutable('+1 day 11:00');
    $finChevauchement = new DateTimeImmutable('+1 day 13:00');
    $conflits = $reservationRepo->findConflicts($salleB12->id, $chevauchement, $finChevauchement);
    check('findConflicts() détecte le chevauchement 11h-13h', count($conflits) >= 1);

    $adjacentDebut = new DateTimeImmutable('+1 day 12:00');
    $adjacentFin = new DateTimeImmutable('+1 day 14:00');
    $sansConflit = $reservationRepo->findConflicts($salleB12->id, $adjacentDebut, $adjacentFin);
    check('findConflicts() n\'inclut pas un créneau adjacent (12h-14h)', count($sansConflit) === 0);

    $annulee = $reservationRepo->cancel($reservation);
    check("cancel() -> statut devient 'annulee'", $annulee->statut === 'annulee');

    $conflitsApresAnnulation = $reservationRepo->findConflicts($salleB12->id, $chevauchement, $finChevauchement);
    check('Une réservation annulée ne bloque plus la salle', count($conflitsApresAnnulation) === 0);

    Reservation::query()->where('motif', '[TEST] verify-repositories')->delete();
}
