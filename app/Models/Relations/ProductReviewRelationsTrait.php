<?php

namespace App\Models\Relations;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait ProductReviewRelationsTrait
{
    public function item(): BelongsTo{
        return $this->belongsTo(Item::class);
    }
    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
}
