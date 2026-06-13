<?php

namespace App\Services\User;

use App\Models\Category;

class CategoryService
{
    public function getRootCategories()
    {
        return Category::whereNull('parent_id')
            ->where('is_active', true)
            ->with('children')
            ->get();
    }

    public function findByPath(string $path): Category
    {
        $slugs = explode('/', $path);
        $slug = end($slugs);

        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->with('children')
            ->firstOrFail();

        return $category;
    }
}
