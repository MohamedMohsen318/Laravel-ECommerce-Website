<?php

namespace App\Models\Traits;

use App\Enums\CartStatus;
use App\Models\Cart;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasCartTrait
{
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function activeCart(): HasOne
    {
        return $this->hasOne(Cart::class)
            ->where('status', CartStatus::ACTIVE)
            ->latest();
    }

    public function getOrCreateCart(): Cart
    {
        return $this->activeCart ?? Cart::create([
            'user_id' => $this->id,
            'status'  => CartStatus::ACTIVE,
        ]);
    }

    public function getCartItemsCount(): int
    {
        return (int) ($this->activeCart?->items()->sum('quantity') ?? 0);
    }
}
