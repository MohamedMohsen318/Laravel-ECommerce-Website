<?php


namespace App\Models\Relations;

use App\Models\Item;
use App\Models\ItemVariant;
use App\Models\Order;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait OrderItemRelationsTrait
{
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function itemVariant(): BelongsTo
    {
        return $this->belongsTo(ItemVariant::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
