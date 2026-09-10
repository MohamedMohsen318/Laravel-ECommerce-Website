<?php

namespace App\Models\Relations;

use App\Models\Item;
use App\Models\AttributeValue;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait ItemAttributeRelationsTrait
{
    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(
            Item::class,
            'item_attribute_values',
            'attribute_id',
            'item_id'
        )->distinct();
    }
}
