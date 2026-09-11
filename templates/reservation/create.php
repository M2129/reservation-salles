<?php require_once dirname(__DIR__) . '/layout/header.php'; ?>
<h1>Nouvelle réservation</h1>
<?php if ($errors !== []): ?><div class="alert"><ul><?php foreach ($errors as $messages): foreach ($messages as $message): ?><li><?= $escape($message) ?></li><?php endforeach; endforeach; ?></ul></div><?php endif; ?>
<form class="form" method="post" action="/reservations">
    <label>Salle <select name="salle_id" required><option value="">Choisir une salle</option><?php foreach ($salles as $salle): ?><option value="<?= $salle->id ?>" <?= (string) ($old['salle_id'] ?? '') === (string) $salle->id ? 'selected' : '' ?>><?= $escape($salle->nom) ?> (<?= $salle->capacite ?> places)</option><?php endforeach; ?></select></label>
    <label>Responsable <input name="responsable" value="<?= $escape($old['responsable'] ?? '') ?>" required></label>
    <label>Email <input type="email" name="email" value="<?= $escape($old['email'] ?? '') ?>" required></label>
    <label>Motif <textarea name="motif" rows="3" required><?= $escape($old['motif'] ?? '') ?></textarea></label>
    <label>Début <input type="datetime-local" name="date_debut" value="<?= $escape($old['date_debut'] ?? '') ?>" required></label>
    <label>Fin <input type="datetime-local" name="date_fin" value="<?= $escape($old['date_fin'] ?? '') ?>" required></label>
    <button class="button" type="submit">Réserver</button>
</form>
<?php require_once dirname(__DIR__) . '/layout/footer.php'; ?>