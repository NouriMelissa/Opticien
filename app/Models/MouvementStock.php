<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MouvementStock extends Model
{
    protected $table = 'mouvement_stocks';
    protected $primaryKey = 'id_mvt_stock';
    public $timestamps = false;

    protected $fillable = [
        'type_mvt_stock', 'quantite', 'reference_id', 'type_reference',
        'date_mouvement', 'note', 'id_article', 'id_util',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class, 'id_article', 'id_article');
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_util', 'id_util');
    }

    /**
     * Récupère le modèle source (Vente, CommandeFournisseur...) via
     * reference_id + type_reference (relation polymorphe manuelle).
     */
    public function source()
    {
        return match ($this->type_reference) {
            'vente' => Vente::find($this->reference_id),
            'commande' => CommandeFournisseur::find($this->reference_id),
            default => null,
        };
    }

    /**
     * Applique le mouvement sur le stock de l'article et met à jour
     * stock_actuel + valeur_stock.
     */
    public function appliquer(): void
    {
        $article = $this->article;

        match ($this->type_mvt_stock) {
            'entree', 'retour' => $article->stock_actuel += $this->quantite,
            'sortie' => $article->stock_actuel -= $this->quantite,
            'ajustement' => $article->stock_actuel += $this->quantite, // peut être négatif
        };

        $article->valeur_stock = $article->stock_actuel * $article->prix_achat;
        $article->save();
    }
}
