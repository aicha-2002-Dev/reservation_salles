<h2>Réservation #<?= (int) $reservation->id ?></h2>

<p>Responsable : <?= htmlspecialchars($reservation->responsable) ?></p>
<p>Email : <?= htmlspecialchars($reservation->email) ?></p>
<p>Motif : <?= htmlspecialchars($reservation->motif) ?></p>
<p>Du <?= $reservation->date_debut->format('d/m/Y H:i') ?> au <?= $reservation->date_fin->format('d/m/Y H:i') ?></p>
<p>Statut : <?= htmlspecialchars($reservation->statut) ?></p>

<p><a href="/reservations">Retour à la liste</a></p>