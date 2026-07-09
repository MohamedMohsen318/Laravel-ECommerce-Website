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
        'has_variants',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_discount' => 'boolean',
        'has_variants' => 'boolean',
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

    public function averageRating(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function reviewsCount(): int
    {
        return $this->reviews()->count();
    }

    public function getEffectivePriceAttribute(): float
    {
        if ($this->has_variants) {
            return (float) ($this->variants()
                ->where('is_active', true)
                ->get()
                ->min('effective_price') ?? 0);
        }

        return (float) ($this->discount_price ?? $this->price);
    }

    public function getEffectiveStockAttribute(): int
    {
        if ($this->has_variants) {
            return (int) $this->variants()
                ->where('is_active', true)
                ->sum('stock');
        }

        return (int) $this->stock;
    }

    public function isInStock(): bool
    {
        return $this->effective_stock > 0;
    }
}
