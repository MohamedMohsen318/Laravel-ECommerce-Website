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

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class)->latestOfMany();
    }

    public function getOrCreateCart(): Cart
    {
        return $this->carts()->firstOrCreate([
            'status' => CartStatus::ACTIVE,
        ]);
    }
}
