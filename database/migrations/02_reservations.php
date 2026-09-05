<?php
namespace Database\migrations;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up(Capsule $capsule): void
    {
        if ($capsule->schema()->hasTable('reservations')) {
            return;
        }

        $capsule->schema()->create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salle_id')->constrained('salles');
            $table->string('responsable', 120);
            $table->string('email', 190);
            $table->string('motif', 255);
            $table->dateTime('date_debut');
            $table->dateTime('date_fin');
            $table->enum('statut', ['confirmée', 'annulée'])->default('confirmée');
            $table->timestamps();
        });
    }

    public function down(Capsule $capsule): void
    {
        $capsule->schema()->dropIfExists('reservations');
    }
};