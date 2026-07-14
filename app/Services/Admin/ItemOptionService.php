<?php

namespace App\Services\Admin;

use App\Models\ItemOption;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ItemOptionService
{
    public function getAll(): Collection
    {
        return ItemOption::with('values')
            ->orderBy('name')
            ->get();
    }

    public function create(array $data): ItemOption{
        $values = $this->cleanValues($data['values'] ?? []);
        unset($data['values']);
        return DB::transaction(function () use ($data, $values) {
            $option = ItemOption::create($data);
            foreach ($values as $value) {
                $option->values()->create([
                    'value' => $value,
                ]);
            }
            return $option;
        });
    }
    public function update(ItemOption $itemOption, array $data): ItemOption{
        $newValues = $this->cleanValues($data['values'] ?? []);
        unset($data['values']);
        return DB::transaction(function () use ($itemOption, $data, $newValues) {
            $itemOption->update($data);
            $existingValues = $itemOption->values()->get()->keyBy('value');
            $keepIds = collect($newValues)->map(
                fn ($value) => $existingValues->get($value)?->id
                    ?? $itemOption->values()->create(['value' => $value])->id
            );
            $toRemove = $itemOption->values()->whereNotIn('id', $keepIds)->get();
            $usedValues = $toRemove->filter(fn ($v) => $v->variants()->exists())->pluck('value');
            if ($usedValues->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'values' => 'Cannot remove values used by existing variants: ' . $usedValues->implode(', '),
                ]);
            }
            $toRemove->each->delete();
            return $itemOption;
        });
    }

    public function delete(ItemOption $itemOption): bool{
        return DB::transaction(function () use ($itemOption) {
            $hasUsedValues = $itemOption->values()
                ->whereHas('variants')
                ->exists();
            if ($hasUsedValues) {
                throw ValidationException::withMessages([
                    'name' => 'Cannot delete an option that has values used by existing variants.',
                ]);
            }
            return $itemOption->delete();
        });
    }
    private function cleanValues(array $values): array{
        return collect($values)
            ->map(fn ($value) => trim((string) $value))
            ->filter()->unique()->values()
            ->all();
    }
}
