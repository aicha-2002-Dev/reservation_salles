<h2>Salles</h2>

<p><a href="/salles/create">+ Ajouter une salle</a></p>

<?php if (empty($salles)): ?>
    <p>Aucune salle enregistrée pour le moment.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Bâtiment</th>
                <th>Capacité</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($salles as $salle): ?>
                <tr>
                    <td><?= htmlspecialchars($salle->nom) ?></td>
                    <td><?= htmlspecialchars($salle->batiment) ?></td>
                    <td><?= (int) $salle->capacite ?></td>
                    <td><?= htmlspecialchars($salle->type) ?></td>
                    <td><?= $salle->active ? 'Active' : 'Inactive' ?></td>
                    <td>
                        <a href="/salles/<?= (int) $salle->id ?>">Voir</a>
                        <a href="/salles/<?= (int) $salle->id ?>/edit">Modifier</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>