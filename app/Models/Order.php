<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Models\Relations\OrderRelationsTrait;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use OrderRelationsTrait;

    protected $fillable = [
        'user_id',
        'status',
        'total_price',
        'coupon_id',
        'discount_amount',
        'final_total',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'total_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_total' => 'decimal:2',
    ];
}
