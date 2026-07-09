<?php

namespace App\Models\Relations;

use App\Models\ItemOption;
use App\Models\ItemVariant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait ItemOptionValueRelationsTrait
{
    public function option(): BelongsTo
    {
        return $this->belongsTo(ItemOption::class, 'item_option_id');
    }

    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(
            related: ItemVariant::class,
            table: 'item_variant_option_value'
        );
    }
}
