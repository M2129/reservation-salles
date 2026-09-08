<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;

$projectRoot = dirname(__DIR__);
Dotenv::createImmutable($projectRoot)->safeLoad();

$capsule = new Capsule();
$capsule->addConnection(require $projectRoot . '/config/database.php');
$capsule->setAsGlobal();
$capsule->bootEloquent();

/**
 * PHASE 1 — vérification de l'infrastructure Docker.
 *
 * Ce fichier restera le point d'entrée unique de l'application
 * (public/index.php), mais son contenu sera progressivement remplacé
 * par le bootstrap réel : chargement de l'environnement, construction
 * du container PHP-DI, récupération de App\Application, puis
 * dispatch FastRoute (voir Phases 2 à 13).
 *
 * Pour l'instant, il se contente de :
 *   1. afficher que la chaîne Navigateur → Nginx → PHP-FPM → PHP fonctionne ;
 *   2. vérifier que PHP → MySQL fonctionne (connexion PDO simple).
 *
 * Les identifiants DB_* sont injectés comme variables d'environnement
 * du conteneur "app" par docker-compose.yml (elles-mêmes lues depuis .env).
 */

$dbStatus = 'inconnue';
$dbError = null;
$autoloadStatus = class_exists(\App\Application::class) ? 'chargé' : 'échec';

try {
    $capsule->getConnection()->select('SELECT 1');
    $dbStatus = 'connectée';
} catch (Throwable $e) {
    $dbStatus = 'échec';
    $dbError = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des réservations de salles universitaires</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <main>
        <h1>Application Gestion des réservations de salles universitaires</h1>
        <p>Phase 3 — Eloquent et connexion MySQL, sur infrastructure Docker</p>

        <ul>
            <li>Navigateur → Nginx → PHP-FPM → PHP : <strong>OK</strong></li>
            <li>Composer → autoload PSR-4 : <strong><?= htmlspecialchars($autoloadStatus, ENT_QUOTES, 'UTF-8') ?></strong></li>
            <li>
                PHP → MySQL :
                <strong><?= htmlspecialchars($dbStatus, ENT_QUOTES, 'UTF-8') ?></strong>
                <?php if ($dbError !== null): ?>
                    <br><small><?= htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8') ?></small>
                <?php endif; ?>
            </li>
        </ul>
    </main>
</body>
</html>
