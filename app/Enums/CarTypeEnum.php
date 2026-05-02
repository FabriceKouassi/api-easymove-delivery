<?php

namespace App\Enums;

enum CarTypeEnum: string
{
    case MOTO = 'moto';
    case CAMION = 'camion';
    case TRICYCLE = 'tricycle';

    public function label(): string
    {
        return match ($this) {
            self::MOTO => 'Moto',
            self::TRICYCLE => 'Tricycle',
            self::CAMION => 'Camion',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
