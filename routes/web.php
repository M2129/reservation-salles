<?php

declare(strict_types=1);

use App\Controller\HomeController;
use App\Controller\ReservationController;
use App\Controller\SalleController;

return [
    ['GET', '/', [HomeController::class, 'index']],
    ['GET', '/salles', [SalleController::class, 'index']],
    ['GET', '/salles/create', [SalleController::class, 'create']],
    ['POST', '/salles', [SalleController::class, 'store']],
    ['GET', '/salles/{id:[0-9]+}', [SalleController::class, 'show']],
    ['GET', '/salles/{id:[0-9]+}/edit', [SalleController::class, 'edit']],
    ['POST', '/salles/{id:[0-9]+}/edit', [SalleController::class, 'update']],
    ['GET', '/reservations', [ReservationController::class, 'index']],
    ['GET', '/reservations/create', [ReservationController::class, 'create']],
    ['POST', '/reservations', [ReservationController::class, 'store']],
    ['GET', '/reservations/{id:[0-9]+}', [ReservationController::class, 'show']],
    ['POST', '/reservations/{id:[0-9]+}/cancel', [ReservationController::class, 'cancel']],
];

