<?php

namespace App\Services\User;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryService
{
    public function getRootCategories(){
        return Category::whereNull('parent_id')
            ->where('is_active', true)
            ->with([
                'media',
                'children.media',
            ])
            ->get();
    }
    public function findByPath(string $path): Category{
        $slugs = explode('/', trim($path, '/'));
        $category = null;
        $parentId = null;
        foreach ($slugs as $slug) {
            $query = Category::where('slug', $slug)
                ->where('is_active', true);
            $query = $parentId === null
                ? $query->whereNull('parent_id')
                : $query->where('parent_id', $parentId);
            $category = $query->firstOrFail();
            $parentId = $category->id;
        }
        return $category->load(['media', 'children.media']);
    }
    public function getCategoryProducts(Category $category): LengthAwarePaginator
    {
        return Item::query()
            ->with(['media', 'categories.translations'])
            ->whereHas('categories', function ($query) use ($category) {
                $query->whereIn('categories.id', $this->categoryAndDescendantIds($category));
            })
            ->where('is_active', true)
            ->latest()
            ->paginate(12);
    }

    private function categoryAndDescendantIds(Category $category): array
    {

        $ids = [];
        $stack = [$category];
        while (!empty($stack)) {
            $node = array_pop($stack);
            $ids[] = $node->id;
            $children = Category::where('parent_id', $node->id)->get();
            foreach ($children as $child) {
                $stack[] = $child;
            }
        }
        return $ids;
    }
}
