<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caisse extends Model
{
    protected $table = 'caisses';
    protected $primaryKey = 'id_caisse';
    public $timestamps = false;

    protected $fillable = ['libelle', 'fond_permanent', 'solde_actuel'];

    public function mouvements()
    {
        return $this->hasMany(MouvementCaisse::class, 'id_caisse', 'id_caisse');
    }
}
