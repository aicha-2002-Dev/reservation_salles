<h2><?= htmlspecialchars($salle->nom) ?></h2>

<p>Bâtiment : <?= htmlspecialchars($salle->batiment) ?></p>
<p>Capacité : <?= (int) $salle->capacite ?> personnes</p>
<p>Type : <?= htmlspecialchars($salle->type) ?></p>
<p>Statut : <?= $salle->active ? 'Active' : 'Inactive' ?></p>

<p>
    <a href="/salles/<?= (int) $salle->id ?>/edit">Modifier cette salle</a>
    | <a href="/salles">Retour à la liste</a>
</p>