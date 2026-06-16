<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LigneVenteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_ligne_vente'   => $this->id_ligne_vente,

            // Enum avec valeur + label lisible
            'type_ligne'       => $this->type_ligne->value,
            'type_ligne_label' => $this->type_ligne->label(),

            'description'      => $this->description,
            'qte'              => $this->qte,
            'prix_unitaire'    => $this->prix_unitaire,
            'remise_pct'       => $this->remise_pct,
            'total_ligne'      => $this->total_ligne,
            'livree'           => $this->livree,

            // Clés étrangères brutes (utiles côté front)
            'id_vente'         => $this->id_vente,
            'id_article'       => $this->id_article,
            'id_tarif_verre'   => $this->id_tarif_verre,
            'id_type_verre'    => $this->id_type_verre,

            // Relations chargées conditionnellement
            'article' => $this->whenLoaded('article', fn () => [
                'id_article'   => $this->article->id_article,
                'modele'       => $this->article->modele,
                'reference'    => $this->article->reference,
                'code_barre'   => $this->article->code_barre,
                'stock_actuel' => $this->article->stock_actuel,
            ]),

            'tarif_verre' => $this->whenLoaded('tarifVerre', fn () => [
                'id_tarif_verre'  => $this->tarifVerre->id_tarif_verre,
                'gamme'           => $this->tarifVerre->gamme,
                'prix_vente_ttc'  => $this->tarifVerre->prix_vente_ttc,
            ]),

            'type_verre' => $this->whenLoaded('typeVerre', fn () => [
                'id_type_verre' => $this->typeVerre->id_type_verre,
                'libelle'       => $this->typeVerre->libelle,
            ]),
        ];
    }
}