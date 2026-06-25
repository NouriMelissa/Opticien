<?php

namespace App\Http\Requests;

use App\Enums\TypeLigneVente;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLigneVenteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'type_ligne'     => ['required', Rule::enum(TypeLigneVente::class)],
            'description'    => 'nullable|string|max:255',
            'qte'            => 'required|integer|min:1',
            'prix_unitaire'  => 'required|numeric|min:0',
            'remise_pct'     => 'numeric|min:0|max:100',
            'id_vente'       => 'required|exists:ventes,id_vente',
            'id_article'     => 'nullable|exists:articles,id_article',
            'id_tarif_verre' => 'nullable|exists:tarif_verres,id_tarif_verre',
            'id_type_verre'  => 'nullable|exists:type_verres,id_type_verre',
        ];
    }

    public function messages(): array
    {
        return [
            'type_ligne.required'    => 'Le type de ligne est obligatoire.',
            'qte.required'           => 'La quantité est obligatoire.',
            'qte.min'                => 'La quantité doit être au moins 1.',
            'qte.integer'            => 'La quantité doit être un nombre entier.',
            'prix_unitaire.required' => 'Le prix unitaire est obligatoire.',
            'prix_unitaire.min'      => 'Le prix unitaire ne peut pas être négatif.',
            'remise_pct.max'         => 'La remise ne peut pas dépasser 100%.',
            'id_vente.required'      => 'La vente est obligatoire.',
            'id_vente.exists'        => 'Cette vente n\'existe pas.',
            'id_article.exists'      => 'Cet article n\'existe pas.',
            'id_tarif_verre.exists'  => 'Ce tarif de verre n\'existe pas.',
            'id_type_verre.exists'   => 'Ce type de verre n\'existe pas.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $hasArticle = !empty($this->id_article);
            $hasTarif   = !empty($this->id_tarif_verre);

            if ($hasArticle && $hasTarif) {
                $v->errors()->add(
                    'id_article',
                    'Une ligne ne peut pas référencer à la fois un article et un tarif de verre.'
                );
            }

            if (!$hasArticle && !$hasTarif) {
                $v->errors()->add(
                    'id_article',
                    'Il faut renseigner soit id_article (monture/accessoire) soit id_tarif_verre (verre).'
                );
            }
        });
    }
}