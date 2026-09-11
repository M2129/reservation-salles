<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Dotenv\Dotenv;

$projectRoot = dirname(__DIR__);

Dotenv::createImmutable($projectRoot)->safeLoad();

$dbStatus = 'inconnue';
$dbError = null;
$autoloadStatus = class_exists(\App\Application::class) ? 'chargé' : 'échec';

try {
    $bootDatabase = require_once $projectRoot . '/config/database.php';
    $capsule = $bootDatabase();

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
        <p>Phase 11 — contrôleurs et vues, sur infrastructure Docker</p>
        <ul>
            <li>Navigateur → Nginx → PHP-FPM → PHP : <strong>OK</strong></li>
            <li>Composer → autoload PSR-4 : <strong><?= htmlspecialchars($autoloadStatus, ENT_QUOTES, 'UTF-8') ?></strong></li>
            <li>
                PHP → MySQL (via Eloquent) :
                <strong><?= htmlspecialchars($dbStatus, ENT_QUOTES, 'UTF-8') ?></strong>
                <?php if ($dbError !== null): ?>
                    <br><small><?= htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8') ?></small>
                <?php endif; ?>
            </li>
        </ul>
    </main>
</body>
</html>
