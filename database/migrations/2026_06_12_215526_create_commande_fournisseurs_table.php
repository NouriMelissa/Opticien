<?php

use App\Enums\StatutCommande;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commande_fournisseurs', function (Blueprint $table) {
            $table->id('id_commande');
            $table->date('date_commande');
            $table->date('date_reception')->nullable();
            $table->enum('statut_commande', array_column(StatutCommande::cases(), 'value'))
                ->default(StatutCommande::EnAttente->value);
            $table->decimal('total_ht', 12, 2)->default(0);
            $table->text('note')->nullable();

            $table->foreignId('id_fournisseur')
                ->constrained('fournisseurs', 'id_fournisseur')
                ->onDelete('cascade');
            $table->foreignId('id_util')
                ->constrained('utilisateurs', 'id_util')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commande_fournisseurs');
    }
};