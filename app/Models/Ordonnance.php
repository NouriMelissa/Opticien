<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ordonnance extends Model
{
    protected $table = 'ordonnances';
    protected $primaryKey = 'id_ordonnance';
    public $timestamps = false;

    protected $fillable = [
        'date_prescription', 'operation', 'observation',
        'od_loin_sph', 'od_loin_cyl', 'od_loin_axe', 'od_loin_add', 'od_loin_prisme', 'od_loin_base',
        'od_pres_sph', 'od_pres_cyl', 'od_pres_axe',
        'og_loin_sph', 'og_loin_cyl', 'og_loin_axe', 'og_loin_add', 'og_loin_prisme', 'og_loin_base',
        'og_pres_sph', 'og_pres_cyl', 'og_pres_axe',
        'ecart_od', 'ecart_og', 'created_at',
        'id_client', 'id_medecin',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client', 'id_client');
    }

    public function medecin()
    {
        return $this->belongsTo(Medecin::class, 'id_medecin', 'id_medecin');
    }

    public function ventes()
    {
        return $this->hasMany(Vente::class, 'id_ordonnance', 'id_ordonnance');
    }
}
