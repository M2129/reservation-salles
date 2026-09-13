<?php

declare(strict_types=1);

use App\Application;
use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;

require dirname(__DIR__) . '/vendor/autoload.php';

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

$container = require dirname(__DIR__) . '/config/container.php';

$container->get(Capsule::class);

$application = $container->get(Application::class);
$application->run();
