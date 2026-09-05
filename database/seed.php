<?php

namespace Database;

require __DIR__ . '/../vendor/autoload.php';

use App\Model\Salle;

$capsuleFactory = require __DIR__ . '/../config/database.php';
$capsuleFactory();

$sallesInitiales = [
    ['nom' => 'Amphithéâtre A',        'batiment' => 'Bâtiment principal', 'capacite' => 250, 'type' => 'amphitheatre', 'active' => true],
    ['nom' => 'Salle B12',              'batiment' => 'Bâtiment B',         'capacite' => 40,  'type' => 'cours',        'active' => true],
    ['nom' => 'Laboratoire Chimie',     'batiment' => 'Bâtiment sciences',  'capacite' => 24,  'type' => 'laboratoire',  'active' => true],
    ['nom' => 'Salle Informatique 1',   'batiment' => 'Bâtiment C',         'capacite' => 30,  'type' => 'informatique', 'active' => true],
    ['nom' => 'Salle de réunion',       'batiment' => 'Bâtiment administratif', 'capacite' => 12, 'type' => 'reunion',   'active' => true],
];

$nombreCrees = 0;

foreach ($sallesInitiales as $donneesSalle) {
    $salle = Salle::firstOrCreate(
        [
            'nom'      => $donneesSalle['nom'],
            'batiment' => $donneesSalle['batiment'],
        ],
        $donneesSalle
    );

    if ($salle->wasRecentlyCreated) {
        echo "Créée : {$salle->nom}\n";
        $nombreCrees++;
    } else {
        echo "Déjà existante, ignorée : {$salle->nom}\n";
    }
}

echo "\n{$nombreCrees} salle(s) créée(s) sur " . count($sallesInitiales) . " définie(s).\n";