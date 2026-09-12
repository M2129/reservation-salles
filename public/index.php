<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Application;
use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;

$projectRoot = dirname(__DIR__);

Dotenv::createImmutable($projectRoot)->safeLoad();
$container = require_once $projectRoot . '/config/container.php';

if (PHP_SAPI !== 'cli') {
    $container->get(Application::class)->run();
    exit;
}

$dbStatus = 'inconnue';
$dbError = null;
$autoloadStatus = class_exists(\App\Application::class) ? 'chargé' : 'échec';

try {
    $capsule = $container->get(Capsule::class);
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
        <p>Phase 12 — FastRoute et dispatch HTTP, sur infrastructure Docker</p>
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
