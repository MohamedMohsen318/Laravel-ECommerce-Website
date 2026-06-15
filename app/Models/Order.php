<?php

namespace App\Models;

use App\Models\Relations\OrderRelationsTrait;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use OrderRelationsTrait;
    protected $fillable = [
        'user_id',
        'status',
        'total_price',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
    ];


}
