<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case CLIENT = 'client';
    case CONDUCTEUR = 'conducteur';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrateur',
            self::CLIENT => 'Client',
            self::CONDUCTEUR => 'Conducteur',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
