<h2>Ajouter une salle</h2>

<?php if (!empty($erreurs)): ?>
    <div class="erreurs">
        <?php foreach ($erreurs as $messages): ?>
            <?php foreach ($messages as $message): ?>
                <p><?= htmlspecialchars($message) ?></p>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="/salles">
    <label>Nom
        <input type="text" name="nom" value="<?= htmlspecialchars($anciennesValeurs['nom'] ?? '') ?>">
    </label>
    <label>Bâtiment
        <input type="text" name="batiment" value="<?= htmlspecialchars($anciennesValeurs['batiment'] ?? '') ?>">
    </label>
    <label>Capacité
        <input type="number" name="capacite" value="<?= htmlspecialchars((string) ($anciennesValeurs['capacite'] ?? '')) ?>">
    </label>
    <label>Type
        <select name="type">
            <?php foreach (['cours', 'informatique', 'laboratoire', 'amphitheatre', 'reunion'] as $type): ?>
                <option value="<?= $type ?>" <?= (($anciennesValeurs['type'] ?? '') === $type) ? 'selected' : '' ?>>
                    <?= ucfirst($type) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <button type="submit">Créer la salle</button>
</form>