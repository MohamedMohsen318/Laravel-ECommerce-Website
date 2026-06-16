<?php

namespace App\Models\Relations;

use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait ItemRelationsTrait
{
    public function categories(): BelongsToMany{
        return $this->belongsToMany(
            related: Category::class,
            table: 'category_item'
        );
    }
}
