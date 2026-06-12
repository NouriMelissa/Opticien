<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeVerre extends Model
{
    protected $table = 'type_verres';
    protected $primaryKey = 'id_type_verre';
    public $timestamps = false;

    protected $fillable = ['libelle', 'description', 'majoration_prix', 'actif'];

    public function tarifVerres()
    {
        return $this->hasMany(TarifVerre::class, 'id_type_verre', 'id_type_verre');
    }

    public function ligneVentes()
    {
        return $this->hasMany(LigneVente::class, 'id_type_verre', 'id_type_verre');
    }
}
