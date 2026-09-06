<h2>Réservations</h2>

<p><a href="/reservations/create">+ Nouvelle réservation</a></p>

<?php if (empty($reservations)): ?>
    <p>Aucune réservation enregistrée.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Responsable</th>
                <th>Salle</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $reservation): ?>
                <tr>
                    <td><?= htmlspecialchars($reservation->responsable) ?></td>
                    <td>Salle #<?= (int) $reservation->salle_id ?></td>
                    <td><?= $reservation->date_debut->format('d/m/Y H:i') ?></td>
                    <td><?= $reservation->date_fin->format('d/m/Y H:i') ?></td>
                    <td><?= htmlspecialchars($reservation->statut) ?></td>
                    <td>
                        <a href="/reservations/<?= (int) $reservation->id ?>">Voir</a>
                        <?php if ($reservation->statut === 'confirmée'): ?>
                            <form method="POST" action="/reservations/<?= (int) $reservation->id ?>/cancel" style="display:inline">
                                <button type="submit">Annuler</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>