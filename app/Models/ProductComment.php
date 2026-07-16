<?php

namespace App\Models;

use App\Models\Relations\ProductCommentRelationsTrait;
use Illuminate\Database\Eloquent\Model;

class ProductComment extends Model
{
    use ProductCommentRelationsTrait;

    protected $fillable = ['item_id', 'user_id', 'parent_id', 'body'];
}
