<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Requête utilisée pour la suppression d'un médecin.
 *
 * Même logique que ValiderVenteRequest : on identifie l'utilisateur
 * qui effectue l'action (traçabilité), même si la suppression
 * elle-même est ensuite bloquée côté Service si des ordonnances existent.
 */
class SupprimerMedecinRequest extends FormRequest
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
            'id_util.required' => 'L\'utilisateur qui supprime le médecin est obligatoire.',
            'id_util.exists'   => 'Cet utilisateur n\'existe pas.',
        ];
    }
}