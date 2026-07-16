<?php

namespace App\Models\Traits;

use App\Models\Item;
use Illuminate\Validation\ValidationException;

trait FindsPurchasableItemTrait
{
    protected function findPurchasableItem(int $itemId): Item
    {
        $item = Item::findOrFail($itemId);

        if ($item->type === 'variant') {
            throw ValidationException::withMessages([
                'item_id' => 'Please select a product variant before ordering.',
            ]);
        }

        if (! $item->is_active) {
            throw ValidationException::withMessages([
                'item_id' => 'This product is not available.',
            ]);
        }

        return $item;
    }
}
