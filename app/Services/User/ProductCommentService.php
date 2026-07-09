<?php

namespace App\Services\User;

use App\Enums\OrderStatus;
use App\Models\Item;
use App\Models\OrderItem;
use App\Models\ProductComment;

class ProductCommentService
{
    public function store(Item $item, int $userId, array $data): ProductComment{
        abort_unless($item->is_active, 404);
        $this->ensureUserPurchasedItem($item, $userId);
        return $item->comments()->create([
            ...$data,
            'user_id' => $userId,
        ]);
    }
    public function update(ProductComment $comment, array $data): ProductComment{
        $comment->update($data);
        return $comment;
    }
    public function destroy(ProductComment $comment): void{
        $comment->delete();
    }
    private function ensureUserPurchasedItem(Item $item, int $userId): void{
        $purchased = OrderItem::query()
            ->where('item_id', $item->id)
            ->whereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', OrderStatus::COMPLETED->value);
            })
            ->exists();
        abort_unless($purchased, 403, __('messages.comment_requires_purchase'));
    }
}
