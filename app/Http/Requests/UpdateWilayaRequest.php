<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWilayaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // unique:wilayas,code,{id} → ignore la wilaya en cours d'édition
        $idWilaya = $this->route('wilaya');

        return [
            'code'       => "sometimes|required|string|size:2|unique:wilayas,code,{$idWilaya},id_wilaya",
            'nom_wilaya' => 'sometimes|required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required'       => 'Le code de la wilaya est obligatoire.',
            'code.size'           => 'Le code doit contenir exactement 2 caractères.',
            'code.unique'         => 'Ce code de wilaya existe déjà.',
            'nom_wilaya.required' => 'Le nom de la wilaya est obligatoire.',
            'nom_wilaya.max'      => 'Le nom ne peut pas dépasser 100 caractères.',
        ];
    }
}