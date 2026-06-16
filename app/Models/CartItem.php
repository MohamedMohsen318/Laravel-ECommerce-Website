<?php

namespace App\Models;

use App\Models\Relations\CartItemRelationsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory, CartItemRelationsTrait;

    protected $fillable = [
        'cart_id',
        'item_id',
        'quantity',
        'price',
        'options',
    ];

    protected $casts = [
        'price'   => 'decimal:2',
        'options' => 'array',
    ];
}
