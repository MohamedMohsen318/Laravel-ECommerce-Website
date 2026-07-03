<?php

namespace App\Models\Relations;

use App\Models\Item;
use App\Models\ProductComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait ProductCommentRelationsTrait
{
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProductComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ProductComment::class, 'parent_id')
            ->with('replies', 'user')
            ->oldest();
    }
}
