<?php

namespace App\Models\Relations;

use App\Models\Category;
use App\Models\ProductComment;
use App\Models\ProductReview;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait ItemRelationsTrait
{
    public function categories(): BelongsToMany{
        return $this->belongsToMany(
            related: Category::class,
            table: 'category_item'
        );
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ProductComment::class)
            ->whereNull('parent_id')
            ->with('replies', 'user')
            ->latest();
    }
}
