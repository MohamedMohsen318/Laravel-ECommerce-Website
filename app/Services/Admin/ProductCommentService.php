<?php

namespace App\Services\Admin;

use App\Models\ProductComment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductCommentService
{
    public function getAllPaginated(): LengthAwarePaginator{
        return ProductComment::query()
            ->with(['item', 'user', 'parent'])
            ->latest()
            ->paginate(15);
    }
    public function destroy(ProductComment $comment): void{
        $comment->delete();
    }
}
