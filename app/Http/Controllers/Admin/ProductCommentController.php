<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductComment;
use App\Services\Admin\ProductCommentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ProductCommentController extends Controller
{
    public function __construct(
        private readonly ProductCommentService $commentService
    ) {}

    public function index(): View
    {
        $comments = $this->commentService->getAllPaginated();

        return view('admin.comments.index', compact('comments'));
    }

    public function destroy(ProductComment $comment): RedirectResponse
    {
        $this->commentService->destroy($comment);

        return back()->with('success', __('messages.comment_deleted'));
    }
}
