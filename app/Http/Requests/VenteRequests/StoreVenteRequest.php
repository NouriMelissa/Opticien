<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVenteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_client' => 'required|exists:clients,id_client',
            'id_session' => 'required|exists:session_shifts,id_session',
            'id_ordonnance' => 'nullable|exists:ordonnances,id_ordonnance',
            'remise_globale_pct' => 'numeric|min:0|max:100',
            'mode_paiement' => 'required|in:especes,tpe,mixte',
            'date_livraison_prevue' => 'nullable|date',
            'note' => 'nullable|string',

            'lignes' => 'required|array|min:1',
            'lignes.*.type_ligne' => 'required|in:verre_loin,verre_pres,monture_loin,monture_pres,accessoire',
            'lignes.*.description' => 'nullable|string',
            'lignes.*.qte' => 'required|integer|min:1',
            'lignes.*.prix_unitaire' => 'required|numeric|min:0',
            'lignes.*.remise_pct' => 'numeric|min:0|max:100',
            'lignes.*.id_article' => 'nullable|exists:articles,id_article',
            'lignes.*.id_tarif_verre' => 'nullable|exists:tarif_verres,id_tarif_verre',
            'lignes.*.id_type_verre' => 'nullable|exists:type_verres,id_type_verre',
        ];
    }

    public function messages(): array
    {
        return [
            'id_client.required' => 'Le client est obligatoire.',
            'id_client.exists' => 'Ce client n\'existe pas.',
            'id_session.required' => 'La session de caisse est obligatoire.',
            'id_session.exists' => 'Cette session n\'existe pas.',
            'mode_paiement.required' => 'Le mode de paiement est obligatoire.',
            'mode_paiement.in' => 'Mode de paiement invalide (especes, tpe, mixte).',
            'lignes.required' => 'La vente doit contenir au moins une ligne.',
            'lignes.min' => 'La vente doit contenir au moins une ligne.',
            'lignes.*.qte.min' => 'La quantité doit être au moins 1.',
            'lignes.*.prix_unitaire.required' => 'Le prix unitaire est obligatoire pour chaque ligne.',
        ];
    }
}