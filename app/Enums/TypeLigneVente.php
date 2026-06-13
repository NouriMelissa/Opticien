<?php

namespace App\Enums;

enum TypeLigneVente: string
{
    case VerreLoin = 'verre_loin';
    case VerrePres = 'verre_pres';
    case MontureLoin = 'monture_loin';
    case MonturePres = 'monture_pres';
    case Accessoire = 'accessoire';

    public function label(): string
    {
        return match ($this) {
            self::VerreLoin => 'Verre de loin',
            self::VerrePres => 'Verre de près',
            self::MontureLoin => 'Monture de loin',
            self::MonturePres => 'Monture de près',
            self::Accessoire => 'Accessoire',
        };
    }

    public function estVerre(): bool
    {
        return $this === self::VerreLoin || $this === self::VerrePres;
    }

    public function estMonture(): bool
    {
        return $this === self::MontureLoin || $this === self::MonturePres;
    }
}