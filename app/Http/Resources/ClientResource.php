<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_client'       => $this->id_client,
            'nom_prenom'      => $this->nom_prenom,
            'date_naissance'  => $this->date_naissance?->format('Y-m-d'),
            'age'             => $this->age,                  // accesseur calculé du Model
            'tel_portable'    => $this->tel_portable,
            'tel_fixe'        => $this->tel_fixe,
            'email'           => $this->email,
            'commune'         => $this->commune,
            'adresse'         => $this->adresse,
            'profession'      => $this->profession,
            'remarque'        => $this->remarque,
            'client_depuis'   => $this->client_depuis?->format('Y-m-d'),
            'derniere_visite' => $this->derniere_visite?->format('Y-m-d H:i:s'),

            'id_wilaya' => $this->id_wilaya,
            'wilaya'    => $this->whenLoaded('wilaya', function () {
                return [
                    'id_wilaya'  => $this->wilaya->id_wilaya,
                    'code'       => $this->wilaya->code,
                    'nom_wilaya' => $this->wilaya->nom_wilaya,
                ];
            }),

            'created_by' => $this->created_by,
            'createur'   => $this->whenLoaded('createur', function () {
                return [
                    'id_util'    => $this->createur->id_util,
                    'nom_prenom' => $this->createur->nom_prenom ?? null,
                ];
            }),

            'nb_ventes'       => $this->whenCounted('ventes'),
            'nb_ordonnances'  => $this->whenCounted('ordonnances'),
        ];
    }
}