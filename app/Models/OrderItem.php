<?php

namespace App\Models;

use App\Models\Relations\OrderItemRelationsTrait;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use OrderItemRelationsTrait;
 protected $fillable = [
        'order_id',
        'item_id',
        'quantity',
        'price',
    ];
}
