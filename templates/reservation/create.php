<h2>Réserver une salle</h2>

<?php if (!empty($erreurs)): ?>
    <div class="erreurs">
        <?php foreach ($erreurs as $messages): ?>
            <?php foreach ($messages as $message): ?>
                <p><?= htmlspecialchars($message) ?></p>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="/reservations">
    <label>ID de la salle
        <input type="number" name="salle_id" value="<?= htmlspecialchars((string) ($anciennesValeurs['salle_id'] ?? '')) ?>">
    </label>
    <label>Responsable
        <input type="text" name="responsable" value="<?= htmlspecialchars($anciennesValeurs['responsable'] ?? '') ?>">
    </label>
    <label>Email
        <input type="email" name="email" value="<?= htmlspecialchars($anciennesValeurs['email'] ?? '') ?>">
    </label>
    <label>Motif
        <textarea name="motif"><?= htmlspecialchars($anciennesValeurs['motif'] ?? '') ?></textarea>
    </label>
    <label>Date de début
        <input type="datetime-local" name="date_debut" value="<?= htmlspecialchars($anciennesValeurs['date_debut'] ?? '') ?>">
    </label>
    <label>Date de fin
        <input type="datetime-local" name="date_fin" value="<?= htmlspecialchars($anciennesValeurs['date_fin'] ?? '') ?>">
    </label>
    <button type="submit">Réserver</button>
</form>