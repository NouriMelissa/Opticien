<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    protected $table = 'communes';
    protected $primaryKey = 'id_commune';
    public $timestamps = false;

    protected $fillable = ['nom_commune', 'id_wilaya'];

    public function wilaya()
    {
        return $this->belongsTo(Wilaya::class, 'id_wilaya', 'id_wilaya');
    }
}
