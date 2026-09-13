<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

http_response_code(200);

use App\Application;
use App\Repository\EloquentReservationRepository;
use App\Repository\EloquentSalleRepository;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;

function check(string $label, bool $condition): void
{
    echo ($condition ? '[OK] ' : '[FAIL] ') . $label . PHP_EOL;
}

$projectRoot = dirname(__DIR__);
Dotenv::createImmutable($projectRoot)->safeLoad();

$container = require $projectRoot . '/config/container.php';

check(
    'SalleRepositoryInterface -> EloquentSalleRepository',
    $container->get(SalleRepositoryInterface::class) instanceof EloquentSalleRepository
);
check(
    'ReservationRepositoryInterface -> EloquentReservationRepository',
    $container->get(ReservationRepositoryInterface::class) instanceof EloquentReservationRepository
);

$capsule1 = $container->get(Capsule::class);
$capsule2 = $container->get(Capsule::class);
check('Capsule::class est un singleton (même instance à chaque get())', $capsule1 === $capsule2);

$application = $container->get(Application::class);
check('Application se résout depuis le container', $application instanceof Application);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/salles';

ob_start();
$application->run();
$corps = (string) ob_get_clean();

check('GET /salles via Application (container) -> 200', http_response_code() === 200);
check('GET /salles via Application (container) -> contient "Salle B12"', str_contains($corps, 'Salle B12'));
