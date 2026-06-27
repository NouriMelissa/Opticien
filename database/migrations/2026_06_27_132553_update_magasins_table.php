<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('magasins', function (Blueprint $table) {

            // Renommer la clé primaire
            $table->renameColumn('id', 'id_magasin');

            // Ajouter les colonnes
            $table->string('libelle', 100);
            $table->string('adresse', 255);
            $table->boolean('actif')->default(true);

            // Supprimer les timestamps
            $table->dropTimestamps();
        });
    }

    public function down(): void
    {
        Schema::table('magasins', function (Blueprint $table) {

            $table->renameColumn('id_magasin', 'id');

            $table->dropColumn([
                'libelle',
                'adresse',
                'actif'
            ]);

            $table->timestamps();
        });
    }
};