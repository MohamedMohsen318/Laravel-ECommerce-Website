<?php

namespace App\Models;

use App\Models\Relations\ProductReviewRelationsTrait;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use ProductReviewRelationsTrait;

    protected $table = 'product_reviews';

    protected $fillable = [
        'item_id',
        'user_id',
        'rating',
        'body',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];
}
