<?php

namespace App\Models\Relations;

use App\Models\ProductComment;
use App\Models\ProductReview;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait ProductFeedbackRelations
{
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ProductComment::class);
    }
}

