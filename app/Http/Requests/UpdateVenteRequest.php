<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVenteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_client'             => 'sometimes|exists:clients,id_client',
            'id_ordonnance'         => 'nullable|exists:ordonnances,id_ordonnance',
            'remise_globale_pct'    => 'numeric|min:0|max:100',
            'mode_paiement'         => 'sometimes|in:especes,tpe,mixte',
            'date_livraison_prevue' => 'nullable|date',
            'note'                  => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'id_client.exists'           => 'Ce client n\'existe pas.',
            'id_ordonnance.exists'       => 'Cette ordonnance n\'existe pas.',
            'mode_paiement.in'           => 'Mode de paiement invalide. Valeurs acceptées : especes, tpe, mixte.',
            'remise_globale_pct.max'     => 'La remise globale ne peut pas dépasser 100%.',
            'remise_globale_pct.min'     => 'La remise globale ne peut pas être négative.',
            'date_livraison_prevue.date' => 'La date de livraison doit être une date valide.',
        ];
    }
}