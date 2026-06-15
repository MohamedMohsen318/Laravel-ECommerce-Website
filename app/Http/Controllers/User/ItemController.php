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
            ->latest()
            ->paginate(12);

        return view('user.items.index', compact('items'));
    }

    public function show(Item $item): View
    {
        abort_unless($item->is_active, 404);

        return view('user.items.show', compact('item'));
    }
}
