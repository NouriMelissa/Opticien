<?php

namespace App\Enums;

enum ModePaiement: string
{
    case Especes = 'especes';
    case Tpe = 'tpe';
    case Mixte = 'mixte';

    public function label(): string
    {
        return match ($this) {
            self::Especes => 'Espèces',
            self::Tpe => 'TPE (carte)',
            self::Mixte => 'Mixte',
        };
    }
}