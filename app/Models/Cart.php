<?php

namespace App\Models;

use App\Enums\CartStatus;
use App\Models\Relations\CartRelationsTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use CartRelationsTrait;

    protected $fillable = ['user_id', 'session_id', 'discount_code', 'discount_amount', 'status', 'expires_at'];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'status' => CartStatus::class,
        'expires_at' => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', CartStatus::ACTIVE);
    }

    public function scopeAbandoned(Builder $query): Builder
    {
        return $query->where('status', CartStatus::ABANDONED);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->items->sum('total');
    }

    public function getTotalAttribute(): float
    {
        return max(0, $this->subtotal - (float) $this->discount_amount);
    }

    public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }

    public function clearDiscount(): void
    {
        $this->update(['discount_code' => null, 'discount_amount' => 0]);
    }

    public function markAsAbandoned(): void
    {
        $this->update(['status' => CartStatus::ABANDONED]);
    }
}
