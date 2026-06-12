<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Echeance extends Model
{
    protected $table = 'echeances';
    protected $primaryKey = 'id_echeance';
    public $timestamps = false;

    protected $fillable = [
        'montant_prevu', 'date_echeance', 'montant_paye', 'date_paiement',
        'statut_echeance', 'id_vente',
    ];

    public function vente()
    {
        return $this->belongsTo(Vente::class, 'id_vente', 'id_vente');
    }
}
