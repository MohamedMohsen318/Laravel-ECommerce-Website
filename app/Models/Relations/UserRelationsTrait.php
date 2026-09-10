<?php

namespace App\Models\Relations;

use App\Models\Cart;
use App\Models\Order;
use App\Models\ProductComment;
use App\Models\ProductReview;
use App\Enums\CartStatus;
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
            'status' => CartStatus::ACTIVE,
        ]);
    }

    public function getCartItemsCount(): int
    {
        $cart = $this->carts()
            ->where('status', CartStatus::ACTIVE)
            ->first();

        return (int) ($cart?->items()->sum('quantity') ?? 0);
    }
}
