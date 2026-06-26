<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table : clients
 * Liée à wilayas (FK nullable) et utilisateurs (FK nullable, created_by)
 * Positionnée APRES wilayas et utilisateurs, AVANT ventes/ordonnances
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {

            $table->id('id_client');                          // PK

            $table->string('nom_prenom', 150);                 // Obligatoire
            $table->date('date_naissance')->nullable();        // age calculé, jamais stocké
            $table->string('tel_portable', 15)->nullable();
            $table->string('tel_fixe', 15)->nullable();
            $table->string('email', 100)->nullable();

            // FK wilaya (nullable)
            $table->unsignedBigInteger('id_wilaya')->nullable();
            $table->foreign('id_wilaya')
                  ->references('id_wilaya')->on('wilayas')
                  ->nullOnDelete();

            $table->string('commune', 100)->nullable();        // Texte libre (pas FK stricte ici)
            $table->string('adresse', 255)->nullable();
            $table->string('profession', 100)->nullable();
            $table->text('remarque')->nullable();

            $table->date('client_depuis');                     // Renseigné à la création (auto)
            $table->dateTime('derniere_visite')->nullable();    // Mis à jour à chaque vente

            // FK utilisateur créateur (nullable)
            $table->unsignedBigInteger('created_by')->nullable();
            // APRÈS — pointe vers users.id (table Laravel par défaut)
          $table->foreign('created_by')
          ->references('id')->on('users')
         ->nullOnDelete();

            // Pas de timestamps Laravel classiques (on gère client_depuis/derniere_visite nous-mêmes)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};