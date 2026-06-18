<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWilayaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|size:2',
            'nom_wilaya' => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required'       => 'Le code de la wilaya est obligatoire.',
            'code.size'           => 'Le code doit contenir exactement 2 caractères (ex: 01, 16, 58).',
            'code.unique'         => 'Ce code de wilaya existe déjà.',
            'nom_wilaya.required' => 'Le nom de la wilaya est obligatoire.',
            'nom_wilaya.max'      => 'Le nom ne peut pas dépasser 100 caractères.',
        ];
    }
}