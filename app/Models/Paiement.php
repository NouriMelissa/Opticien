<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $table = 'paiements';
    protected $primaryKey = 'id_paiement';
    public $timestamps = false;

    protected $fillable = ['montant', 'mode_paiement', 'date_paiement', 'note', 'id_vente', 'id_session'];

    public function vente()
    {
        return $this->belongsTo(Vente::class, 'id_vente', 'id_vente');
    }

    public function session()
    {
        return $this->belongsTo(SessionShift::class, 'id_session', 'id_session');
    }
}
