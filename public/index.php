<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

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
    $host = getenv('DB_HOST') ?: 'mysql';
    $port = getenv('DB_PORT') ?: '3306';
    $database = getenv('DB_DATABASE') ?: 'university_rooms';
    $username = getenv('DB_USERNAME') ?: 'app';
    $password = getenv('DB_PASSWORD') ?: 'app_password';

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $database);

    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 3,
    ]);

    $pdo->query('SELECT 1');
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
        <p>Phase 2 — Composer et autoload PSR-4, sur infrastructure Docker</p>

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
