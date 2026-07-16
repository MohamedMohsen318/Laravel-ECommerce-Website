<?php

namespace App\Models\Relations;

use App\Models\Cart;
use App\Models\Order;
use App\Models\ProductComment;
use App\Models\ProductReview;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait UserRelationsTrait
{
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class)->latestOfMany();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ProductComment::class);
    }

    public function getOrCreateCart(): Cart
    {
        return $this->carts()->firstOrCreate([
            'status' => \App\Enums\CartStatus::ACTIVE,
        ]);
    }
}
