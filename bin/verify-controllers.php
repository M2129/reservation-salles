<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Controller\ReservationController;
use App\Controller\SalleController;
use App\Exception\ReservationIntrouvableException;
use App\Exception\SalleIndisponibleException;
use App\Model\Reservation;
use App\Repository\EloquentReservationRepository;
use App\Repository\EloquentSalleRepository;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use App\Validation\ReservationValidator;
use App\Validation\SalleValidator;
use App\View\ViewRenderer;
use Dotenv\Dotenv;

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
$bootDatabase = require dirname(__DIR__) . '/config/database.php';
$bootDatabase();

function check(string $label, bool $condition): void
{
    echo ($condition ? '[OK] ' : '[FAIL] ') . $label . PHP_EOL;
}

Reservation::query()->where('motif', 'like', '[TEST]%')->delete();

$salleRepo = new EloquentSalleRepository();
$reservationRepo = new EloquentReservationRepository();
$view = new ViewRenderer(dirname(__DIR__) . '/templates');

$salleController = new SalleController($salleRepo, new SalleValidator(), $view);
$reservationController = new ReservationController(
    $reservationRepo,
    $salleRepo,
    new ReservationValidator(),
    new CreerReservationService($salleRepo, $reservationRepo),
    new AnnulerReservationService($reservationRepo),
    $view,
);

$salleB12 = null;
foreach ($salleRepo->all() as $salle) {
    if ($salle->nom === 'Salle B12') {
        $salleB12 = $salle;
        break;
    }
}
check('Salle B12 présente (seed)', $salleB12 !== null);

$html = $salleController->index();
check('SalleController::index() retourne du HTML non vide', trim($html) !== '');

if ($salleB12 !== null) {
    $html = $salleController->show($salleB12->id);
    check('SalleController::show() affiche le nom de la salle', str_contains($html, 'B12'));
}

try {
    $salleController->show(999999);
    check('SalleController::show() id inexistant -> exception attendue', false);
} catch (SalleIndisponibleException $e) {
    check('SalleController::show() id inexistant -> SalleIndisponibleException', true);
}

$avant = \App\Model\Salle::query()->count();
$salleController->store(['nom' => 'X', 'batiment' => '', 'capacite' => '-5', 'type' => 'inconnu']);
$apres = \App\Model\Salle::query()->count();
check('SalleController::store() invalide -> aucune salle créée', $avant === $apres);

$html = $reservationController->create();
check('ReservationController::create() liste les salles', str_contains($html, 'B12'));

if ($salleB12 !== null) {
    $data = [
        'salle_id'    => (string) $salleB12->id,
        'responsable' => 'Awa Ndiaye',
        'email'       => 'awa.ndiaye@universite.sn',
        'motif'       => '[TEST] controller valide',
        'date_debut'  => (new DateTimeImmutable('+5 day 10:00'))->format('Y-m-d H:i:s'),
        'date_fin'    => (new DateTimeImmutable('+5 day 12:00'))->format('Y-m-d H:i:s'),
    ];
    $reservationController->store($data);
    $creee = Reservation::query()->where('motif', '[TEST] controller valide')->first();
    check('ReservationController::store() valide -> réservation créée', $creee !== null && $creee->statut === 'confirmee');
}

if ($salleB12 !== null) {
    $avant = Reservation::query()->count();
    $data = [
        'salle_id'    => (string) $salleB12->id,
        'responsable' => 'Autre Personne',
        'email'       => 'autre@universite.sn',
        'motif'       => '[TEST] controller conflit',
        'date_debut'  => (new DateTimeImmutable('+5 day 11:00'))->format('Y-m-d H:i:s'),
        'date_fin'    => (new DateTimeImmutable('+5 day 13:00'))->format('Y-m-d H:i:s'),
    ];
    $reservationController->store($data);
    $apres = Reservation::query()->count();
    check('ReservationController::store() conflit -> aucune réservation créée', $avant === $apres);
}

$aAnnuler = Reservation::query()->where('motif', '[TEST] controller valide')->first();
if ($aAnnuler !== null) {
    $reservationController->cancel($aAnnuler->id);
    $rechargee = $reservationRepo->findById($aAnnuler->id);
    check('ReservationController::cancel() -> statut = annulee', $rechargee->statut === 'annulee');
}

try {
    $reservationController->cancel(999999);
    check('ReservationController::cancel() id inexistant -> exception attendue', false);
} catch (ReservationIntrouvableException $e) {
    check('ReservationController::cancel() id inexistant -> ReservationIntrouvableException', true);
}

Reservation::query()->where('motif', 'like', '[TEST]%')->delete();
echo 'Nettoyage effectué.' . PHP_EOL;
