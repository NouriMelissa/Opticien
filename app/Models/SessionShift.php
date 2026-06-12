<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionShift extends Model
{
    protected $table = 'session_shifts';
    protected $primaryKey = 'id_session';
    public $timestamps = false;

    protected $fillable = [
        'date_ouverture', 'date_fermeture', 'fond_caisse_initial',
        'total_especes', 'total_tpe', 'total_ventes', 'statut', 'id_util',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_util', 'id_util');
    }

    public function ventes()
    {
        return $this->hasMany(Vente::class, 'id_session', 'id_session');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'id_session', 'id_session');
    }

    public function mouvementsCaisse()
    {
        return $this->hasMany(MouvementCaisse::class, 'id_session', 'id_session');
    }
}
