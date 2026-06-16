<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommandeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'date_commande'              => 'required|date',
            'note'                       => 'nullable|string',
            'id_fournisseur'             => 'required|exists:fournisseurs,id_fournisseur',
            'id_util'                    => 'required|exists:utilisateurs,id_util',

            'lignes'                     => 'required|array|min:1',
            'lignes.*.qte_commandee'     => 'required|integer|min:1',
            'lignes.*.prix_unitaire'     => 'required|numeric|min:0',
            'lignes.*.id_article'        => 'nullable|exists:articles,id_article',
            'lignes.*.id_tarif_verre'    => 'nullable|exists:tarif_verres,id_tarif_verre',
        ];
    }

    public function messages(): array
    {
        return [
            'date_commande.required'          => 'La date de commande est obligatoire.',
            'id_fournisseur.required'          => 'Le fournisseur est obligatoire.',
            'id_fournisseur.exists'            => 'Ce fournisseur n\'existe pas.',
            'id_util.required'                 => 'L\'utilisateur est obligatoire.',
            'lignes.required'                  => 'La commande doit contenir au moins une ligne.',
            'lignes.*.qte_commandee.required'  => 'La quantité commandée est obligatoire.',
            'lignes.*.prix_unitaire.required'  => 'Le prix unitaire est obligatoire.',
        ];
    }
}