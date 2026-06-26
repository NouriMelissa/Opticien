<?php

namespace App\Http\Requests\Utilisateur;

use Illuminate\Foundation\Http\FormRequest;

class StoreUtilisateurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'nom' => 'required|string|max:100',

            'prenom' => 'required|string|max:100',

            'email' => 'required|email|unique:users,email',

            'password' => 'required|min:6',

            'tel_portable' => 'nullable|string|max:20',

            'role' => 'required',

            'actif' => 'nullable|boolean',

            'id_wilaya' => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [

            'nom.required' =>
                'Le nom est obligatoire',

            'prenom.required' =>
                'Le prénom est obligatoire',

            'email.required' =>
                'Email obligatoire',

            'email.email' =>
                'Email invalide',

            'email.unique' =>
                'Cet email existe déjà',

            'password.required' =>
                'Mot de passe obligatoire',

            'password.min' =>
                'Minimum 6 caractères',

            'role.required' =>
                'Le rôle est obligatoire',
        ];
    }
}