<?php

namespace App\Models;

use App\Enums\ItemStatus;
use App\Models\Relations\ItemRelationsTrait;
use App\Models\Traits\HasMediaTrait;
use App\Models\Traits\HasTranslationsTrait;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
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

    protected $casts = [
        'is_active' => 'boolean',
        'is_discount' => 'boolean',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'status' => ItemStatus::class,
    ];

    public function getNameAttribute(): string
    {
        return $this->translate('en')?->name ?? 'Untitled product';
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->translate('en')?->description;
    }
}
