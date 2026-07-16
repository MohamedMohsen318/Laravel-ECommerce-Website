<?php

namespace App\Services\Admin;

use App\Enums\MediaType;
use App\Models\Item;
use App\Models\ItemAttributeValue;
use Illuminate\Validation\ValidationException;

class ItemService
{
    public function getAll(){
        return Item::with(['categories.translations', 'media', 'children.attributeValues.attribute'])
            ->orderByDesc('id')
            ->get();
    }
    public function find(int $id): Item{
        return Item::with(['children.attributeValues.attribute'])->findOrFail($id);
    }
    public function create(array $data): Item{
        $item = new Item();
        return $this->save($item, $data);
    }
    public function update(Item $item, array $data): Item{
        return $this->save($item, $data);
    }
    public function delete(Item $item): bool{
        return $item->delete();
    }

    private function save(Item $item, array $data): Item{
        $translation = $this->extractTranslation($data);
        $categoryIds = $this->extractCategoryIds($data);
        $variants = $this->extractVariants($data);
        $image = $data['image'] ?? null;
        unset($data['image']);
        $data['type'] = empty($variants) ? 'simple' : 'variant';
        $item->exists ? $item->update($data) : $item->fill($data)->save();
        $item->setTranslation('en', $translation);
        $item->categories()->sync($categoryIds);
        $this->syncVariants($item, $variants);
        if ($image) {
            $item->setMedia($image, MediaType::Image, 'items');}
        return $item;
    }
    private function extractTranslation(array &$data): array{
        $translation = [
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
        ];
        unset($data['name'], $data['description']);
        return $translation;
    }
    private function extractCategoryIds(array &$data): array{
        $categoryIds = $data['category_ids'] ?? [];
        unset($data['category_ids']);
        return $categoryIds;
    }
    private function extractVariants(array &$data): array{
        $variants = $data['variants'] ?? [];
        unset($data['variants']);
        return collect($variants)
            ->filter(fn ($variant) => filled($variant['price'] ?? null)
                || filled($variant['sku'] ?? null)
                || ! empty($variant['attribute_value_ids'] ?? []))
            ->values()->all();
    }
    private function syncVariants(Item $item, array $variants): void{
        $this->validateVariants($variants);
        $keepIds = [];
        foreach ($variants as $variantData) {
            $attributeValueIds = $variantData['attribute_value_ids'] ?? [];
            $id = $variantData['id'] ?? null;
            unset($variantData['attribute_value_ids'], $variantData['id']);
            $variantData['is_active'] = (bool) ($variantData['is_active'] ?? false);
            $variantData['type'] = 'simple';
            $variantData['parent_id'] = $item->id;
            $variantData['name'] = $variantData['name'] ?? $item->name;
            if ($id) {
                $variant = Item::where('parent_id', $item->id)->findOrFail($id);
                $variant->update($variantData);
                $variant->attributeValues()->sync($attributeValueIds);
                $keepIds[] = $variant->id;
                continue;}
            $variant = $item->children()->create($variantData);
            $variant->attributeValues()->sync($attributeValueIds);
            $keepIds[] = $variant->id;}

        if (empty($keepIds)) {
            $item->children()->delete();
            return;
        }

        $item->children()->whereNotIn('id', $keepIds)->delete();
    }
    private function validateVariants(array $variants): void
    {
        $errors = [];
        $seenCombinations = [];

        foreach ($variants as $index => $variant) {
            $label = 'Variant ' . ($index + 1);

            $this->validateBasicFields(
                $variant,
                $index,
                $label,
                $errors
            );

            $ids = collect($variant['attribute_value_ids'] ?? [])
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            $groups = $ids->isNotEmpty()
                ? ItemAttributeValue::whereIn('id', $ids)
                    ->pluck('item_attribute_id')
                : collect();

            $key = $ids->sort()->implode('-');

            if ($ids->isEmpty()) {
                $errors["variants.$index.attribute_value_ids"] =
                    "$label must have at least one option value.";

                continue;
            }

            if ($groups->count() !== $ids->count()) {
                $errors["variants.$index.attribute_value_ids"] =
                    "$label has invalid option values.";

                continue;
            }

            if ($groups->duplicates()->isNotEmpty()) {
                $errors["variants.$index.attribute_value_ids"] =
                    "$label cannot use more than one value from the same option.";

                continue;
            }

            if (isset($seenCombinations[$key])) {
                $errors["variants.$index.attribute_value_ids"] =
                    "$label duplicates another variant combination.";

                continue;
            }

            $seenCombinations[$key] = true;
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }
    }
    private function validateBasicFields(array $variant, int $index, string $label, array &$errors): void {
        $price = $variant['price'] ?? null;
        $discount = $variant['discount_price'] ?? null;
        if (! filled($price)) {
            $errors["variants.$index.price"] =
                "$label must have a price.";}
        if (! filled($variant['stock'] ?? null)) {
            $errors["variants.$index.stock"] =
                "$label must have stock.";}
        if (filled($discount) && filled($price) &&
            (float) $discount > (float) $price)
        {
            $errors["variants.$index.discount_price"] =
                "$label discount price cannot be greater than price.";}
    }
}
