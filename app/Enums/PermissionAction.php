<?php

namespace App\Enums;

enum PermissionAction: string{
    case Create = 'create';
    case Edit = 'edit';
    case Delete = 'delete';
    case View = 'view';

    public static function values(): array
    {
        return collect(self::cases())
            ->pluck('value')
            ->all();
    }
}
