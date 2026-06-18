<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Wilaya extends Model
{
    protected $table      = 'wilayas';
    protected $primaryKey = 'id_wilaya';
    public    $timestamps = false;

    protected $fillable = [
        'code',
        'nom_wilaya',
    ];

    // ── Relations ────────────────────────────────────────────────

    /**
     * Une wilaya a plusieurs médecins
     */
    public function medecins()
    {
        return $this->hasMany(Medecin::class, 'id_wilaya', 'id_wilaya');
    }

    /**
     * Une wilaya a plusieurs clients
     */
    public function clients()
    {
        return $this->hasMany(Client::class, 'id_wilaya', 'id_wilaya');
    }

    /**
     * Une wilaya a plusieurs fournisseurs
     */
    public function fournisseurs()
    {
        return $this->hasMany(Fournisseur::class, 'id_wilaya', 'id_wilaya');
    }

    /**
     * Une wilaya a plusieurs communes
     */
    public function communes()
    {
        return $this->hasMany(Commune::class, 'id_wilaya', 'id_wilaya');
    }

    // ── Scopes ───────────────────────────────────────────────────

    /**
     * Recherche par nom de wilaya
     * Usage : Wilaya::searchNom('alger')->get()
     */
    public function scopeSearchNom(Builder $query, string $terme): Builder
    {
        return $query->where('nom_wilaya', 'like', "%{$terme}%");
    }

    /**
     * Recherche par code (ex: '16')
     */
    public function scopeByCode(Builder $query, string $code): Builder
    {
        return $query->where('code', $code);
    }
}