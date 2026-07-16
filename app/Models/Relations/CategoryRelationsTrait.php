<?php

namespace App\Models\Relations;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait CategoryRelationsTrait
{
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'category_item');
    }
}
