<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';
    protected $primaryKey = 'id_client';
    public $timestamps = false;

    protected $fillable = [
        'nom_prenom', 'date_naissance', 'tel_portable', 'tel_fixe', 'email',
        'adresse', 'commune', 'profession', 'remarque', 'client_depuis',
        'derniere_visite', 'id_wilaya', 'id_util',
    ];

    public function wilaya()
    {
        return $this->belongsTo(Wilaya::class, 'id_wilaya', 'id_wilaya');
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_util', 'id_util');
    }

    public function ordonnances()
    {
        return $this->hasMany(Ordonnance::class, 'id_client', 'id_client');
    }

    public function ventes()
    {
        return $this->hasMany(Vente::class, 'id_client', 'id_client');
    }
}
