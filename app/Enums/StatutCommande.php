<?php

namespace App\Enums;

enum StatutCommande: string
{
    case EnAttente = 'en_attente';
    case Recue = 'recue';
    case Annulee = 'annulee';

    public function label(): string
    {
        return match ($this) {
            self::EnAttente => 'En attente',
            self::Recue => 'Reçue',
            self::Annulee => 'Annulée',
        };
    }
}