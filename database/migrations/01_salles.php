<?php
namespace Database\migrations;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up(Capsule $capsule): void
    {
        if ($capsule->schema()->hasTable('salles')) {
            return;
        }

        $capsule->schema()->create('salles', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);
            $table->string('batiment', 100);
            $table->unsignedInteger('capacite');
            $table->enum('type', ['cours', 'informatique', 'laboratoire', 'amphitheatre', 'reunion']);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(Capsule $capsule): void
    {
        $capsule->schema()->dropIfExists('salles');
    }
};