<?php

namespace App\Services\Admin;

use App\Enums\CartStatus;
use App\Models\Cart;
use Illuminate\Pagination\LengthAwarePaginator;

class CartService
{
    public function getAllCarts(array $filters = []): LengthAwarePaginator
    {
        return Cart::with(['user', 'items.item'])
            ->when(!empty($filters['status']), function ($query) use ($filters) {
                $query->where('status', $filters['status']);
            })
            ->when(!empty($filters['search']), function ($query) use ($filters) {
                $search = $filters['search'];

                $query->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20);
    }

    public function getStats(): array
    {
        return [
            'total'     => Cart::count(),
            'active'    => Cart::active()->count(),
            'abandoned' => Cart::abandoned()->count(),
            'completed' => Cart::where('status', CartStatus::COMPLETED)->count(),
        ];
    }

    public function updateStatus(Cart $cart, string $status): Cart
    {
        $cart->update([
            'status' => $status,
        ]);

        return $cart->fresh();
    }

    public function destroy(Cart $cart): void
    {
        $cart->items()->delete();
        $cart->delete();
    }
}
