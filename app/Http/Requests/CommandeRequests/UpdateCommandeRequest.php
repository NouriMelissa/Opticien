<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommandeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'date_commande'  => 'sometimes|date',
            'note'           => 'nullable|string',
            'id_fournisseur' => 'sometimes|exists:fournisseurs,id_fournisseur',
        ];
    }

    public function messages(): array
    {
        return [
            'id_fournisseur.exists' => 'Ce fournisseur n\'existe pas.',
        ];
    }
}