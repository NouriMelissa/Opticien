<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LigneCommande extends Model
{
    protected $table = 'ligne_commandes';
    protected $primaryKey = 'id_ligne_commande';
    public $timestamps = false;

    protected $fillable = [
        'qte_commandee', 'qte_recue', 'prix_unitaire',
        'id_commande', 'id_article', 'id_tarif_verre',
    ];

    protected $casts = [
        'prix_unitaire' => 'float',
    
    ];

    public function commande()
    {
        return $this->belongsTo(CommandeFournisseur::class, 'id_commande', 'id_commande');
    }

    public function article()
    {
        return $this->belongsTo(Article::class, 'id_article', 'id_article');
    }

    public function tarifVerre()
    {
        return $this->belongsTo(TarifVerre::class, 'id_tarif_verre', 'id_tarif_verre');
    }

    public function getTotalLigneAttribute(): float
    {
        return $this->prix_unitaire * $this->qte_commandee;
    }
}