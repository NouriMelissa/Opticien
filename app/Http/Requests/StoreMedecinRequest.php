<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedecinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles pour la CRÉATION d'un médecin
     */
    public function rules(): array
    {
        return [
            'nom_prenom' => 'required|string|max:150',
            'specialite' => 'nullable|string|max:100',
            'tel'        => 'nullable|string|max:15|regex:/^[0-9\s\+\-\.]+$/',
            'adresse'    => 'nullable|string|max:255',
            'id_wilaya'  => 'nullable|exists:wilayas,id_wilaya',
        ];
    }

    public function messages(): array
    {
        return [
            'nom_prenom.required' => 'Le nom du médecin est obligatoire.',
            'nom_prenom.max'      => 'Le nom ne peut pas dépasser 150 caractères.',
            'specialite.max'      => 'La spécialité ne peut pas dépasser 100 caractères.',
            'tel.max'             => 'Le téléphone ne peut pas dépasser 15 caractères.',
            'tel.regex'           => 'Le format du téléphone est invalide.',
            'adresse.max'         => 'L\'adresse ne peut pas dépasser 255 caractères.',
            'id_wilaya.exists'    => 'La wilaya sélectionnée n\'existe pas.',
        ];
    }
}