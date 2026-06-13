<?php

namespace App\Models\Relations;

use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// FIX #13: الـ trait مكانش فاضي - أضفنا علاقة الـ categories
trait ItemRelationsTrait
{
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Category::class,
            table: 'category_item'
        );
    }
}
