<?php

namespace App\Services\User;

use App\Models\Item;
use App\Models\ProductComment;

class ProductCommentService
{
    public function store(Item $item, int $userId, array $data): ProductComment
    {
        return $item->comments()->create([
            ...$data,
            'user_id' => $userId,
        ]);
    }

    public function update(ProductComment $comment, array $data): ProductComment
    {
        $comment->update($data);

        return $comment;
    }

    public function destroy(ProductComment $comment): void
    {
        $comment->delete();
    }
}
