<?php

namespace App\Models;

use App\Models\Relations\ItemRelationsTrait;
use App\Models\Traits\HasMediaTrait;
use App\Models\Traits\HasTranslationsTrait;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    // FIX #13: أضفنا ItemRelationsTrait اللي كان مش بيتستخدم
    use HasTranslationsTrait,
        HasMediaTrait,
        ItemRelationsTrait;

    protected $table = 'items';

    protected $fillable = [
        'price',
        'discount_price',
        'is_active',
        'is_discount',
        'status',
        'stock',
        'sku',
    ];
}
