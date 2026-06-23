<?php

namespace App\Models;

use App\Enums\CouponType;
use App\Models\Relations\CouponRelations;
use App\Models\Traits\HasCoupons;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;
    use CouponRelations;
    use HasCoupons;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'is_active',
        'starts_at',
        'expires_at',
        'max_uses',
        'max_uses_per_user',
        'used_count',
    ];

    protected $casts = [
        'type' => CouponType::class,
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'max_uses' => 'integer',
        'max_uses_per_user' => 'integer',
        'used_count' => 'integer',
    ];
}
