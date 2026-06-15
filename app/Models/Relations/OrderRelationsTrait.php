<?php

namespace App\Models\Relations;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait OrderRelationsTrait
{
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
