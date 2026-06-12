<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TarifVerre extends Model
{
    protected $table = 'tarif_verres';
    protected $primaryKey = 'id_tarif_verre';
    public $timestamps = false;

    protected $fillable = [
        'gamme', 'sph_min', 'sph_max', 'cyl_min', 'cyl_max',
        'prix_achat', 'prix_vente_ht', 'prix_vente_ttc', 'actif',
        'id_fournisseur', 'id_type_verre',
    ];

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class, 'id_fournisseur', 'id_fournisseur');
    }

    public function typeVerre()
    {
        return $this->belongsTo(TypeVerre::class, 'id_type_verre', 'id_type_verre');
    }

    public function ligneVentes()
    {
        return $this->hasMany(LigneVente::class, 'id_tarif_verre', 'id_tarif_verre');
    }

    public function ligneCommandes()
    {
        return $this->hasMany(LigneCommande::class, 'id_tarif_verre', 'id_tarif_verre');
    }
}
