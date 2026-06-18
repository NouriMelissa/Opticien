<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute la contrainte FK medecins.id_wilaya → wilayas.id_wilaya
 * À lancer APRÈS que la table wilayas existe.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medecins', function (Blueprint $table) {
            $table->foreign('id_wilaya')
                  ->references('id_wilaya')
                  ->on('wilayas')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('medecins', function (Blueprint $table) {
            $table->dropForeign(['id_wilaya']);
        });
    }
};