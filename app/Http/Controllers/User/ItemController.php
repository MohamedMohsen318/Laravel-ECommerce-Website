<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            'children' => fn ($query) => $query->where('is_active', true)->with('attributeValues.attribute'),
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

    public function variants(Item $item, Request $request): JsonResponse
    {
        abort_unless($item->is_active, 404);

        $attributeValueIds = collect($request->input('attribute_value_ids', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values();

        $variant = $item->children()
            ->where('is_active', true)
            ->has('attributeValues', '=', $attributeValueIds->count())
            ->whereHas('attributeValues', function ($query) use ($attributeValueIds) {
                $query->whereIn('item_attribute_values.id', $attributeValueIds);
            }, '=', $attributeValueIds->count())
            ->with('attributeValues.attribute')
            ->first();

        return response()->json([
            'variant' => $variant,
        ]);
    }
}
