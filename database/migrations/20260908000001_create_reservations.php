<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;

return static function (Capsule $capsule): void {
    $schema = $capsule->schema();

    if ($schema->hasTable('reservations')) {
        return;
    }

    $schema->create('reservations', static function ($table): void {
        $table->id();
        $table->foreignId('salle_id')->constrained('salles')->restrictOnDelete();
        $table->string('responsable', 150);
        $table->string('email', 255);
        $table->string('motif', 255);
        $table->dateTime('date_debut');
        $table->dateTime('date_fin');
        $table->string('statut', 20)->default('confirmee');
        $table->timestamps();

        $table->index(['salle_id', 'statut', 'date_debut', 'date_fin']);
    });
};
