<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemOption;
use App\Services\Admin\ItemOptionService;
use Illuminate\Http\Request;

class ItemOptionController extends Controller
{
    public function __construct(
        protected ItemOptionService $itemOptionService
    ) {}

    public function index()
    {
        $options = $this->itemOptionService->getAll();

        return view('admin.item-options.index', compact('options'));
    }

    public function create()
    {
        return view('admin.item-options.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        $this->itemOptionService->create($data);

        return redirect()
            ->route('admins.item-options.index')
            ->with('success', 'Option created successfully.');
    }

    public function edit(ItemOption $itemOption)
    {
        $itemOption->load('values');

        return view('admin.item-options.edit', compact('itemOption'));
    }

    public function update(Request $request, ItemOption $itemOption)
    {
        $data = $request->validate($this->rules());

        $this->itemOptionService->update($itemOption, $data);

        return redirect()
            ->route('admins.item-options.index')
            ->with('success', 'Option updated successfully.');
    }

    public function destroy(ItemOption $itemOption)
    {
        $this->itemOptionService->delete($itemOption);

        return back()->with('success', 'Option deleted successfully.');
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'values' => ['required', 'array', 'min:1'],
            'values.*' => ['required', 'string', 'max:255'],
        ];
    }
}
