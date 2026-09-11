<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Validation\SalleValidator;
use App\Validation\ReservationValidator;

function check(string $label, bool $condition): void
{
    echo ($condition ? '[OK] ' : '[FAIL] ') . $label . PHP_EOL;
}

$salleValidator = new SalleValidator();

$resultat = $salleValidator->validate([
    'nom' => 'Salle B12', 'batiment' => 'Bâtiment B', 'capacite' => 40,
    'type' => 'cours', 'active' => true,
]);
check('Salle valide -> isValid()', $resultat->isValid());

$resultat = $salleValidator->validate([
    'nom' => 'Salle B12', 'batiment' => 'Bâtiment B', 'capacite' => -5,
    'type' => 'cours', 'active' => true,
]);
check('Capacité négative -> erreur sur "capacite"', !$resultat->isValid() && $resultat->errorsFor('capacite') !== []);

$resultat = $salleValidator->validate([
    'nom' => 'Salle B12', 'batiment' => 'Bâtiment B', 'capacite' => 40,
    'type' => 'sous-sol-secret', 'active' => true,
]);
check('Type inconnu -> erreur sur "type"', !$resultat->isValid() && $resultat->errorsFor('type') !== []);

$reservationValidator = new ReservationValidator();

$resultat = $reservationValidator->validate([
    'salle_id' => 2, 'responsable' => 'Awa Ndiaye', 'email' => 'awa.ndiaye@universite.sn',
    'motif' => "Cours d'architecture logicielle", 'date_debut' => '2026-09-10 10:00',
    'date_fin' => '2026-09-10 12:00',
]);
check('Réservation valide -> isValid()', $resultat->isValid());

$resultat = $reservationValidator->validate([
    'salle_id' => 2, 'responsable' => '', 'email' => 'adresse-invalide',
    'motif' => 'TP', 'date_debut' => 'pas-une-date', 'date_fin' => '2026-09-10 12:00',
]);
check('Responsable vide -> erreur', $resultat->errorsFor('responsable') !== []);
check('Email invalide -> erreur', $resultat->errorsFor('email') !== []);
check('Date invalide -> erreur', $resultat->errorsFor('date_debut') !== []);
check('Formulaire multi-erreurs -> plusieurs champs en erreur', count($resultat->errors()) >= 3);
check('Valeurs saisies conservées -> data()', $resultat->data()['email'] === 'adresse-invalide');
