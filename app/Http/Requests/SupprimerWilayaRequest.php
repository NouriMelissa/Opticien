<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * La suppression d'une wilaya est très sensible :
 * elle est référencée par medecins, clients, fournisseurs, communes.
 * On exige l'identité de l'utilisateur (traçabilité) avant de tenter
 * la suppression, qui sera bloquée côté Service si des dépendances existent.
 */
class SupprimerWilayaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'id_util' => 'required|exists:utilisateurs,id_util',
        ];
    }

    public function messages(): array
    {
        return [
            'id_util.required' => 'L\'utilisateur qui supprime la wilaya est obligatoire.',
            'id_util.exists'   => 'Cet utilisateur n\'existe pas.',
        ];
    }
}