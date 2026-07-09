<?php

namespace App\Services\Admin;

use App\Enums\MediaType;
use App\Models\Item;
use App\Models\ItemVariant;
use Illuminate\Validation\ValidationException;

class ItemService
{
    public function getAll()
    {
        return Item::with(['categories.translations', 'media', 'variants.optionValues.option'])
            ->orderByDesc('id')
            ->get();
    }

    public function find(int $id): Item
    {
        return Item::with(['variants.optionValues.option'])->findOrFail($id);
    }

    public function create(array $data): Item
    {
        $translation = $this->extractTranslation($data);
        $categoryIds = $this->extractCategoryIds($data);
        $variants = $this->extractVariants($data);
        $image = $data['image'] ?? null;
        unset($data['image']);

        $data['has_variants'] = ! empty($variants);
        $item = Item::create($data);
        $item->setTranslation('en', ['en' => $translation]);
        $item->categories()->sync($categoryIds);
        $this->syncVariants($item, $variants);

        if ($image) {
            $item->setMedia($image, MediaType::Image, 'items');
        }

        return $item;
    }

    public function update(Item $item, array $data): Item
    {
        $translation = $this->extractTranslation($data);
        $categoryIds = $this->extractCategoryIds($data);
        $variants = $this->extractVariants($data);
        $image = $data['image'] ?? null;
        unset($data['image']);

        $data['has_variants'] = ! empty($variants);
        $item->update($data);
        $item->setTranslation('en', ['en' => $translation]);
        $item->categories()->sync($categoryIds);
        $this->syncVariants($item, $variants);

        if ($image) {
            $item->setMedia($image, MediaType::Image, 'items');
        }

        return $item;
    }

    public function delete(Item $item): bool
    {
        return $item->delete();
    }

    private function extractTranslation(array &$data): array
    {
        $translation = [
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
        ];

        unset($data['name'], $data['description']);

        return $translation;
    }

    private function extractCategoryIds(array &$data): array
    {
        $categoryIds = $data['category_ids'] ?? [];
        unset($data['category_ids']);

        return $categoryIds;
    }

    private function extractVariants(array &$data): array
    {
        $variants = $data['variants'] ?? [];
        unset($data['variants']);

        return collect($variants)
            ->filter(fn ($variant) => filled($variant['price'] ?? null) || filled($variant['sku'] ?? null) || ! empty($variant['option_value_ids'] ?? []))
            ->values()
            ->all();
    }

    private function syncVariants(Item $item, array $variants): void
    {
        $this->validateVariants($variants);

        $keepIds = [];

        foreach ($variants as $variantData) {
            $optionValueIds = $variantData['option_value_ids'] ?? [];
            unset($variantData['option_value_ids']);

            $variantData['is_active'] = (bool) ($variantData['is_active'] ?? false);

            if (! empty($variantData['id'])) {
                $variant = ItemVariant::where('item_id', $item->id)
                    ->findOrFail($variantData['id']);
                unset($variantData['id']);
                $variant->update($variantData);
            } else {
                unset($variantData['id']);
                $variant = $item->variants()->create($variantData);
            }

            $variant->optionValues()->sync($optionValueIds);
            $keepIds[] = $variant->id;
        }

        $item->variants()
            ->when($keepIds, fn ($query) => $query->whereNotIn('id', $keepIds))
            ->when(empty($keepIds), fn ($query) => $query)
            ->delete();
    }

    private function validateVariants(array $variants): void
    {
        $errors = [];
        $seenCombinations = [];

        foreach ($variants as $index => $variant) {
            $label = 'Variant ' . ($index + 1);
            $optionValueIds = collect($variant['option_value_ids'] ?? [])
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            if (! filled($variant['price'] ?? null)) {
                $errors["variants.$index.price"] = "$label must have a price.";
            }

            if (! array_key_exists('stock', $variant) || ! filled($variant['stock'])) {
                $errors["variants.$index.stock"] = "$label must have stock.";
            }

            if (filled($variant['discount_price'] ?? null) && filled($variant['price'] ?? null) && (float) $variant['discount_price'] > (float) $variant['price']) {
                $errors["variants.$index.discount_price"] = "$label discount price cannot be greater than price.";
            }

            if ($optionValueIds->isEmpty()) {
                $errors["variants.$index.option_value_ids"] = "$label must have at least one option value.";
                continue;
            }

            $optionGroups = \App\Models\ItemOptionValue::query()
                ->whereIn('id', $optionValueIds)
                ->pluck('item_option_id');

            if ($optionGroups->count() !== $optionValueIds->count()) {
                $errors["variants.$index.option_value_ids"] = "$label has invalid option values.";
                continue;
            }

            if ($optionGroups->duplicates()->isNotEmpty()) {
                $errors["variants.$index.option_value_ids"] = "$label cannot use more than one value from the same option.";
            }

            $combinationKey = $optionValueIds->sort()->implode('-');
            if (isset($seenCombinations[$combinationKey])) {
                $errors["variants.$index.option_value_ids"] = "$label duplicates another variant combination.";
            }

            $seenCombinations[$combinationKey] = true;
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }
    }
}
