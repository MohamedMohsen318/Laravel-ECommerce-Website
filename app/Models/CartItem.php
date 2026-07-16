<?php

namespace App\Models;

use App\Models\Relations\CartItemRelationsTrait;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use CartItemRelationsTrait;

    protected $fillable = ['cart_id', 'item_id', 'quantity', 'price', 'options'];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'options' => 'array',
    ];

    public function getTotalAttribute(): float
    {
        return (float) $this->price * (int) $this->quantity;
    }
}
