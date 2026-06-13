<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\Admin\CategoryService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService
    ) {}

    public function index(): Factory|View
    {
        $categories = $this->categoryService->getCategoriesForIndex();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): Factory|View
    {
        $selectCategories = $this->categoryService->getCategoriesForSelect();

        return view('admin.categories.create', compact('selectCategories'));
    }

    // FIX #3: الـ Controller دلوقتي هو اللي بيعمل الـ redirect مش الـ Service
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->categoryService->store($request->validated());

        return redirect()
            ->route('admins.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function show(Category $category)
    {
        //
    }

    public function edit(Category $category): Factory|View
    {
        $selectCategories = $this->categoryService->getCategoriesForEdit($category);

        $category->load(['translations', 'media']);

        return view('admin.categories.edit', compact('category', 'selectCategories'));
    }

    // FIX #3: نفس الكلام - الـ redirect هنا مش في الـ Service
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->categoryService->update($request->validated(), $category);

        return redirect()
            ->route('admins.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->categoryService->destroy($category);

        return redirect()
            ->route('admins.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
