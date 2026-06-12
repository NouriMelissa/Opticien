<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilaya extends Model
{
    protected $table = 'wilayas';
    protected $primaryKey = 'id_wilaya';
    public $timestamps = false;

    protected $fillable = ['code_wilaya', 'nom_wilaya'];

    public function communes()
    {
        return $this->hasMany(Commune::class, 'id_wilaya', 'id_wilaya');
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'id_wilaya', 'id_wilaya');
    }

    public function utilisateurs()
    {
        return $this->hasMany(Utilisateur::class, 'id_wilaya', 'id_wilaya');
    }

    public function fournisseurs()
    {
        return $this->hasMany(Fournisseur::class, 'id_wilaya', 'id_wilaya');
    }

    public function medecins()
    {
        return $this->hasMany(Medecin::class, 'id_wilaya', 'id_wilaya');
    }
}
