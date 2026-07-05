<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Contracts\View\View;

class ItemController extends Controller
{
    public function index(): View
    {
        $items = Item::query()
            ->where('is_active', true)
            ->with([
                'media',
                'categories.translations',
            ])
            ->latest()
            ->paginate(12);

        return view('user.items.index', compact('items'));
    }

    public function show(Item $item): View
    {
        abort_unless($item->is_active, 404);

        $item->load([
            'media',
            'categories.translations',
        ]);

        $reviews = $item->reviews()
            ->with('user')
            ->latest()
            ->paginate(10, ['*'], 'reviews_page')
            ->withQueryString();

        $comments = $item->comments()
            ->whereNull('parent_id')
            ->with([
                'user',
                'replies',
            ])
            ->latest()
            ->paginate(10, ['*'], 'comments_page')
            ->withQueryString();

        $myReview = auth()->id()
            ? $item->reviews()
                ->where('user_id', auth()->id())
                ->first()
            : null;

        return view('user.items.show', compact(
            'item',
            'reviews',
            'comments',
            'myReview'
        ));
    }
}
