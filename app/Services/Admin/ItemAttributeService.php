<?php

namespace App\Services\Admin;

use App\Models\ItemAttribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ItemAttributeService
{
    public function getAll(): Collection{
        return ItemAttribute::with('values')
            ->orderBy('name')
            ->get();
    }
    public function create(array $data): ItemAttribute{
        $values = $this->cleanValues($data['values'] ?? []);
        unset($data['values']);
        return DB::transaction(function () use ($data, $values) {
            $attribute = ItemAttribute::create($data);
            foreach ($values as $value) {
                $attribute->values()->create([
                    'value' => $value,
                ]);
            }
            return $attribute;
        });
    }
    public function update(ItemAttribute $itemAttribute, array $data): ItemAttribute{
        $values = $this->cleanValues($data['values'] ?? []);
        unset($data['values']);
        return DB::transaction(function () use ($itemAttribute, $data, $values) {
            $itemAttribute->update($data);
            $existingValues = $itemAttribute->values()->get()->keyBy('value');
            $keepIds = collect($values)->map(
                fn ($value) => $existingValues->get($value)?->id
                    ?? $itemAttribute->values()->create(['value' => $value])->id
            );
            $toRemove = $itemAttribute->values()
                ->whereNotIn('id', $keepIds)
                ->get();
            $usedValues = $toRemove
                ->filter(fn ($value) => $value->items()->exists())
                ->pluck('value');
            if ($usedValues->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'values' => 'Cannot remove values used by existing items: ' .
                        $usedValues->implode(', '),
                ]);
            }
            $toRemove->each->delete();
            return $itemAttribute;
        });
    }
    public function delete(ItemAttribute $itemAttribute): bool{
        if ($itemAttribute->values()->whereHas('items')->exists()) {
            throw ValidationException::withMessages([
                'name' => 'Cannot delete an attribute that has values used by existing items.',
            ]);
        }
        return (bool) $itemAttribute->delete();
    }
    private function cleanValues(array $values): array{
        $cleaned = [];
        foreach ($values as $value) {
            $value = trim((string) $value);
            if ($value && ! in_array($value, $cleaned)) {
                $cleaned[] = $value;
            }
        }
        return $cleaned;
    }
}
