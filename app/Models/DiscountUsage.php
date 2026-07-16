<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountUsage extends Model
{
    protected $fillable = ['user_id', 'discount_id', 'order_id', 'discount_amount'];

    protected $casts = ['discount_amount' => 'decimal:2'];
}
