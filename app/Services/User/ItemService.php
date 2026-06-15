<?php

namespace App\Services\User;

use App\Models\Item;
use App\Enums\ItemStatus;

class ItemService
{
    public function getAllAvailable()
    {
        return Item::where('status', ItemStatus::Available)
            ->orderByDesc('id')
            ->get();
    }

    public function findAvailable(int $id): ?Item
    {
        return Item::where('id', $id)
            ->where('status', ItemStatus::Available)
            ->first();
    }
}
