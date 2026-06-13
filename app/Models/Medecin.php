<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Medecin extends Model
{
    // ── Configuration ───────────────────────────────────────────────
    protected $table      = 'medecins';
    protected $primaryKey = 'id_medecin';
    public    $timestamps = false;           // Pas de created_at / updated_at

    // ── Champs autorisés en masse (Mass Assignment) ─────────────────
    protected $fillable = [
        'nom_prenom',
        'specialite',
        'tel',
        'adresse',
        'id_wilaya',
    ];

    // ── Casting des types ───────────────────────────────────────────
    protected $casts = [
        'id_wilaya' => 'integer',
    ];

    // ── Relations ───────────────────────────────────────────────────

    /**
     * Un médecin appartient à une wilaya (optionnel)
     * Medecin.id_wilaya → Wilaya.id_wilaya
     */
    public function wilaya()
    {
        return $this->belongsTo(Wilaya::class, 'id_wilaya', 'id_wilaya');
    }

    /**
     * Un médecin peut avoir plusieurs ordonnances
     * Ordonnance.id_medecin → Medecin.id_medecin
     */
    public function ordonnances()
    {
        return $this->hasMany(Ordonnance::class, 'id_medecin', 'id_medecin');
    }

    // ── Scopes (filtres réutilisables) ──────────────────────────────

    /**
     * Filtrer par nom/prénom (recherche partielle)
     * Usage : Medecin::searchNom('Bensalem')->get()
     */
    public function scopeSearchNom(Builder $query, string $terme): Builder
    {
        return $query->where('nom_prenom', 'like', "%{$terme}%");
    }

    /**
     * Filtrer par spécialité
     * Usage : Medecin::bySpecialite('Ophtalmologue')->get()
     */
    public function scopeBySpecialite(Builder $query, string $specialite): Builder
    {
        return $query->where('specialite', $specialite);
    }

    /**
     * Filtrer par wilaya
     * Usage : Medecin::byWilaya(16)->get()
     */
    public function scopeByWilaya(Builder $query, int $wilayaId): Builder
    {
        return $query->where('id_wilaya', $wilayaId);
    }
}