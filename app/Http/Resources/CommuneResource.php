<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommuneResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_commune'  => $this->id_commune,
            'nom_commune' => $this->nom_commune,
            'id_wilaya'   => $this->id_wilaya,

            // Relation wilaya — incluse seulement si chargée (with('wilaya'))
            'wilaya' => $this->whenLoaded('wilaya', function () {
                return [
                    'id_wilaya'  => $this->wilaya->id_wilaya,
                    'code'       => $this->wilaya->code,
                    'nom_wilaya' => $this->wilaya->nom_wilaya,
                ];
            }),
        ];
    }
}