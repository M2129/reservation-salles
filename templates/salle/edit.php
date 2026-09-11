<?php require_once dirname(__DIR__) . '/layout/header.php'; ?>
<h1>Modifier une salle</h1>
<?php if ($errors !== []): ?><div class="alert"><ul><?php foreach ($errors as $messages): foreach ($messages as $message): ?><li><?= $escape($message) ?></li><?php endforeach; endforeach; ?></ul></div><?php endif; ?>
<?php $values = $old ?? ['nom' => $salle->nom, 'batiment' => $salle->batiment, 'capacite' => $salle->capacite, 'type' => $salle->type, 'active' => $salle->active]; ?>
<form class="form" method="post" action="/salles/<?= $salle->id ?>/edit">
    <label>Nom <input name="nom" value="<?= $escape($values['nom']) ?>" required></label>
    <label>Bâtiment <input name="batiment" value="<?= $escape($values['batiment']) ?>" required></label>
    <label>Capacité <input type="number" name="capacite" min="1" max="1000" value="<?= $escape($values['capacite']) ?>" required></label>
    <label>Type <select name="type" required><?php foreach (['cours', 'informatique', 'laboratoire', 'amphitheatre', 'reunion'] as $type): ?><option value="<?= $type ?>" <?= $values['type'] === $type ? 'selected' : '' ?>><?= $escape($type) ?></option><?php endforeach; ?></select></label>
    <label class="check"><input type="checkbox" name="active" value="1" <?= $values['active'] ? 'checked' : '' ?>> Active</label>
    <button class="button" type="submit">Enregistrer</button>
</form>
<?php require_once dirname(__DIR__) . '/layout/footer.php'; ?>