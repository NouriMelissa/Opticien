<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommuneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom_commune' => 'sometimes|required|string|max:100',
            'id_wilaya'   => 'sometimes|required|exists:wilayas,id_wilaya',
        ];
    }

    public function messages(): array
    {
        return [
            'nom_commune.required' => 'Le nom de la commune est obligatoire.',
            'nom_commune.max'      => 'Le nom ne peut pas dépasser 100 caractères.',
            'id_wilaya.required'   => 'La wilaya est obligatoire.',
            'id_wilaya.exists'     => 'Cette wilaya n\'existe pas.',
        ];
    }
}