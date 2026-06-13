<?php

use App\Enums\ModePaiement;
use App\Enums\StatutVente;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventes', function (Blueprint $table) {
            $table->id('id_vente');
            $table->dateTime('date_vente')->useCurrent();
            $table->decimal('total_avant_remise', 12, 2)->default(0);
            $table->decimal('remise_globale_pct', 5, 2)->default(0);
            $table->decimal('remise_globale_mnt', 12, 2)->default(0);
            $table->decimal('total_ttc', 12, 2)->default(0);
            $table->decimal('montant_encaisse', 12, 2)->default(0);
            $table->decimal('reste_a_payer', 12, 2)->default(0);
            $table->enum('mode_paiement', array_column(ModePaiement::cases(), 'value'))->default(ModePaiement::Especes->value);
            $table->date('date_livraison_prevue')->nullable();
            $table->boolean('livree')->default(false);
            $table->enum('statut_vente', array_column(StatutVente::cases(), 'value'))->default(StatutVente::Brouillon->value);
            $table->text('note')->nullable();

            $table->foreignId('id_client')->constrained('clients', 'id_client')->onDelete('cascade');
            $table->foreignId('id_session')->constrained('session_shifts', 'id_session')->onDelete('cascade');
            $table->foreignId('id_ordonnance')->nullable()->constrained('ordonnances', 'id_ordonnance')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventes');
    }
};