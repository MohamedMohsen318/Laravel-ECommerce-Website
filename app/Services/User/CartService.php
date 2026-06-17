<?php

namespace App\Services\User;

use App\Enums\CartStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function getCart(): Cart
    {
        if (Auth::check()) {
            return Auth::user()->getOrCreateCart();
        }

        $sessionId = Session::getId();

        return Cart::firstOrCreate(
            [
                'session_id' => $sessionId,
                'user_id'    => null,
                'status'     => CartStatus::ACTIVE,
            ],
            [
                'session_id' => $sessionId,
                'status'     => CartStatus::ACTIVE,
            ]
        );
    }

    public function addItem(int $itemId, int $quantity = 1, array $options = []): CartItem
    {
        $cart = $this->getCart();
        $item = Item::findOrFail($itemId);

        $existingItem = $cart->items()->where('item_id', $itemId)->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $quantity);
            return $existingItem->fresh();
        }

        return $cart->items()->create([
            'item_id'  => $item->id,
            'quantity' => $quantity,
            'price'    => $item->price,
            'options'  => $options,
        ]);
    }

    public function updateItem(int $cartItemId, int $quantity): ?CartItem
    {
        $item = $this->getCart()->items()->findOrFail($cartItemId);

        if ($quantity <= 0) {
            $item->delete();
            return null;
        }

        $item->update([
            'quantity' => $quantity,
        ]);

        return $item->fresh();
    }

    public function removeItem(int $cartItemId): bool
    {
        return (bool) $this->getCart()
            ->items()
            ->where('id', $cartItemId)
            ->delete();
    }

    public function clearCart(): bool
    {
        $cart = $this->getCart();

        $cart->items()->delete();
        $cart->clearDiscount();

        return true;
    }

    public function applyCoupon(string $code): array
    {
        return [
            'success' => false,
            'message' => 'Invalid coupon code',
        ];
    }

    public function removeCoupon(): bool
    {
        return $this->getCart()->clearDiscount();
    }

    public function mergeGuestCart(?string $sessionId = null): void
    {
        if (!Auth::check()) {
            return;
        }

        $sessionId ??= Session::getId();

        $guestCart = Cart::where('session_id', $sessionId)
            ->active()
            ->first();

        if (!$guestCart || $guestCart->isEmpty()) {
            return;
        }

        $userCart = Auth::user()->getOrCreateCart();

        DB::transaction(function () use ($guestCart, $userCart) {
            foreach ($guestCart->items as $guestItem) {
                $existingItem = $userCart->items()
                    ->where('item_id', $guestItem->item_id)
                    ->first();

                if ($existingItem) {
                    $existingItem->increment('quantity', $guestItem->quantity);
                    continue;
                }

                $userCart->items()->create([
                    'item_id'  => $guestItem->item_id,
                    'quantity' => $guestItem->quantity,
                    'price'    => $guestItem->price,
                    'options'  => $guestItem->options,
                ]);
            }

            $guestCart->items()->delete();
            $guestCart->markAsAbandoned();
        });
    }
}
