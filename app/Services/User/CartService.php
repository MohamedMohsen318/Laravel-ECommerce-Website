<?php

namespace App\Services\User;

use App\Enums\CartStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Item;
use App\Models\ItemVariant;
use App\Services\DiscountException;
use App\Services\DiscountService;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CartService
{
    private ?Cart $cart = null;

    public function getCart(): Cart
    {
        if ($this->cart) {
            return $this->cart;
        }

        if (Auth::check()) {
            return $this->cart = Auth::user()
                ->getOrCreateCart()
                ->loadMissing('items');
        }

        return $this->cart = Cart::firstOrCreate(
            [
                'session_id' => Session::getId(),
                'user_id' => null,
            ],
            [
                'status' => CartStatus::ACTIVE,
            ]
        )->loadMissing('items');
    }

    public function addItem(
        int $itemId,
        int $quantity = 1,
        ?int $itemVariantId = null,
        array $options = []
    ): CartItem
    {
        $cart = $this->getCart();

        $item = Item::findOrFail($itemId);
        $price = $item->discount_price ?? $item->price;
        $availableStock = $item->stock;

        if ($item->has_variants) {
            if (! $itemVariantId) {
                throw ValidationException::withMessages([
                    'item_variant_id' => 'Please select a product variant.',
                ]);
            }

            $variant = ItemVariant::where('item_id', $item->id)
                ->where('is_active', true)
                ->findOrFail($itemVariantId);

            $price = $variant->effective_price;
            $availableStock = $variant->stock;
        } elseif ($itemVariantId) {
            throw ValidationException::withMessages([
                'item_variant_id' => 'This product does not use variants.',
            ]);
        }

        if ($availableStock < $quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'The requested quantity is not available in stock.',
            ]);
        }

        $existingItem = $cart->items()
            ->where('item_id', $itemId)
            ->where('item_variant_id', $itemVariantId)
            ->first();

        if ($existingItem) {
            if (($existingItem->quantity + $quantity) > $availableStock) {
                throw ValidationException::withMessages([
                    'quantity' => 'The requested quantity is not available in stock.',
                ]);
            }

            $existingItem->increment('quantity', $quantity);

            return $existingItem->fresh();
        }

        return $cart->items()->create([
            'item_id' => $item->id,
            'item_variant_id' => $itemVariantId,
            'quantity' => $quantity,
            'price' => $price,
            'options' => $options,
        ]);
    }

    public function updateItem(int $cartItemId, int $quantity): ?CartItem
    {
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

    public function removeItem(int $cartItemId): bool
    {
        return (bool) $this->getCart()
            ->items()
            ->whereKey($cartItemId)
            ->delete();
    }

    public function clearCart(): bool
    {
        $cart = $this->getCart();

        $cart->items()->delete();
        $cart->clearDiscount();

        return true;
    }

    public function applyDiscount(string $code): array
    {
        $cart = $this->getCart()->loadMissing('items');
        try {$result = app(DiscountService::class)->validate(
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

    public function removeDiscount(): bool
    {
        return $this->getCart()->clearDiscount();
    }

    public function mergeGuestCart(?string $sessionId = null): ?string
    {
        if (! Auth::check()) {
            return null;
        }

        $guestCart = Cart::with('items')
            ->where('session_id', $sessionId ?? Session::getId())
            ->active()
            ->first();

        if (! $guestCart || $guestCart->isEmpty()) {
            return null;
        }

        $userCart = $this->getCart()->loadMissing('items');

        return DB::transaction(function () use ($guestCart, $userCart) {
            // سلوك مقصود: لو المستخدم المسجّل عنده كود مطبق بالفعل على كارته،
            // بياخد الأولوية على كود الضيف. لو مفيش كود عند المستخدم، بنستخدم
            // كود الضيف (لو موجود). الكود المختار بيتعاد التحقق من صلاحيته
            // بعد الدمج جوه refreshMergedDiscount لأن الـ subtotal بيتغير.
            $discountCode = $userCart->discount_code ?: $guestCart->discount_code;
            $existingItems = $userCart->items->keyBy(
                fn ($item) => $item->item_id . '-' . ($item->item_variant_id ?? 'base')
            );
            $newItems = [];
            $now = now();

            foreach ($guestCart->items as $guestItem) {
                $key = $guestItem->item_id . '-' . ($guestItem->item_variant_id ?? 'base');
                $item = $existingItems->get($key);

                if ($item) {
                    $item->increment('quantity', $guestItem->quantity);
                    continue;
                }

                $newItems[] = [
                    'cart_id' => $userCart->id,
                    'item_id' => $guestItem->item_id,
                    'item_variant_id' => $guestItem->item_variant_id,
                    'quantity' => $guestItem->quantity,
                    'price' => $guestItem->price,
                    'options' => $guestItem->options,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if ($newItems) {
                $userCart->items()->insert($newItems);
            }

            $userCart->load('items');

            $warning = $this->refreshMergedDiscount($userCart, $discountCode);

            $guestCart->items()->delete();
            $guestCart->markAsAbandoned();

            return $warning;
        });
    }
    private function refreshMergedDiscount(
        Cart $cart,
        ?string $discountCode
    ): ?string {
        if (! $discountCode) {
            $cart->clearDiscount();

            return null;
        }

        try {
            $result = app(DiscountService::class)->validate(
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
