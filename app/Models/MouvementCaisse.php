<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MouvementCaisse extends Model
{
    protected $table = 'mouvement_caisses';
    protected $primaryKey = 'id_mvt_caisse';
    public $timestamps = false;

    protected $fillable = [
        'type_mouvement', 'montant', 'reference', 'description', 'date_mouvement',
        'id_session', 'id_caisse', 'id_util',
    ];

    public function session()
    {
        return $this->belongsTo(SessionShift::class, 'id_session', 'id_session');
    }

    public function caisse()
    {
        return $this->belongsTo(Caisse::class, 'id_caisse', 'id_caisse');
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_util', 'id_util');
    }
}
