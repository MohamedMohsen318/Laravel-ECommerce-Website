<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ItemAttributeRequest;
use App\Models\Attribute;
use App\Services\Admin\ItemAttributeService;

class ItemAttributeController extends Controller
{
    protected function service(): ItemAttributeService{
        return app(ItemAttributeService::class);
    }
    public function index(){
        $attributes = $this->service()->getAll();
        return view('admin.item-attributes.index', compact('attributes'));
    }
    public function create(){
        return view('admin.item-attributes.create');
    }
    public function store(ItemAttributeRequest $request){
        $this->service()->create($request->validated());
        return redirect()
            ->route('admins.item-attributes.index')
            ->with('success', 'Attribute created successfully.');
    }
    public function edit(Attribute $itemAttribute){
        $itemAttribute->load('values');
        return view('admin.item-attributes.edit', compact('itemAttribute'));
    }
    public function update(ItemAttributeRequest $request, Attribute $itemAttribute){
        $this->service()->update($itemAttribute, $request->validated());
        return redirect()
            ->route('admins.item-attributes.index')
            ->with('success', 'Attribute updated successfully.');
    }
    public function destroy(Attribute $itemAttribute){
        $this->service()->delete($itemAttribute);
        return back()->with('success', 'Attribute deleted successfully.');
    }
}
