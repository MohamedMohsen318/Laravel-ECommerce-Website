<?php

namespace App\Services\User;

use App\Enums\CartStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Item;
use App\Services\DiscountException;
use App\Services\DiscountService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function getCart(): Cart{
        if (Auth::check()) {
            return Auth::user()->getOrCreateCart();
        }
        return Cart::firstOrCreate(
            ['session_id' => Session::getId(), 'user_id' => null,],
            ['status' => CartStatus::ACTIVE,]
        );
    }

    public function addItem(int $itemId, int $quantity = 1, array $options = []): CartItem{
        $cart = $this->getCart();
        $item = Item::findOrFail($itemId);
        $existingItem = $cart->items()
            ->where('item_id', $itemId)
            ->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $quantity);
            return $existingItem->fresh();
        }

        return $cart->items()->create([
            'item_id' => $item->id,
            'quantity' => $quantity,
            'price' => $item->price,
            'options' => $options,
        ]);
    }
    public function updateItem(int $cartItemId, int $quantity): ?CartItem{
        $item = $this->getCart()
            ->items()
            ->findOrFail($cartItemId);
        if ($quantity <= 0) {
            $item->delete();
            return null;
        }
        $item->update([
            'quantity' => $quantity,
        ]);
        return $item->fresh();
    }
    public function removeItem(int $cartItemId): bool{
        return (bool) $this->getCart()
            ->items()
            ->where('id', $cartItemId)
            ->delete();
    }
    public function clearCart(): bool{
        $cart = $this->getCart();
        $cart->items()->delete();
        $cart->clearDiscount();

        return true;
    }
    public function applyDiscount(string $code): array{
        $cart = $this->getCart()->load('items');
        try {
            $result = app(DiscountService::class)->validate(
                $code,
                $cart->subtotal,
                Auth::user()
            );
            $cart->update([
                'discount_code' => $result['discount']->code,
                'discount_amount' => $result['discount_amount'],
            ]);
            return [
                'success' => true,
                'message' => $result['message'],
            ];
        } catch (DiscountException $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage(),
            ];
        }
    }
    public function removeDiscount(): bool{
        return $this->getCart()->clearDiscount();
    }
    public function mergeGuestCart(?string $sessionId = null): ?string{
        if (! Auth::check()) {
            return null;
        }
        $sessionId ??= Session::getId();
        $guestCart = Cart::where('session_id', $sessionId)
            ->active()
            ->first();
        if (! $guestCart || $guestCart->isEmpty()) {
            return null;
        }
        $userCart = Auth::user()->getOrCreateCart();
        return DB::transaction(function () use ($guestCart, $userCart) {
            $previousDiscountCode = $userCart->discount_code;
            $discountCode = $userCart->discount_code ?: $guestCart->discount_code;
            $existingItems = $userCart->items()
                ->get()
                ->keyBy('item_id');
            $newItems = [];
            foreach ($guestCart->items as $guestItem) {
                $existingItem = $existingItems->get($guestItem->item_id);

                if ($existingItem) {
                    $existingItem->increment('quantity', $guestItem->quantity);

                    continue;
                }
                $newItems[] = [
                    'cart_id' => $userCart->id,
                    'item_id' => $guestItem->item_id,
                    'quantity' => $guestItem->quantity,
                    'price' => $guestItem->price,
                    'options' => $guestItem->options,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (! empty($newItems)) {
                $userCart->items()->insert($newItems);
            }
            $userCart->refresh()->load('items');
            $warning = $this->refreshMergedDiscount(
                $userCart,
                $discountCode,
                $previousDiscountCode
            );
            $guestCart->items()->delete();
            $guestCart->markAsAbandoned();
            return $warning;
        });
    }
    private function refreshMergedDiscount(Cart $cart, ?string $discountCode,?string $previousDiscountCode): ?string {
        if (! $discountCode) {
            $cart->clearDiscount();
            return null;
        }
        try {$result = app(DiscountService::class)->validate(
                $discountCode,
                $cart->subtotal,
                Auth::user()
            );
            $cart->update([
                'discount_code' => $result['discount']->code,
                'discount_amount' => $result['discount_amount'],
            ]);
            return null;
        } catch (DiscountException $exception) {
            $cart->clearDiscount();
            return sprintf(
                'Discount code "%s" is no longer valid after merging your cart: %s',
                $discountCode,
                $exception->getMessage()
            );
        }
    }
}
