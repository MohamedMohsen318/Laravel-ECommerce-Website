<?php

namespace App\Models\Traits;

use App\Models\ItemAttributeValue;

trait ItemAttributesTrait
{
    protected $appends = ['effective_price', 'options_label'];

    public function getEffectivePriceAttribute(): ?float
    {
        if (is_null($this->price)) {
            return null;
        }

        return (float) ($this->discount_price ?? $this->price);
    }

    public function inStock(): bool
    {
        return $this->is_active && (int) $this->stock > 0;
    }

    public function getOptionsLabelAttribute(): string
    {
        return $this->attributeValues
            ->map(fn (ItemAttributeValue $value) => $value->attribute?->name . ': ' . $value->value)
            ->filter()
            ->join(' / ');
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

        return (int) $this->children->where('is_active', true)->sum('stock');
    }
}
