<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Commune extends Model
{
    protected $table      = 'communes';
    protected $primaryKey = 'id_commune';
    public    $timestamps = false;

    protected $fillable = [
        'nom_commune',
        'id_wilaya',
    ];

    protected $casts = [
        'id_wilaya' => 'integer',
    ];

    // ── Relations ────────────────────────────────────────────────

    /**
     * Une commune appartient à une wilaya (obligatoire)
     */
    public function wilaya()
    {
        return $this->belongsTo(Wilaya::class, 'id_wilaya', 'id_wilaya');
    }

    // ── Scopes ───────────────────────────────────────────────────

    /**
     * Recherche par nom de commune
     * Usage : Commune::searchNom('Bab El Oued')->get()
     */
    public function scopeSearchNom(Builder $query, string $terme): Builder
    {
        return $query->where('nom_commune', 'like', "%{$terme}%");
    }

    /**
     * Filtrer les communes d'une wilaya donnée
     * Usage : Commune::byWilaya(16)->get()
     */
    public function scopeByWilaya(Builder $query, int $wilayaId): Builder
    {
        return $query->where('id_wilaya', $wilayaId);
    }
}