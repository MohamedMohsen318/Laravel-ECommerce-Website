<?php

namespace App\Models;

use App\Enums\DiscountType;
use App\Models\Relations\DiscountRelations;
use App\Models\Traits\HasDiscounts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use SoftDeletes;
    use DiscountRelations;
    use HasDiscounts;


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
        'type' => DiscountType::class,
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
