<?php require_once dirname(__DIR__) . '/layout/header.php'; ?>
<section class="heading-row"><div><p class="eyebrow">Campus</p><h1>Salles disponibles</h1></div><a class="button" href="/salles/create">Ajouter une salle</a></section>
<form class="toolbar" method="get" action="/salles"><label for="room-search">Recherche</label><input id="room-search" name="q" value="<?= $escape($search) ?>" placeholder="Nom ou bâtiment"><button type="submit">Rechercher</button></form>
<div class="list">
<?php foreach ($salles as $salle): ?>
    <article class="item"><div><h2><a href="/salles/<?= $salle->id ?>"><?= $escape($salle->nom) ?></a></h2><p><?= $escape($salle->batiment) ?> · <?= $salle->capacite ?> places · <?= $escape($salle->type) ?></p></div><span class="status <?= $salle->active ? 'active' : 'inactive' ?>"><?= $salle->active ? 'Active' : 'Inactive' ?></span></article>
<?php endforeach; ?>
</div>
<?php require_once dirname(__DIR__) . '/layout/footer.php'; ?>