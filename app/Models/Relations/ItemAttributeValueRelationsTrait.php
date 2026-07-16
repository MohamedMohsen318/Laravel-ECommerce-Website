<?php

namespace App\Models\Relations;

use App\Models\Item;
use App\Models\ItemAttribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait ItemAttributeValueRelationsTrait
{
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(ItemAttribute::class, 'item_attribute_id');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_attribute_value_item');
    }
}
