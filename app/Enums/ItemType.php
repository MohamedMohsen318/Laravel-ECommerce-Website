<?php

namespace App\Enums;

enum ItemType: string
{
    case Simple = 'simple';
    case Variant = 'variant';
    case Compo = 'compo';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
