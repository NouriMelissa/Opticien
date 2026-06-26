<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Client extends Model
{
    protected $table      = 'clients';
    protected $primaryKey = 'id_client';
    public    $timestamps = false;

    protected $fillable = [
        'nom_prenom',
        'date_naissance',
        'tel_portable',
        'tel_fixe',
        'email',
        'id_wilaya',
        'commune',
        'adresse',
        'profession',
        'remarque',
        'client_depuis',
        'derniere_visite',
        'created_by',
    ];

    protected $casts = [
        'date_naissance'  => 'date',
        'client_depuis'   => 'date',
        'derniere_visite' => 'datetime',
        'id_wilaya'       => 'integer',
        'created_by'      => 'integer',
    ];

    // ── Attribut calculé : age ──────────────────────────────────
    // Jamais stocké en BDD, toujours recalculé à la volée.
    // Accessible via $client->age dans le code PHP
    // et inclus automatiquement si on l'ajoute aux $appends.
    protected $appends = ['age'];

    public function getAgeAttribute(): ?int
    {
        if (!$this->date_naissance) {
            return null;
        }

        return Carbon::parse($this->date_naissance)->age;
    }

    // ── Relations ────────────────────────────────────────────────

    public function wilaya()
    {
        return $this->belongsTo(Wilaya::class, 'id_wilaya', 'id_wilaya');
    }

    public function createur()
    {
        return $this->belongsTo(Utilisateur::class, 'created_by', 'id_util');
    }

    public function ordonnances()
    {
        return $this->hasMany(Ordonnance::class, 'id_client', 'id_client');
    }

    public function ventes()
    {
        return $this->hasMany(Vente::class, 'id_client', 'id_client');
    }

    // ── Scopes ───────────────────────────────────────────────────

    public function scopeSearchNom(Builder $query, string $terme): Builder
    {
        return $query->where('nom_prenom', 'like', "%{$terme}%");
    }

    public function scopeByWilaya(Builder $query, int $wilayaId): Builder
    {
        return $query->where('id_wilaya', $wilayaId);
    }

    public function scopeByTel(Builder $query, string $tel): Builder
    {
        return $query->where('tel_portable', 'like', "%{$tel}%")
                     ->orWhere('tel_fixe', 'like', "%{$tel}%");
    }

    // ── Méthode métier : mettre à jour la dernière visite ────────
    // Appelée par VenteService::creer() à chaque nouvelle vente
    public function marquerVisite(): void
    {
        $this->derniere_visite = now();
        $this->save();
    }
}