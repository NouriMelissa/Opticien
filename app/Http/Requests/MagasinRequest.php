<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MagasinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'libelle' => 'required|string|max:100',
            'adresse' => 'required|string|max:255',
            'actif' => 'required|boolean'
        ];
    }

    public function messages()
    {
        return [
            'libelle.required' => 'Le libellé est obligatoire.',
            'adresse.required' => 'L\'adresse est obligatoire.',
            'actif.required' => 'Le statut est obligatoire.',
            'actif.boolean' => 'Le statut doit être vrai ou faux.'
        ];
    }
}