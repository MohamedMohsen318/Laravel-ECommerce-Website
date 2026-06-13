<?php

namespace App\Services\Admin;

use App\Models\Category;
use Illuminate\Support\Str;

class CategoryService
{
    public function getCategoriesForIndex()
    {
        return Category::with([
            'allChildren',
            'translations',
            'media',
        ])
            ->withCount('items')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
    }

    public function getCategoriesForSelect()
    {
        return Category::with('translations')->get();
    }

    public function getCategoriesForEdit(Category $category)
    {
        return Category::with('translations')
            ->where('id', '!=', $category->id)
            ->get();
    }

    // FIX #3: الـ Service دلوقتي بترجع Category مش RedirectResponse
    public function store(array $data): Category
    {
        $category = Category::create([
            'parent_id' => $data['parent_id'] ?? null,
            'slug'      => $this->makeUniqueSlug($data['translations']['en']['name']),
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        $this->syncTranslations($category, $data['translations']);

        if (isset($data['image'])) {
            $category->setMedia($data['image'], 'image', 'categories');
        }

        return $category;
    }

    // FIX #3: بترجع Category مش RedirectResponse
    public function update(array $data, Category $category): Category
    {
        $category->update([
            'parent_id' => $data['parent_id'] ?? null,
            'slug'      => $this->makeUniqueSlug(
                $data['translations']['en']['name'],
                $category->id
            ),
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        $this->syncTranslations($category, $data['translations']);

        if (isset($data['image'])) {
            $category->setMedia($data['image'], 'image', 'categories');
        }

        return $category->fresh();
    }

    // FIX #3: بترجع void مش RedirectResponse
    public function destroy(Category $category): void
    {
        $category->delete();
    }

    // FIX #4: كانت بتبعت $translations كله بدل ما تبعت $data['translations']
    // والصح إنها تبعت $translations اللي هو parameter الـ method
    private function syncTranslations(Category $category, array $translations): void
    {
        foreach ($translations as $locale => $content) {
            if (! empty($content['name'])) {
                $category->setTranslation($locale, $translations);
            }
        }
    }

    private function makeUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug     = $baseSlug;
        $counter  = 1;

        while (
            Category::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
