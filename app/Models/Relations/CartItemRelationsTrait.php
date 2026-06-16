<?php

namespace App\Models\Relations;

use App\Models\Cart;
use App\Models\Item;
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
}
