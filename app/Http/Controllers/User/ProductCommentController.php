<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreProductCommentRequest;
use App\Http\Requests\User\UpdateProductCommentRequest;
use App\Models\Item;
use App\Models\ProductComment;
use App\Services\User\ProductCommentService;
use Illuminate\Http\RedirectResponse;

class ProductCommentController extends Controller
{
    public function __construct(
        private readonly ProductCommentService $commentService
    ) {}

    public function store(StoreProductCommentRequest $request, Item $item): RedirectResponse
    {
        $this->commentService->store($item, auth()->id(), $request->validated());

        return back()->with('success', __('messages.comment_added'));
    }

    public function update(UpdateProductCommentRequest $request, ProductComment $comment): RedirectResponse
    {
        $this->commentService->update($comment, $request->validated());

        return back()->with('success', __('messages.comment_updated'));
    }

    public function destroy(ProductComment $comment): RedirectResponse
    {
        abort_unless($comment->user_id === auth()->id(), 403);

        $this->commentService->destroy($comment);

        return back()->with('success', __('messages.comment_deleted'));
    }
}
