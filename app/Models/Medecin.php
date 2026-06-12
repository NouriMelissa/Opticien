<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medecin extends Model
{
    protected $table = 'medecins';
    protected $primaryKey = 'id_medecin';
    public $timestamps = false;

    protected $fillable = ['nom_prenom', 'specialite', 'tel', 'adresse', 'id_wilaya'];

    public function wilaya()
    {
        return $this->belongsTo(Wilaya::class, 'id_wilaya', 'id_wilaya');
    }

    public function ordonnances()
    {
        return $this->hasMany(Ordonnance::class, 'id_medecin', 'id_medecin');
    }
}
