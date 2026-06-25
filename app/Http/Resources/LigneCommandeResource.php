<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LigneCommandeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_ligne_commande' => $this->id_ligne_commande,
            'qte_commandee'     => $this->qte_commandee,
            'qte_recue'         => $this->qte_recue,
            'qte_restante'      => $this->qte_commandee - $this->qte_recue,
            'prix_unitaire'     => $this->prix_unitaire,
            'total_ligne'       => $this->prix_unitaire * $this->qte_commandee,
            'id_commande'       => $this->id_commande,
            'id_article'        => $this->id_article,
            'id_tarif_verre'    => $this->id_tarif_verre,

            'article' => $this->whenLoaded('article', fn () => $this->article ? [
                'id_article'   => $this->article->id_article,
                'modele'       => $this->article->modele,
                'reference'    => $this->article->reference,
                'stock_actuel' => $this->article->stock_actuel,
            ] : null),

            'tarif_verre' => $this->whenLoaded('tarifVerre', fn () => $this->tarifVerre ? [
                'id_tarif_verre' => $this->tarifVerre->id_tarif_verre,
                'gamme'          => $this->tarifVerre->gamme,
            ] : null),
        ];
    }
}