<?php

namespace App\Models\Traits;

use App\Models\AttributeValue;

trait ItemAttributesTrait
{
    protected $appends = [
        'effective_price',
        'effective_stock',
        'has_variants',
        'options_label',
    ];

    public function getEffectivePriceAttribute(): ?float
    {
        if (is_null($this->price)) {
            return null;
        }

        return (float) ($this->discount_price ?? $this->price);
    }

    public function inStock(): bool
    {
        return $this->is_active && $this->effective_stock > 0;
    }

    public function getHasVariantsAttribute(): bool
    {
        return $this->type === 'variant';
    }

    public function getEffectiveStockAttribute(): int
    {
        if ($this->type !== 'variant') {
            return (int) $this->stock;
        }

        return (int) $this->children()
            ->where('is_active', true)
            ->sum('stock');
    }

    public function getOptionsLabelAttribute(): string
    {
        return $this->attributeValues()
            ->with('attribute')
            ->get()
            ->map(function (AttributeValue $value) {
                return "{$value->attribute?->name}: {$value->value}";
            })
            ->filter()
            ->join(' / ');
    }
}
