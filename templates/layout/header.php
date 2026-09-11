<?php

declare(strict_types=1);

$title = $title ?? 'Réservations universitaires';
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $escape($title) ?></title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<header class="site-header">
    <a href="/">Réservations universitaires</a>
    <nav><a href="/salles">Salles</a><a href="/reservations">Réservations</a></nav>
</header>
<main class="page">
