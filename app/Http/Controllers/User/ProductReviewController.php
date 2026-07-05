<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreProductReviewRequest;
use App\Models\Item;
use App\Services\User\ProductReviewService;
use Illuminate\Http\RedirectResponse;

class ProductReviewController extends Controller
{
    public function store(StoreProductReviewRequest $request, Item $item): RedirectResponse
    {
        app(ProductReviewService::class)
            ->storeOrUpdate($item, auth()->id(), $request->validated());

        return back()->with('success', __('messages.review_saved'));
    }

    public function destroy(Item $item): RedirectResponse
    {
        app(ProductReviewService::class)
            ->destroy($item, auth()->id());

        return back()->with('success', __('messages.review_deleted'));
    }
}
