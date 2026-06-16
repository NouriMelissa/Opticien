<?php

use App\Enums\TypeLigneVente;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ligne_ventes', function (Blueprint $table) {
            $table->id('id_ligne_vente');
            $table->enum('type_ligne', array_column(TypeLigneVente::cases(), 'value'));
            $table->string('description')->nullable();
            $table->integer('qte')->default(1);
            $table->decimal('prix_unitaire', 10, 2)->default(0);
            $table->decimal('remise_pct', 5, 2)->default(0);
            $table->decimal('total_ligne', 12, 2)->default(0);
            $table->boolean('livree')->default(false);

            $table->foreignId('id_vente')
                ->constrained('ventes', 'id_vente')
                ->onDelete('cascade');
            $table->foreignId('id_article')
                ->nullable()
                ->constrained('articles', 'id_article')
                ->onDelete('set null');
            $table->foreignId('id_tarif_verre')
                ->nullable()
                ->constrained('tarif_verres', 'id_tarif_verre')
                ->onDelete('set null');
            $table->foreignId('id_type_verre')
                ->nullable()
                ->constrained('type_verres', 'id_type_verre')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ligne_ventes');
    }
};