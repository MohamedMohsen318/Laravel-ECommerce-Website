<?php

namespace App\Models\Relations;

use App\Models\Item;
use App\Models\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait ItemAttributeValueRelationsTrait
{
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(
            Item::class,
            'item_attribute_values',
            'attribute_value_id',
            'item_id'
        )->withPivot('attribute_id');
    }
}
