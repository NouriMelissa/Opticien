<?php

namespace App\Models;

use App\Enums\ModePaiement;
use App\Enums\StatutVente;
use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    protected $table = 'ventes';
    protected $primaryKey = 'id_vente';
    public $timestamps = false;

    protected $fillable = [
        'date_vente', 'total_avant_remise', 'remise_globale_pct', 'remise_globale_mnt',
        'total_ttc', 'montant_encaisse', 'reste_a_payer', 'mode_paiement',
        'date_livraison_prevue', 'livree', 'statut_vente', 'note',
        'id_client', 'id_session', 'id_ordonnance',
    ];

    protected $casts = [
        'statut_vente' => StatutVente::class,
        'mode_paiement' => ModePaiement::class,
        'livree' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client', 'id_client');
    }

    public function session()
    {
        return $this->belongsTo(SessionShift::class, 'id_session', 'id_session');
    }

    public function ordonnance()
    {
        return $this->belongsTo(Ordonnance::class, 'id_ordonnance', 'id_ordonnance');
    }

    public function ligneVentes()
    {
        return $this->hasMany(LigneVente::class, 'id_vente', 'id_vente');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'id_vente', 'id_vente');
    }

    public function echeances()
    {
        return $this->hasMany(Echeance::class, 'id_vente', 'id_vente');
    }

    public function factures()
    {
        return $this->hasMany(Facture::class, 'id_vente', 'id_vente');
    }

    /**
     * Recalcule le total_ttc à partir des lignes de vente.
     */
    public function recalculerTotal(): void
    {
        $totalAvantRemise = $this->ligneVentes->sum('total_ligne');
        $this->total_avant_remise = $totalAvantRemise;
        $this->remise_globale_mnt = $totalAvantRemise * ($this->remise_globale_pct / 100);
        $this->total_ttc = $totalAvantRemise - $this->remise_globale_mnt;
        $this->reste_a_payer = $this->total_ttc - $this->montant_encaisse;
    }
}