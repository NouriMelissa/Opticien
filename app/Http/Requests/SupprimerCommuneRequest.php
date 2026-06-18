<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupprimerCommuneRequest extends FormRequest
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
            'id_util.required' => 'L\'utilisateur qui supprime la commune est obligatoire.',
            'id_util.exists'   => 'Cet utilisateur n\'existe pas.',
        ];
    }
}