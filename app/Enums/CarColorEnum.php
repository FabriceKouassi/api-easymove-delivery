<?php

namespace App\Enums;

enum CarColorEnum: string
{
    case BLACK = 'noir';
    case WHITE = 'blanc';
    case RED = 'rouge';
    case YELLOW = 'jaune';
    case GREY = 'gris';
    case BLUE = 'bleu';

    public function label(): string
    {
        return match ($this) {
            self::BLACK => 'Noir',
            self::RED => 'Rouge',
            self::WHITE => 'Blanc',
            self::YELLOW => 'Jaune',
            self::GREY => 'Gris',
            self::BLUE => 'Blue',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
