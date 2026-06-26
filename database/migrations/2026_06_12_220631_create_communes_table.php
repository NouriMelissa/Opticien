<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table : communes
 * Liée à wilayas (FK obligatoire — une commune appartient TOUJOURS à une wilaya)
 * Doit être migrée APRÈS wilayas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communes', function (Blueprint $table) {

            $table->id('id_commune');                 // PK auto-increment

            $table->string('nom_commune', 100);        // Nom de la commune

            // FK obligatoire vers wilayas (table déjà existante à ce stade)
            $table->unsignedBigInteger('id_wilaya');
            $table->foreign('id_wilaya')
                  ->references('id_wilaya')
                  ->on('wilayas')
                  ->cascadeOnDelete();  // Si wilaya supprimée → ses communes aussi

            // Pas de timestamps
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communes');
    }
};