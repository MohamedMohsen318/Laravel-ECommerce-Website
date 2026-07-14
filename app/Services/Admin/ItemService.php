<?php

namespace App\Services\Admin;

use App\Enums\MediaType;
use App\Models\Item;
use App\Models\ItemOptionValue;
use App\Models\ItemVariant;
use Illuminate\Validation\ValidationException;

class ItemService
{
    public function getAll(){
        return Item::with(['categories.translations', 'media', 'variants.optionValues.option'])
            ->orderByDesc('id')
            ->get();
    }
    public function find(int $id): Item{
        return Item::with(['variants.optionValues.option'])->findOrFail($id);
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
        $data['has_variants'] = ! empty($variants);
        $item->exists ? $item->update($data) : $item->fill($data)->save();
        $item->setTranslation('en', ['en' => $translation]);
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
                || ! empty($variant['option_value_ids'] ?? []))
            ->values()->all();
    }
    private function syncVariants(Item $item, array $variants): void{
        $this->validateVariants($variants);
        $keepIds = [];
        foreach ($variants as $variantData) {
            $optionValueIds = $variantData['option_value_ids'] ?? [];
            $id = $variantData['id'] ?? null;
            unset($variantData['option_value_ids'], $variantData['id']);
            $variantData['is_active'] = (bool) ($variantData['is_active'] ?? false);
            if ($id) {
                $variant = ItemVariant::where('item_id', $item->id)->findOrFail($id);
                $variant->update($variantData);
                $variant->optionValues()->sync($optionValueIds);
                $keepIds[] = $variant->id;
                continue;}
            $variant = $item->variants()->create($variantData);
            $variant->optionValues()->sync($optionValueIds);
            $keepIds[] = $variant->id;}
        $item->variants()->whereNotIn('id', $keepIds)->delete();
    }
    private function validateVariants(array $variants): void{
        $errors = [];
        $seen = [];
        foreach ($variants as $index => $variant) {
            $label = "Variant " . ($index + 1);
            $price = $variant['price'] ?? null;
            $discount = $variant['discount_price'] ?? null;
            $errors += array_filter([
                "variants.$index.price" => !filled($price) ? "$label must have a price." : null,
                "variants.$index.stock" => !filled($variant['stock'] ?? null) ? "$label must have stock." : null,
                "variants.$index.discount_price" => filled($discount) && filled($price) && (float) $discount > (float) $price
                    ? "$label discount price cannot be greater than price."
                    : null,
            ]);
            $ids = collect($variant['option_value_ids'] ?? [])->filter()->map(fn ($id) => (int) $id)->unique()->values();
            $groups = $ids->isNotEmpty() ? ItemOptionValue::whereIn('id', $ids)->pluck('item_option_id') : collect();
            $key = $ids->sort()->implode('-');
            $message = match(true) {
                $ids->isEmpty() => "$label must have at least one option value.",
                $groups->count() !== $ids->count() => "$label has invalid option values.",
                isset($seen[$key]) => "$label duplicates another variant combination.",
                $groups->duplicates()->isNotEmpty() => "$label cannot use more than one value from the same option.",
                default => null,
            };
            if ($message) {
                $errors["variants.$index.option_value_ids"] = $message;}
            if ($ids->isNotEmpty() && $groups->count() === $ids->count()) {
                $seen[$key] = true;}
        }
        if ($errors) {
            throw ValidationException::withMessages($errors);}
    }
}
