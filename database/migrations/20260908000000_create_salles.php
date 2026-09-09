<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;

return static function (Capsule $capsule): void {
    $schema = $capsule->schema();

    if ($schema->hasTable('salles')) {
        return;
    }

    $schema->create('salles', static function ($table): void {
        $table->id();
        $table->string('nom', 100);
        $table->string('batiment', 100);
        $table->unsignedInteger('capacite');
        $table->string('type', 30);
        $table->boolean('active')->default(true);
        $table->timestamps();
    });
};
