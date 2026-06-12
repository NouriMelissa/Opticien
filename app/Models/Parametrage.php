<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametrage extends Model
{
    protected $table = 'parametrages';
    protected $primaryKey = 'id_param';
    public $timestamps = false;

    protected $fillable = ['cle_param', 'valeur_param', 'type_param', 'description_param'];

    /**
     * Récupère une valeur de config par sa clé.
     * Ex: Parametrage::get('tva_pct', 19)
     */
    public static function get(string $cle, $defaut = null)
    {
        $param = static::where('cle_param', $cle)->first();
        return $param ? $param->valeur_param : $defaut;
    }
}
