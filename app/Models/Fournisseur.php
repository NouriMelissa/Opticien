<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
    protected $table = 'fournisseurs';
    protected $primaryKey = 'id_fournisseur';
    public $timestamps = false;

    protected $fillable = ['nom', 'contact', 'tel', 'adresse', 'type_fournisseur', 'actif', 'id_wilaya'];

    public function wilaya()
    {
        return $this->belongsTo(Wilaya::class, 'id_wilaya', 'id_wilaya');
    }

    public function articles()
    {
        return $this->hasMany(Article::class, 'id_fournisseur', 'id_fournisseur');
    }

    public function tarifVerres()
    {
        return $this->hasMany(TarifVerre::class, 'id_fournisseur', 'id_fournisseur');
    }

    public function commandes()
    {
        return $this->hasMany(CommandeFournisseur::class, 'id_fournisseur', 'id_fournisseur');
    }
}
