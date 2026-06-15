<?php

namespace App\Services\Admin;

use App\Models\Item;

class ItemService
{
    public function getAll()
    {
        return Item::orderByDesc('id')->get();
    }

    public function find(int $id): Item
    {
        return Item::findOrFail($id);
    }

    public function create(array $data): Item
    {
        $translation = $this->extractTranslation($data);
        $item = Item::create($data);
        $item->setTranslation('en', ['en' => $translation]);

        return $item;
    }

    public function update(Item $item, array $data): Item
    {
        $translation = $this->extractTranslation($data);
        $item->update($data);
        $item->setTranslation('en', ['en' => $translation]);

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
}
