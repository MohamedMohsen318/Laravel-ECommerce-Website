<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\User\CategoryService;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    public function index()
    {
        $categories = $this->categoryService->getRootCategories();

        return view('user.categories.index', compact('categories'));
    }

    public function show(string $path)
    {
        $category = $this->categoryService->findByPath($path);

        $products = new LengthAwarePaginator([], 0, 12);

        return view('user.categories.show', compact('category', 'products'));
    }
}
