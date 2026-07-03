<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use App\Services\Admin\ProductReviewService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ProductReviewController extends Controller
{
    public function __construct(
        private readonly ProductReviewService $reviewService
    ) {}

    public function index(): View
    {
        $reviews = $this->reviewService->getAllPaginated();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function destroy(ProductReview $review): RedirectResponse
    {
        $this->reviewService->destroy($review);

        return back()->with('success', __('messages.review_deleted'));
    }
}
