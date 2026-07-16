<?php

namespace App\Models\Relations;

use App\Models\ItemAttributeValue;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait ItemAttributeRelationsTrait
{
    public function values(): HasMany
    {
        return $this->hasMany(ItemAttributeValue::class);
    }
}
