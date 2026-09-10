<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titre ?? 'Réservation de salles') ?></title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <header>
        <h1>Gestion des réservations de salles</h1>

        <nav>
            <a href="/salles">Salles</a>
            <a href="/reservations">Réservations</a>
        </nav>
    </header>

    <main>

        <?php if (!empty($messageSucces)): ?>
            <div class="succes">
                <?= htmlspecialchars($messageSucces) ?>
            </div>
        <?php endif; ?>

        <?= $contenu ?>

    </main>
</body>
</html>