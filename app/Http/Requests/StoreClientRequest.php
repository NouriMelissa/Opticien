<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom_prenom'     => 'required|string|max:150',
            'date_naissance' => 'nullable|date|before:today',
            'tel_portable'   => 'nullable|string|max:15|regex:/^[0-9\s\+\-\.]+$/',
            'tel_fixe'       => 'nullable|string|max:15|regex:/^[0-9\s\+\-\.]+$/',
            'email'          => 'nullable|email|max:100',
            'id_wilaya'      => 'nullable|exists:wilayas,id_wilaya',
            'commune'        => 'nullable|string|max:100',
            'adresse'        => 'nullable|string|max:255',
            'profession'     => 'nullable|string|max:100',
            'remarque'       => 'nullable|string',
            'created_by'     => 'nullable|exists:utilisateurs,id_util',
        ];
    }

    public function messages(): array
    {
        return [
            'nom_prenom.required'     => 'Le nom du client est obligatoire.',
            'nom_prenom.max'          => 'Le nom ne peut pas dépasser 150 caractères.',
            'date_naissance.date'     => 'La date de naissance est invalide.',
            'date_naissance.before'   => 'La date de naissance doit être dans le passé.',
            'tel_portable.regex'      => 'Le format du téléphone portable est invalide.',
            'tel_fixe.regex'          => 'Le format du téléphone fixe est invalide.',
            'email.email'             => 'L\'adresse email est invalide.',
            'id_wilaya.exists'        => 'Cette wilaya n\'existe pas.',
            'created_by.exists'       => 'Cet utilisateur créateur n\'existe pas.',
        ];
    }
}