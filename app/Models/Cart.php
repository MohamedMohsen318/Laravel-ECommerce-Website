<?php

namespace App\Models;

use App\Enums\CartStatus;
use App\Models\Relations\CartRelationsTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory, CartRelationsTrait;

    protected $fillable = [
        'user_id',
        'session_id',
        'coupon_code',
        'discount_amount',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'status' => CartStatus::class,
        'expires_at' => 'datetime',
        'discount_amount' => 'decimal:2',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', CartStatus::ACTIVE);
    }

    public function scopeAbandoned(Builder $query): Builder
    {
        return $query->where('status', CartStatus::ABANDONED);
    }

    public function isEmpty(): bool
    {
        return $this->items()->count() === 0;
    }

    public function getSubtotalAttribute(): float
    {
        $items = $this->relationLoaded('items')
            ? $this->items
            : $this->items()->get();

        return (float) $items->sum(fn (CartItem $item) => $item->total);
    }

    public function getTotalAttribute(): float
    {
        return max(0, $this->subtotal - (float) $this->discount_amount);
    }

    public function clearDiscount(): bool
    {
        return $this->update([
            'coupon_code' => null,
            'discount_amount' => 0,
        ]);
    }

    public function markAsAbandoned(): bool
    {
        return $this->update([
            'status' => CartStatus::ABANDONED,
        ]);
    }
}
