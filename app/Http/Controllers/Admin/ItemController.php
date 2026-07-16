<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ItemStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ItemRequest;
use App\Models\Item;
use App\Services\Admin\CategoryService;
use App\Services\Admin\ItemAttributeService;
use App\Services\Admin\ItemService;

class ItemController extends Controller
{
    protected function itemService(): ItemService{
        return app(ItemService::class);
    }
    protected function categoryService(): CategoryService{
        return app(CategoryService::class);
    }
    protected function itemAttributeService(): ItemAttributeService{
        return app(ItemAttributeService::class);
    }
    public function index(){
        $items = $this->itemService()->getAll();
        return view('admin.items.index', compact('items'));
    }
    public function create(){
        $statuses = ItemStatus::cases();
        $selectCategories = $this->categoryService()->getCategoriesForSelect();
        $itemAttributes = $this->itemAttributeService()->getAll();
        return view('admin.items.create',
            compact('statuses', 'selectCategories', 'itemAttributes'));
    }
    public function store(ItemRequest $request){
        $data = $request->validated();
        $this->itemService()->create($data);
        return redirect()
            ->route('admins.items.index')
            ->with('success', 'Product created successfully.');
    }
    public function edit(Item $item){
        $statuses = ItemStatus::cases();
        $selectCategories = $this->categoryService()->getCategoriesForSelect();
        $itemAttributes = $this->itemAttributeService()->getAll();
        $item->load(['categories', 'media', 'children.attributeValues']);
        return view('admin.items.edit',
            compact('item', 'statuses', 'selectCategories', 'itemAttributes'));
    }
    public function update(ItemRequest $request, Item $item){
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->itemService()->update($item, $data);

        return redirect()
            ->route('admins.items.index')
            ->with('success', 'Product updated successfully.');
    }
    public function destroy(Item $item){
        $this->itemService()->delete($item);
        return back()->with('success', 'Product deleted successfully.');
    }
}
