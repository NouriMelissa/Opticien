<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marque extends Model
{
    protected $table = 'marques';
    protected $primaryKey = 'id_marque';
    public $timestamps = false;

    protected $fillable = ['libelle', 'logo_url', 'actif'];

    public function articles()
    {
        return $this->hasMany(Article::class, 'id_marque', 'id_marque');
    }
}
