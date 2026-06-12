<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Utilisateur extends Model
{
    protected $table = 'utilisateurs';
    protected $primaryKey = 'id_util';
    public $timestamps = false;

    protected $fillable = [
        'nom', 'prenom', 'login', 'password_hash', 'email',
        'tel_portable', 'role', 'actif', 'id_wilaya', 'created_at',
    ];

    protected $hidden = ['password_hash'];

    public function wilaya()
    {
        return $this->belongsTo(Wilaya::class, 'id_wilaya', 'id_wilaya');
    }

    public function sessions()
    {
        return $this->hasMany(SessionShift::class, 'id_util', 'id_util');
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'id_util', 'id_util');
    }

    public function mouvementsCaisse()
    {
        return $this->hasMany(MouvementCaisse::class, 'id_util', 'id_util');
    }

    public function mouvementsStock()
    {
        return $this->hasMany(MouvementStock::class, 'id_util', 'id_util');
    }

    public function commandesFournisseur()
    {
        return $this->hasMany(CommandeFournisseur::class, 'id_util', 'id_util');
    }
}
