<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Magasin extends Model
{
    protected $table = 'magasins';
    protected $primaryKey = 'id_magasin';
    public $timestamps = false;

    protected $fillable = ['libelle', 'adresse', 'actif'];

    public function articles()
    {
        return $this->hasMany(Article::class, 'id_magasin', 'id_magasin');
    }
}
