<?php

namespace App\Models;

use App\Enums\CartStatus;
use App\Models\Relations\CartRelationsTrait;
use Illuminate\Database\Eloquent\Model;


class Cart extends Model
{
    use CartRelationsTrait;

    protected $fillable = [
        'user_id',
        'session_id',
        'coupon_code',
        'discount_amount',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'status' => CartStatus::class,
        'expires_at' => 'datetime',
    ];
}
