<?php

namespace App\Services\User;

use App\Enums\OrderStatus;
use App\Models\Item;
use App\Models\OrderItem;
use App\Models\ProductReview;

class ProductReviewService
{
    public function storeOrUpdate(Item $item, int $userId, array $data): ProductReview{
        abort_unless($item->is_active, 404);
        $this->ensureUserPurchasedItem($item, $userId);
        return $item->reviews()->updateOrCreate(
            ['user_id' => $userId],
            $data);
    }
    public function destroy(Item $item, int $userId): void{
        abort_unless($item->is_active, 404);
        $item->reviews()->where('user_id', $userId)->delete();
    }
    private function ensureUserPurchasedItem(Item $item, int $userId): void{
        $purchased = OrderItem::query()
            ->where('item_id', $item->id)
            ->whereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', OrderStatus::COMPLETED->value);
            })
            ->exists();
        abort_unless($purchased, 403, __('messages.review_requires_purchase'));
    }
}
