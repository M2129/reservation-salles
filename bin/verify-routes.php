<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Application;
use App\Controller\HomeController;
use App\Controller\ReservationController;
use App\Controller\SalleController;
use App\Exception\ControllerNotFoundException;
use App\Repository\EloquentReservationRepository;
use App\Repository\EloquentSalleRepository;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use App\Validation\ReservationValidator;
use App\Validation\SalleValidator;
use App\View\ViewRenderer;
use Dotenv\Dotenv;
use FastRoute\RouteCollector;

use function FastRoute\simpleDispatcher;

$projectRoot = dirname(__DIR__);
Dotenv::createImmutable($projectRoot)->safeLoad();
$bootDatabase = require_once $projectRoot . '/config/database.php';
$bootDatabase();
ob_start();

function check(string $label, bool $condition): void
{
    echo ($condition ? '[OK] ' : '[FAIL] ') . $label . PHP_EOL;
}

$salleRepository = new EloquentSalleRepository();
$reservationRepository = new EloquentReservationRepository();
$view = new ViewRenderer($projectRoot . '/templates');

$resolveController = static function (string $classe) use ($salleRepository, $reservationRepository, $view): object {
    return match ($classe) {
        HomeController::class => new HomeController($view),
        SalleController::class => new SalleController($salleRepository, new SalleValidator(), $view),
        ReservationController::class => new ReservationController(
            $reservationRepository,
            $salleRepository,
            new ReservationValidator(),
            new CreerReservationService($salleRepository, $reservationRepository),
            new AnnulerReservationService($reservationRepository),
            $view,
        ),
        default => throw new ControllerNotFoundException("Contrôleur inconnu : {$classe}"),
    };
};

$routes = require_once $projectRoot . '/routes/web.php';
$dispatcher = simpleDispatcher(static function (RouteCollector $r) use ($routes): void {
    foreach ($routes as [$methode, $chemin, $gestionnaire]) {
        $r->addRoute($methode, $chemin, $gestionnaire);
    }
});

function requete(App\Application $application, string $methode, string $uri): array
{
    $_SERVER['REQUEST_METHOD'] = $methode;
    $_SERVER['REQUEST_URI'] = $uri;
    $_GET = [];
    $_POST = [];
    http_response_code(200);

    ob_start();

    $application->run();

    $corps = (string) ob_get_clean();
    $code = http_response_code();

    return [$code, $corps];
}

$application = new Application($dispatcher, $view, $resolveController);

$salleB12 = null;
foreach ($salleRepository->all() as $salle) {
    if ($salle->nom === 'Salle B12') {
        $salleB12 = $salle;
        break;
    }
}
check('Salle B12 présente (seed)', $salleB12 !== null);

[$code, $corps] = requete($application, 'GET', '/');
check('GET / -> 200', $code === 200);
check('GET / -> contient le titre du projet', str_contains($corps, 'Gestion des réservations'));

[$code, $corps] = requete($application, 'GET', '/salles');
check('GET /salles -> 200', $code === 200);
check('GET /salles -> liste "Salle B12"', str_contains($corps, 'Salle B12'));

if ($salleB12 !== null) {
    [$code, $corps] = requete($application, 'GET', '/salles/' . $salleB12->id);
    check('GET /salles/{id} valide -> 200', $code === 200);
    check('GET /salles/{id} valide -> affiche la salle', str_contains($corps, 'B12'));
}

[$code] = requete($application, 'GET', '/salles/999999');
check('GET /salles/999999 -> 404 (SalleIndisponibleException interceptée)', $code === 404);

[$code, $corps] = requete($application, 'GET', '/inconnue');
check('Scénario 7 : GET /inconnue -> 404', $code === 404);
check('Scénario 7 : page 404 affichée', str_contains($corps, '<h1>404</h1>'));

[$code, $corps] = requete($application, 'DELETE', '/salles');
check('Scénario 8 : DELETE /salles -> 405', $code === 405);
check('Scénario 8 : page 405 affichée', str_contains($corps, '405'));

[$code, $corps] = requete($application, 'GET', '/reservations');
check('GET /reservations -> 200', $code === 200);

[$code, $corps] = requete($application, 'GET', '/reservations/create');
check('GET /reservations/create -> 200, liste les salles', $code === 200 && str_contains($corps, 'B12'));
