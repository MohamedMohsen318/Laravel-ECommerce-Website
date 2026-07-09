<?php

namespace App\Models\Relations;

use App\Models\ItemOptionValue;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait ItemOptionRelationsTrait
{
    public function values(): HasMany
    {
        return $this->hasMany(ItemOptionValue::class);
    }
}
