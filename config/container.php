<?php

declare(strict_types=1);

use App\Application;
use App\Controller\HomeController;
use App\Controller\ReservationController;
use App\Controller\SalleController;
use App\Repository\EloquentReservationRepository;
use App\Repository\EloquentSalleRepository;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use App\Validation\ReservationValidator;
use App\Validation\SalleValidator;
use App\View\ViewRenderer;
use DI\ContainerBuilder;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Illuminate\Database\Capsule\Manager as Capsule;
use Psr\Container\ContainerInterface;

use function FastRoute\simpleDispatcher;

$projectRoot = dirname(__DIR__);

$routes = require_once $projectRoot . '/routes/web.php';

$builder = new ContainerBuilder();
$builder->addDefinitions([
    Capsule::class => static function (): Capsule {
        $bootDatabase = require dirname(__DIR__) . '/config/database.php';

        return $bootDatabase();
    },
    SalleRepositoryInterface::class => static function (ContainerInterface $container): SalleRepositoryInterface {
        return $container->get(EloquentSalleRepository::class);
    },
    ReservationRepositoryInterface::class => static function (ContainerInterface $container): ReservationRepositoryInterface {
        return $container->get(EloquentReservationRepository::class);
    },
    ViewRenderer::class => static fn (): ViewRenderer => new ViewRenderer($projectRoot . '/templates'),
    Dispatcher::class => static function () use ($routes): Dispatcher {
        return simpleDispatcher(static function (RouteCollector $collector) use ($routes): void {
            foreach ($routes as [$method, $path, $handler]) {
                $collector->addRoute($method, $path, $handler);
            }
        });
    },
    Application::class => static function (ContainerInterface $container): Application {
        return new Application(
            $container->get(Dispatcher::class),
            $container->get(ViewRenderer::class),
            static fn (string $controllerClass): object => $container->get($controllerClass),
        );
    },
    HomeController::class => DI\autowire(),
    SalleController::class => DI\autowire(),
    ReservationController::class => DI\autowire(),
    CreerReservationService::class => DI\autowire(),
    AnnulerReservationService::class => DI\autowire(),
    SalleValidator::class => DI\create(),
    ReservationValidator::class => DI\create(),
]);

return $builder->build();
