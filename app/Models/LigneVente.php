<?php

namespace App\Models;

use App\Enums\TypeLigneVente;
use Illuminate\Database\Eloquent\Model;

class LigneVente extends Model
{
    protected $table = 'ligne_ventes';
    protected $primaryKey = 'id_ligne_vente';
    public $timestamps = false;

    protected $fillable = [
        'type_ligne',
        'description',
        'qte',
        'prix_unitaire',
        'remise_pct',
        'total_ligne',
        'livree',
        'id_vente',
        'id_article',
        'id_tarif_verre',
        'id_type_verre',
    ];

    protected $casts = [
        'type_ligne'    => TypeLigneVente::class,
        'livree'        => 'boolean',
        'prix_unitaire' => 'float',
        'remise_pct'    => 'float',
        'total_ligne'   => 'float',
    ];

    public function vente()
    {
        return $this->belongsTo(Vente::class, 'id_vente', 'id_vente');
    }

    public function article()
    {
        return $this->belongsTo(Article::class, 'id_article', 'id_article');
    }

    public function tarifVerre()
    {
        return $this->belongsTo(TarifVerre::class, 'id_tarif_verre', 'id_tarif_verre');
    }

    public function typeVerre()
    {
        return $this->belongsTo(TypeVerre::class, 'id_type_verre', 'id_type_verre');
    }

    /**
     * total_ligne = (prix_unitaire × qte) × (1 - remise_pct / 100)
     */
    public function calculerTotal(): void
    {
        $this->total_ligne = ($this->prix_unitaire * $this->qte)
            * (1 - $this->remise_pct / 100);
    }
}