<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WilayaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_wilaya'  => $this->id_wilaya,
            'code'       => $this->code,
            'nom_wilaya' => $this->nom_wilaya,

            'nb_medecins'     => $this->whenCounted('medecins'),
            
            // 'nb_clients'      => $this->whenCounted('clients'),
            // 'nb_fournisseurs' => $this->whenCounted('fournisseurs'),
            // 'nb_communes'     => $this->whenCounted('communes'),
            'nb_clients' => 0,
        'nb_fournisseurs' => 0,
        'nb_communes' => 0,
        ];
    }
}