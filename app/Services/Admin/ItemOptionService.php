<?php

namespace App\Services\Admin;

use App\Models\ItemOption;
use Illuminate\Database\Eloquent\Collection;

class ItemOptionService
{
    public function getAll(): Collection
    {
        return ItemOption::with('values')->orderBy('name')->get();
    }

    public function create(array $data): ItemOption
    {
        $values = $this->cleanValues($data['values'] ?? []);
        unset($data['values']);

        $option = ItemOption::create($data);

        foreach ($values as $value) {
            $option->values()->create(['value' => $value]);
        }

        return $option;
    }

    public function update(ItemOption $itemOption, array $data): ItemOption
    {
        $values = $this->cleanValues($data['values'] ?? []);
        unset($data['values']);

        $itemOption->update($data);
        $itemOption->values()->delete();

        foreach ($values as $value) {
            $itemOption->values()->create(['value' => $value]);
        }

        return $itemOption;
    }

    public function delete(ItemOption $itemOption): bool
    {
        return $itemOption->delete();
    }

    private function cleanValues(array $values): array
    {
        return collect($values)
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
