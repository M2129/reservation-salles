<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\DTO\CreerReservationDTO;
use App\Exception\ReservationInvalideException;
use App\Exception\ReservationIntrouvableException;
use App\Exception\SalleIndisponibleException;
use App\Model\Reservation;
use App\Repository\EloquentReservationRepository;
use App\Repository\EloquentSalleRepository;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use Dotenv\Dotenv;

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
$bootDatabase = require_once dirname(__DIR__) . '/config/database.php';
$bootDatabase();

function check(string $label, bool $condition): void
{
    echo ($condition ? '[OK] ' : '[FAIL] ') . $label . PHP_EOL;
}

Reservation::query()->where('motif', 'like', '[TEST]%')->delete();

$salleRepo = new EloquentSalleRepository();
$reservationRepo = new EloquentReservationRepository();
$creerService = new CreerReservationService($salleRepo, $reservationRepo);
$annulerService = new AnnulerReservationService($reservationRepo);

$salleB12 = null;
foreach ($salleRepo->all() as $salle) {
    if ($salle->nom === 'Salle B12') {
        $salleB12 = $salle;
        break;
    }
}

if ($salleB12 === null) {
    echo "[ERREUR] Salle B12 introuvable — lance d'abord database/seed.php." . PHP_EOL;
    exit(1);
}

function faireDto(int $salleId, DateTimeImmutable $debut, DateTimeImmutable $fin, string $motif = '[TEST] service'): CreerReservationDTO
{
    return new CreerReservationDTO(
        salleId: $salleId,
        responsable: 'Awa Ndiaye',
        email: 'awa.ndiaye@universite.sn',
        motif: $motif,
        dateDebut: $debut,
        dateFin: $fin,
    );
}

try {
    $dto = faireDto($salleB12->id, new DateTimeImmutable('+1 day 10:00'), new DateTimeImmutable('+1 day 12:00'), '[TEST] valide');
    $reservation = $creerService->creer($dto);
    check('1. Réservation valide -> confirmée', $reservation->statut === 'confirmee');
} catch (Throwable $e) {
    check('1. Réservation valide -> confirmée (exception inattendue : ' . $e->getMessage() . ')', false);
}

try {
    $dto = faireDto(999999, new DateTimeImmutable('+2 day 10:00'), new DateTimeImmutable('+2 day 12:00'));
    $creerService->creer($dto);
    check('2. Salle inexistante -> exception attendue', false);
} catch (SalleIndisponibleException $e) {
    check('2. Salle inexistante -> SalleIndisponibleException', true);
}

$salleRepo->setActive($salleB12, false);
try {
    $dto = faireDto($salleB12->id, new DateTimeImmutable('+2 day 10:00'), new DateTimeImmutable('+2 day 12:00'));
    $creerService->creer($dto);
    check('3. Salle inactive -> exception attendue', false);
} catch (SalleIndisponibleException $e) {
    check('3. Salle inactive -> SalleIndisponibleException', true);
} finally {
    $salleRepo->setActive($salleB12, true);
}

try {
    $dto = faireDto($salleB12->id, new DateTimeImmutable('+3 day 12:00'), new DateTimeImmutable('+3 day 10:00'));
    $creerService->creer($dto);
    check('4. Fin avant début -> exception attendue', false);
} catch (ReservationInvalideException $e) {
    check('4. Fin avant début -> ReservationInvalideException', true);
}

try {
    $dto = faireDto($salleB12->id, new DateTimeImmutable('+3 day 08:00'), new DateTimeImmutable('+3 day 14:00'));
    $creerService->creer($dto);
    check('5. Durée > 4h -> exception attendue', false);
} catch (ReservationInvalideException $e) {
    check('5. Durée > 4h -> ReservationInvalideException', true);
}

try {
    $dto = faireDto($salleB12->id, new DateTimeImmutable('-1 day 10:00'), new DateTimeImmutable('-1 day 12:00'));
    $creerService->creer($dto);
    check('6. Date passée -> exception attendue', false);
} catch (ReservationInvalideException $e) {
    check('6. Date passée -> ReservationInvalideException', true);
}

try {
    $dto = faireDto($salleB12->id, new DateTimeImmutable('+1 day 11:00'), new DateTimeImmutable('+1 day 13:00'), '[TEST] conflit');
    $creerService->creer($dto);
    check('7. Conflit (11h-13h vs 10h-12h) -> exception attendue', false);
} catch (SalleIndisponibleException $e) {
    check('7. Conflit (11h-13h vs 10h-12h) -> SalleIndisponibleException', true);
}

try {
    $dto = faireDto($salleB12->id, new DateTimeImmutable('+1 day 12:00'), new DateTimeImmutable('+1 day 14:00'), '[TEST] voisine');
    $reservation = $creerService->creer($dto);
    check('8. Réservation voisine (12h-14h) -> acceptée', $reservation->statut === 'confirmee');
} catch (Throwable $e) {
    check('8. Réservation voisine (12h-14h) -> acceptée (exception inattendue : ' . $e->getMessage() . ')', false);
}

try {
    $annulerService->annuler(999999);
    check('9. Annulation id inexistant -> exception attendue', false);
} catch (ReservationIntrouvableException $e) {
    check('9. Annulation id inexistant -> ReservationIntrouvableException', true);
}

$reservationTest = Reservation::query()->where('motif', '[TEST] valide')->first();
if ($reservationTest !== null) {
    $annulee = $annulerService->annuler($reservationTest->id);
    check('10. Annulation réussie -> statut = annulee', $annulee->statut === 'annulee');
} else {
    check('10. Annulation réussie -> statut = annulee', false);
}

Reservation::query()->where('motif', 'like', '[TEST]%')->delete();
echo 'Nettoyage effectué.' . PHP_EOL;
