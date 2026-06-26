<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * MedecinResource
 *
 * Transforme un objet Medecin en JSON structuré.
 * C'est ici qu'on contrôle exactement ce que l'API renvoie
 * — jamais directement le Model brut.
 */
class MedecinResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_medecin'  => $this->id_medecin,
            'nom_prenom'  => $this->nom_prenom,
            'specialite'  => $this->specialite,
            'tel'         => $this->tel,
            'adresse'     => $this->adresse,

            // Relation wilaya — incluse seulement si chargée (with('wilaya'))
            'id_wilaya'   => $this->id_wilaya,
            'wilaya'      => $this->whenLoaded('wilaya', function () {
                return [
                    'id_wilaya'   => $this->wilaya->id_wilaya,
                    'code'        => $this->wilaya->code,
                    'nom_wilaya'  => $this->wilaya->nom_wilaya,
                ];
            }),

            // Nombre d'ordonnances liées — inclus seulement si withCount('ordonnances')
            // 'nb_ordonnances' => $this->whenCounted('ordonnances'),
        ];
    }
}