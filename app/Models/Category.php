<?php

namespace App\Models;

use App\Models\Relations\CategoryRelationsTrait;
use App\Models\Traits\HasMediaTrait;
use App\Models\Traits\HasTranslationsTrait;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use CategoryRelationsTrait,
        HasTranslationsTrait,
        HasMediaTrait;

    protected $fillable = [
        'parent_id',
        'sort_order',
        'is_active',
        'slug',
    ];

    public function getTotalItems(): int
    {
        $totalItems = $this->items_count ?? 0;

        foreach ($this->allChildren as $child) {
            $totalItems += $child->getTotalItems();
        }

        return $totalItems;
    }

    public function fullPath(): string
    {
        $slugs = collect();

        $parent = $this->parent;

        while ($parent) {
            $slugs->prepend($parent->slug);
            $parent = $parent->parent;
        }

        return $slugs->push($this->slug)->implode('/');
    }
}
