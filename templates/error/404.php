
<?php if (!empty($erreurs)): ?>
    <div class="erreurs">
        <?php foreach ($erreurs as $erreur): ?>
            <p><?= htmlspecialchars($erreur) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h2>Erreur</h2>
<p><?= htmlspecialchars($message ?? "Ressource introuvable.") ?></p>
<p><a href="/">Retour à l'accueil</a></p>