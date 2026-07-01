<?php

namespace App\Models\Relations;

use App\Models\Discount;
use App\Models\DiscountUsage;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait DiscountRelations
{
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(DiscountUsage::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'discount_usages')
            ->withPivot('discount_amount', 'order_id')
            ->withTimestamps();
    }

    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(Discount::class, 'discount_usages')
            ->withPivot('discount_amount', 'order_id')
            ->withTimestamps();
    }

    public function discountUsages(): HasMany
    {
        return $this->hasMany(DiscountUsage::class);
    }
}
