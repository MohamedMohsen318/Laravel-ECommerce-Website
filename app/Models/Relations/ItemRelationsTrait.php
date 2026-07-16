<?php

namespace App\Models\Relations;

use App\Models\Category;
use App\Models\CartItem;
use App\Models\Item;
use App\Models\ItemAttributeValue;
use App\Models\OrderItem;
use App\Models\ProductComment;
use App\Models\ProductReview;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait ItemRelationsTrait
{
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Item::class, 'parent_id');
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(ItemAttributeValue::class, 'item_attribute_value_item');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, table: 'category_item');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)->latest();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ProductComment::class)
            ->whereNull('parent_id')
            ->with('replies', 'user')
            ->latest();
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
