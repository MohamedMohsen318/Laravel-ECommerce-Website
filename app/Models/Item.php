<?php

namespace App\Models;

use App\Enums\ItemStatus;
use App\Models\Relations\ItemRelationsTrait;
use App\Models\Traits\HasMediaTrait;
use App\Models\Traits\HasTranslationsTrait;
use App\Models\Traits\ItemAttributesTrait;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use ItemRelationsTrait;
    use HasMediaTrait;
    use HasTranslationsTrait;
    use ItemAttributesTrait;

    protected $fillable = [
        'type',
        'parent_id',
        'sku',
        'price',
        'discount_price',
        'stock',
        'status',
        'is_active',
        'is_discount',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'is_discount' => 'boolean',
        'status' => ItemStatus::class,
    ];
}
