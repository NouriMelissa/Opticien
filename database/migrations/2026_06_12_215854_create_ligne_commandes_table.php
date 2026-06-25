<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
     
        Schema::create('ligne_commandes', function (Blueprint $table) {
            $table->id('id_ligne_commande');
            $table->integer('qte_commandee');
            $table->integer('qte_recue')->default(0);
            $table->decimal('prix_unitaire', 10, 2)->default(0);

            $table->foreignId('id_commande')
                ->constrained('commande_fournisseurs', 'id_commande')
                ->onDelete('cascade');
            $table->foreignId('id_article')
                ->nullable()
                ->constrained('articles', 'id_article')
                ->onDelete('set null');
            $table->foreignId('id_tarif_verre')
                ->nullable()
                ->constrained('tarif_verres', 'id_tarif_verre')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ligne_commandes');
    }
};