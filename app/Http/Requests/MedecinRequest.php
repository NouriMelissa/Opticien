<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class MedecinRequest extends FormRequest
{
    /**
     * Tout utilisateur authentifié peut gérer les médecins
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation
     * Utilisées pour STORE (création) ET UPDATE (modification)
     */
    public function rules(): array
    {
        return [
            'nom_prenom'  => ['required', 'string', 'max:150'],
            'specialite'  => ['nullable', 'string', 'max:100'],
            'tel'         => ['nullable', 'string', 'max:15', 'regex:/^[0-9\s\+\-\.]+$/'],
            'adresse'     => ['nullable', 'string', 'max:255'],
            'id_wilaya'   => ['nullable', 'integer', 'exists:wilayas,id_wilaya'],
        ];
    }

    /**
     * Messages d'erreur personnalisés en français
     */
    public function messages(): array
    {
        return [
            'nom_prenom.required' => 'Le nom du médecin est obligatoire.',
            'nom_prenom.max'      => 'Le nom ne peut pas dépasser 150 caractères.',
            'specialite.max'      => 'La spécialité ne peut pas dépasser 100 caractères.',
            'tel.max'             => 'Le téléphone ne peut pas dépasser 15 caractères.',
            'tel.regex'           => 'Le format du téléphone est invalide.',
            'adresse.max'         => 'L\'adresse ne peut pas dépasser 255 caractères.',
            'id_wilaya.integer'   => 'La wilaya doit être un identifiant numérique.',
            'id_wilaya.exists'    => 'La wilaya sélectionnée n\'existe pas.',
        ];
    }

    /**
     * Noms lisibles des champs (pour les messages d'erreur génériques)
     */
    public function attributes(): array
    {
        return [
            'nom_prenom' => 'nom du médecin',
            'specialite' => 'spécialité',
            'tel'        => 'téléphone',
            'adresse'    => 'adresse',
            'id_wilaya'  => 'wilaya',
        ];
    }

    /**
     * Si la validation échoue → retourner JSON au lieu de redirect
     * Important pour une API REST consommée par Angular
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Erreur de validation.',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}