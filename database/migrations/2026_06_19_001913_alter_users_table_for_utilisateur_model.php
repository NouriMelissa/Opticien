<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // si colonne name existe (Laravel default), on peut la supprimer
            if (Schema::hasColumn('users', 'name')) {
                $table->dropColumn('name');
            }

            // ajout de TES colonnes
            if (!Schema::hasColumn('users', 'nom')) {
                $table->string('nom');
            }

            if (!Schema::hasColumn('users', 'prenom')) {
                $table->string('prenom');
            }

            if (!Schema::hasColumn('users', 'tel_portable')) {
                $table->string('tel_portable')->nullable();
            }

            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('VENDEUR');
            }

            if (!Schema::hasColumn('users', 'actif')) {
                $table->boolean('actif')->default(true);
            }

            if (!Schema::hasColumn('users', 'id_wilaya')) {
                $table->unsignedBigInteger('id_wilaya')->nullable();
            }

            if (!Schema::hasColumn('users', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'nom',
                'prenom',
                'tel_portable',
                'role',
                'actif',
                'id_wilaya',
                'created_at'
            ]);
        });
    }
};
