<?php require_once dirname(__DIR__) . '/layout/header.php'; ?>
<section class="heading-row"><div><p class="eyebrow">Planning</p><h1>Réservations</h1></div><a class="button" href="/reservations/create">Nouvelle réservation</a></section>
<form class="toolbar" method="get" action="/reservations"><label for="reservation-status">Statut</label><select id="reservation-status" name="statut"><option value="">Tous les statuts</option><option value="confirmee" <?= ($filters['statut'] ?? '') === 'confirmee' ? 'selected' : '' ?>>Confirmées</option><option value="annulee" <?= ($filters['statut'] ?? '') === 'annulee' ? 'selected' : '' ?>>Annulées</option></select><button type="submit">Filtrer</button></form>
<div class="list">
<?php foreach ($reservations as $reservation): ?>
    <article class="item"><div><h2><a href="/reservations/<?= $reservation->id ?>"><?= $escape($reservation->motif) ?></a></h2><p><?= $escape($reservation->salle?->nom ?? 'Salle inconnue') ?> · <?= $escape($reservation->responsable) ?></p><p><?= $reservation->date_debut?->format('d/m/Y H:i') ?> → <?= $reservation->date_fin?->format('H:i') ?></p></div><span class="status"><?= $escape($reservation->statut) ?></span></article>
<?php endforeach; ?>
</div>
<?php require_once dirname(__DIR__) . '/layout/footer.php'; ?>