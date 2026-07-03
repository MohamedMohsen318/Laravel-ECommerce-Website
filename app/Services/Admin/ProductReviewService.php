<?php

namespace App\Services\Admin;

use App\Models\ProductReview;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductReviewService
{
    public function getAllPaginated(): LengthAwarePaginator
    {
        return ProductReview::query()
            ->with(['item', 'user'])
            ->latest()
            ->paginate(20);
    }

    public function destroy(ProductReview $review): void
    {
        $review->delete();
    }
}
