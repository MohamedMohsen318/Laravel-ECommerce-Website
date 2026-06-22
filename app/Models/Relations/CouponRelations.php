<?php

namespace App\Models\Relations;

use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait CouponRelations
{
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'coupon_usages')
            ->withPivot('discount_amount', 'order_id')
            ->withTimestamps();
    }
}
