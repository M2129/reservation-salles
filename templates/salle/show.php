<?php require_once dirname(__DIR__) . '/layout/header.php'; ?>
<p class="eyebrow">Salle</p><h1><?= $escape($salle->nom) ?></h1>
<dl class="details"><dt>Bâtiment</dt><dd><?= $escape($salle->batiment) ?></dd><dt>Capacité</dt><dd><?= $salle->capacite ?> places</dd><dt>Type</dt><dd><?= $escape($salle->type) ?></dd><dt>État</dt><dd><?= $salle->active ? 'Active' : 'Inactive' ?></dd></dl>
<p><a class="button" href="/salles/<?= $salle->id ?>/edit">Modifier</a></p>
<?php require_once dirname(__DIR__) . '/layout/footer.php'; ?>