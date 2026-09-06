<h2>Modifier la salle</h2>

<?php if (!empty($erreurs)): ?>
    <div class="erreurs">
        <?php foreach ($erreurs as $messages): ?>
            <?php foreach ($messages as $message): ?>
                <p><?= htmlspecialchars($message) ?></p>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="/salles/<?= (int) $salle->id ?>">
    <label>Nom
        <input type="text" name="nom" value="<?= htmlspecialchars($salle->nom) ?>">
    </label>
    <label>Bâtiment
        <input type="text" name="batiment" value="<?= htmlspecialchars($salle->batiment) ?>">
    </label>
    <label>Capacité
        <input type="number" name="capacite" value="<?= (int) $salle->capacite ?>">
    </label>
    <label>Type
        <select name="type">
            <?php foreach (['cours', 'informatique', 'laboratoire', 'amphitheatre', 'reunion'] as $type): ?>
                <option value="<?= $type ?>" <?= ($salle->type === $type) ? 'selected' : '' ?>>
                    <?= ucfirst($type) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>
        <input type="checkbox" name="active" <?= $salle->active ? 'checked' : '' ?>>
        Salle active
    </label>
    <button type="submit">Enregistrer les modifications</button>
</form>