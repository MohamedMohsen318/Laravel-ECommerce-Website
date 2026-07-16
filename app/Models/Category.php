<?php

namespace App\Models;

use App\Models\Relations\CategoryRelationsTrait;
use App\Models\Traits\HasMediaTrait;
use App\Models\Traits\HasTranslationsTrait;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use CategoryRelationsTrait;
    use HasMediaTrait;
    use HasTranslationsTrait;

    protected $fillable = ['parent_id', 'slug', 'sort_order', 'is_active'];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function fullPath(): string
    {
        return $this->parent ? $this->parent->fullPath() . '/' . $this->slug : $this->slug;
    }
}
