<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ItemStatus;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Services\Admin\CategoryService;
use App\Services\Admin\ItemOptionService;
use App\Services\Admin\ItemService;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function __construct(
        protected ItemService $itemService,
        protected CategoryService $categoryService,
        protected ItemOptionService $itemOptionService
    ) {}

    public function index()
    {
        $items = $this->itemService->getAll();

        return view('admin.items.index', compact('items'));
    }

    public function create()
    {
        $statuses = ItemStatus::cases();
        $selectCategories = $this->categoryService->getCategoriesForSelect();
        $itemOptions = $this->itemOptionService->getAll();

        return view('admin.items.create', compact('statuses', 'selectCategories', 'itemOptions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        $this->itemService->create($data);

        return redirect()
            ->route('admins.items.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Item $item)
    {
        $statuses = ItemStatus::cases();
        $selectCategories = $this->categoryService->getCategoriesForSelect();
        $itemOptions = $this->itemOptionService->getAll();
        $item->load(['categories', 'media', 'variants.optionValues']);

        return view('admin.items.edit', compact('item', 'statuses', 'selectCategories', 'itemOptions'));
    }

    public function update(Request $request, Item $item)
    {
        $data = $request->validate($this->rules(true));

        $data['is_active'] = $request->boolean('is_active');

        $this->itemService->update($item, $data);

        return redirect()
            ->route('admins.items.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Item $item)
    {
        $this->itemService->delete($item);

        return back()->with('success', 'Product deleted successfully.');
    }

    private function rules(bool $isUpdate = false): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:' . implode(',', ItemStatus::values())],
            'is_active' => ['nullable'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['exists:categories,id'],
            'image' => ['nullable', 'image', 'max:2048'],

            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'integer', 'exists:item_variants,id'],
            'variants.*.sku' => ['nullable', 'string', 'max:255'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.discount_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
            'variants.*.is_active' => ['nullable'],
            'variants.*.option_value_ids' => ['nullable', 'array'],
            'variants.*.option_value_ids.*' => ['exists:item_option_values,id'],
        ];
    }
}
