<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id_categorie';
    public $timestamps = false;

    protected $fillable = ['libelle', 'type_categorie'];

    public function articles()
    {
        return $this->hasMany(Article::class, 'id_categorie', 'id_categorie');
    }
}
