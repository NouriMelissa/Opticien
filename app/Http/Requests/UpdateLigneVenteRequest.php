<?php

namespace App\Http\Requests;

use App\Enums\TypeLigneVente;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLigneVenteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'type_ligne'     => ['sometimes', Rule::enum(TypeLigneVente::class)],
            'description'    => 'nullable|string|max:255',
            'qte'            => 'sometimes|integer|min:1',
            'prix_unitaire'  => 'sometimes|numeric|min:0',
            'remise_pct'     => 'numeric|min:0|max:100',
            'id_article'     => 'nullable|exists:articles,id_article',
            'id_tarif_verre' => 'nullable|exists:tarif_verres,id_tarif_verre',
            'id_type_verre'  => 'nullable|exists:type_verres,id_type_verre',
        ];
    }

    public function messages(): array
    {
        return [
            'qte.min'               => 'La quantité doit être au moins 1.',
            'prix_unitaire.min'     => 'Le prix unitaire ne peut pas être négatif.',
            'remise_pct.max'        => 'La remise ne peut pas dépasser 100%.',
            'id_article.exists'     => 'Cet article n\'existe pas.',
            'id_tarif_verre.exists' => 'Ce tarif de verre n\'existe pas.',
            'id_type_verre.exists'  => 'Ce type de verre n\'existe pas.',
        ];
    }
}