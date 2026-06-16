<?php

namespace App\Services\Admin;

use App\Enums\MediaType;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryService
{
    private function buildTree($categories, $level = 0): array
    {
        $tree = [];

        foreach ($categories as $category) {

            $tree[] = [
                'id' => $category->id,
                'name' => $category->translate('en')?->name ?? $category->slug,
                'slug' => $category->slug,
                'level' => $level,
                'items_count' => $category->items_count ?? 0,
                'is_active' => $category->is_active,
                'parent_id' => $category->parent_id,
            ];

            if ($category->allChildren->isNotEmpty()) {
                $tree = array_merge(
                    $tree,
                    $this->buildTree($category->allChildren, $level + 1)
                );
            }
        }

        return $tree;
    }

    public function getCategoriesForIndex(): array
    {
        $categories = Category::with(['allChildren', 'translations'])
            ->withCount('items')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return $this->buildTree($categories);
    }

    public function getCategoriesForSelect(): array
    {
        $categories = Category::with(['translations', 'allChildren.translations'])
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return $this->buildTree($categories);
    }

    public function getCategoriesForEdit(Category $category): array
    {
        $categories = Category::with(['translations', 'allChildren.translations'])
            ->where('id', '!=', $category->id)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return $this->buildTree($categories);
    }

    public function store(array $data): Category
    {
        $category = Category::create([
            'parent_id' => $data['parent_id'] ?? null,
            'slug' => $this->makeUniqueSlug($data['translations']['en']['name']),
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        $this->syncTranslations($category, $data['translations']);

        if (isset($data['image'])) {
            $category->setMedia($data['image'], MediaType::Image, 'categories');
        }

        return $category;
    }

    public function update(array $data, Category $category): Category
    {
        $category->update([
            'parent_id' => $data['parent_id'] ?? null,
            'slug' => $this->makeUniqueSlug(
                $data['translations']['en']['name'],
                $category->id
            ),
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        $this->syncTranslations($category, $data['translations']);

        if (isset($data['image'])) {
            $category->setMedia($data['image'], MediaType::Image, 'categories');
        }

        return $category->fresh();
    }

    public function destroy(Category $category): void
    {
        $category->delete();
    }

    private function syncTranslations(Category $category, array $translations): void
    {
        foreach ($translations as $locale => $content) {
            if (!empty($content['name'])) {
                $category->setTranslation($locale, $translations);
            }
        }
    }

    private function makeUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (
        Category::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
