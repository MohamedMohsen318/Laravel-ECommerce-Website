<?php

namespace App\Models;

use App\Models\Relations\ItemVariantRelationsTrait;
use Illuminate\Database\Eloquent\Model;

class ItemVariant extends Model
{
    use ItemVariantRelationsTrait;

    protected $fillable = [
        'item_id',
        'sku',
        'price',
        'discount_price',
        'stock',
        'is_active',
    ];
    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];
    protected $appends = ['effective_price', 'options_label',];
    public function getEffectivePriceAttribute(): float{
        return (float) ($this->discount_price ?? $this->price);
    }
    public function inStock(): bool{
        return $this->is_active && $this->stock > 0;
    }
    public function getOptionsLabelAttribute(): string{
        return $this->optionValues
            ->map(fn (ItemOptionValue $value) => $value->option?->name . ': ' . $value->value)
            ->filter()
            ->join(' / ');
    }
}
