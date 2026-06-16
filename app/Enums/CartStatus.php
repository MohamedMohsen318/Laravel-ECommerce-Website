<?php

namespace App\Enums;

enum CartStatus: string
{
    case ACTIVE    = 'active';
    case CHECKOUT  = 'checkout';
    case COMPLETED = 'completed';
    case ABANDONED = 'abandoned';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE    => 'active',
            self::CHECKOUT  => 'checkout',
            self::COMPLETED => 'completed',
            self::ABANDONED => 'abandoned',
            self::CANCELLED => 'cancelled',
        };
    }


    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
