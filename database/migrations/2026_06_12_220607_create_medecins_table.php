<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medecins', function (Blueprint $table) {

            // ── Clé primaire ──────────────────────────────────────
            $table->id('id_medecin');

            // ── Colonnes métier ───────────────────────────────────
            $table->string('nom_prenom', 150);
            $table->string('specialite', 100)->nullable();
            $table->string('tel', 15)->nullable();
            $table->string('adresse', 255)->nullable();

            // ── FK vers wilayas (nullable, sans contrainte stricte) ─
            // On utilise unsignedBigInteger SANS foreign() ici
            // car la table wilayas n'est peut-être pas encore créée
            // La contrainte FK sera ajoutée séparément si besoin
            $table->unsignedBigInteger('id_wilaya')->nullable();

            // Pas de timestamps
        });
    }

    public function down(): void
    {
        // Supprimer la table directement, sans toucher aux FK
        Schema::dropIfExists('medecins');
    }
};