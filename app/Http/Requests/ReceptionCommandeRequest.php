<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReceptionCommandeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_util'                    => 'required|exists:utilisateurs,id_util',
            'date_reception'             => 'required|date',
            'lignes'                     => 'required|array|min:1',
            'lignes.*.id_ligne_commande' => 'required|exists:ligne_commandes,id_ligne_commande',
            'lignes.*.qte_recue'         => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'id_util.required'                    => 'L\'utilisateur qui réceptionne est obligatoire.',
            'id_util.exists'                      => 'Cet utilisateur n\'existe pas.',
            'date_reception.required'             => 'La date de réception est obligatoire.',
            'date_reception.date'                 => 'La date de réception doit être une date valide.',
            'lignes.required'                     => 'Les lignes de réception sont obligatoires.',
            'lignes.min'                          => 'Au moins une ligne doit être réceptionnée.',
            'lignes.*.id_ligne_commande.required' => 'L\'identifiant de la ligne est obligatoire.',
            'lignes.*.id_ligne_commande.exists'   => 'Cette ligne de commande n\'existe pas.',
            'lignes.*.qte_recue.required'         => 'La quantité reçue est obligatoire.',
            'lignes.*.qte_recue.min'              => 'La quantité reçue ne peut pas être négative.',
            'lignes.*.qte_recue.integer'          => 'La quantité reçue doit être un nombre entier.',
        ];
    }
}