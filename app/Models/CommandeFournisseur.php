<?php

namespace App\Models;

use App\Enums\StatutCommande;
use Illuminate\Database\Eloquent\Model;

class CommandeFournisseur extends Model
{
    protected $table = 'commande_fournisseurs';
    protected $primaryKey = 'id_commande';
    public $timestamps = false;

    protected $fillable = [
        'date_commande',
        'date_reception',
        'statut_commande',
        'total_ht',
        'note',
        'id_fournisseur',
        'id_util',
    ];

    protected $casts = [
        'statut_commande' => StatutCommande::class,
        'total_ht'        => 'float',
    ];

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class, 'id_fournisseur', 'id_fournisseur');
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_util', 'id_util');
    }

    public function lignes()
    {
        return $this->hasMany(LigneCommande::class, 'id_commande', 'id_commande');
    }

    /**
     * total_ht = somme de (prix_unitaire × qte_commandee) de chaque ligne
     * Pas de total_ligne en base donc on calcule à la volée depuis les lignes
     */
    public function recalculerTotal(): void
    {
        $this->total_ht = $this->lignes->sum(
            fn ($l) => $l->prix_unitaire * $l->qte_commandee
        );
        $this->save();
    }
}