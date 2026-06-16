<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommandeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_commande'          => $this->id_commande,
            'date_commande'        => $this->date_commande,
            'date_reception'       => $this->date_reception,
            'statut_commande'      => $this->statut_commande->value,
            'statut_commande_label'=> $this->statut_commande->label(),
            'total_ht'             => $this->total_ht,
            'note'                 => $this->note,
            'id_fournisseur'       => $this->id_fournisseur,
            'id_util'              => $this->id_util,

            'fournisseur' => $this->whenLoaded('fournisseur', fn () => [
                'id_fournisseur' => $this->fournisseur->id_fournisseur,
                'nom'            => $this->fournisseur->nom,
                'tel'            => $this->fournisseur->tel,
            ]),

            'utilisateur' => $this->whenLoaded('utilisateur', fn () => [
                'id_util' => $this->utilisateur->id_util,
                'nom'     => $this->utilisateur->nom,
                'prenom'  => $this->utilisateur->prenom,
            ]),

            'lignes' => $this->whenLoaded(
                'lignes',
                fn () => LigneCommandeResource::collection($this->lignes)
            ),
        ];
    }
}