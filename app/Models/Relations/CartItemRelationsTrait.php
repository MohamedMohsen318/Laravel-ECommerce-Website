<?php

namespace App\Models\Relations;

use App\Models\Cart;
use App\Models\Item;
use App\Models\ItemVariant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait CartItemRelationsTrait
{
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function itemVariant(): BelongsTo
    {
        return $this->belongsTo(ItemVariant::class);
    }
}
