<?php

namespace App\Http\Requests\Utilisateur;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUtilisateurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'nom' =>
                'sometimes|string|max:100',

            'prenom' =>
                'sometimes|string|max:100',

            'email' =>
                'sometimes|email',

            'tel_portable' =>
                'nullable|string|max:20',

            'role' =>
                'sometimes',

            'actif' =>
                'sometimes|boolean',

            'id_wilaya' =>
                'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [

            'email.email' =>
                'Format email invalide',

            'actif.boolean' =>
                'Actif doit être true ou false',
        ];
    }
}