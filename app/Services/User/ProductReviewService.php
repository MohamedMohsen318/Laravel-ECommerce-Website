<?php

namespace App\Services\User;

use App\Models\Item;
use App\Models\ProductReview;

class ProductReviewService
{
    public function storeOrUpdate(Item $item, int $userId, array $data): ProductReview
    {
        return $item->reviews()->updateOrCreate(
            ['user_id' => $userId],
            $data
        );
    }

    public function destroy(Item $item, int $userId): void
    {
        $item->reviews()->where('user_id', $userId)->delete();
    }
}
