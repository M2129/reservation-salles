<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;

return static function (): Capsule {
    $capsule = new Capsule();

    $capsule->addConnection([
        'driver'    => getenv('DB_CONNECTION') ?: 'mysql',
        'host'      => getenv('DB_HOST') ?: 'mysql',
        'port'      => getenv('DB_PORT') ?: '3306',
        'database'  => getenv('DB_DATABASE') ?: 'university_rooms',
        'username'  => getenv('DB_USERNAME') ?: 'app',
        'password'  => getenv('DB_PASSWORD') ?: 'app_password',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix'    => '',
    ]);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};
