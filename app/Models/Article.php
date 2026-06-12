<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';
    protected $primaryKey = 'id_article';
    public $timestamps = false;

    protected $fillable = [
        'code_barre', 'reference', 'modele',
        'prix_achat', 'remise_achat_pct', 'prix_achat_net', 'main_oeuvre',
        'coefficient', 'prix_vente_ht', 'tva_pct', 'remise_client_pct', 'prix_vente_ttc',
        'stock_actuel', 'stock_min', 'valeur_stock', 'date_entree', 'actif',
        'id_categorie', 'id_marque', 'id_fournisseur', 'id_magasin',
    ];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'id_categorie', 'id_categorie');
    }

    public function marque()
    {
        return $this->belongsTo(Marque::class, 'id_marque', 'id_marque');
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class, 'id_fournisseur', 'id_fournisseur');
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class, 'id_magasin', 'id_magasin');
    }

    public function ligneVentes()
    {
        return $this->hasMany(LigneVente::class, 'id_article', 'id_article');
    }

    public function ligneCommandes()
    {
        return $this->hasMany(LigneCommande::class, 'id_article', 'id_article');
    }

    public function mouvementsStock()
    {
        return $this->hasMany(MouvementStock::class, 'id_article', 'id_article');
    }

    /**
     * Calcule les champs prix selon la formule du cahier des charges :
     * prix_achat_net = prix_achat × (1 - remise_achat_pct/100)
     * prix_vente_ht  = (prix_achat_net + main_oeuvre) × coefficient
     * prix_vente_ttc = prix_vente_ht × (1 + tva_pct/100) puis remise client
     */
    public function calculerPrix(): void
    {
        $this->prix_achat_net = $this->prix_achat * (1 - $this->remise_achat_pct / 100);
        $this->prix_vente_ht = ($this->prix_achat_net + $this->main_oeuvre) * $this->coefficient;

        $ttc = $this->prix_vente_ht * (1 + $this->tva_pct / 100);
        $this->prix_vente_ttc = $ttc * (1 - $this->remise_client_pct / 100);

        $this->valeur_stock = $this->stock_actuel * $this->prix_achat;
    }
}
