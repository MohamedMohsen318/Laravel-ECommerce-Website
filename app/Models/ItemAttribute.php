<?php

namespace App\Models;

use App\Models\Relations\ItemAttributeRelationsTrait;
use Illuminate\Database\Eloquent\Model;

class ItemAttribute extends Model
{
    use ItemAttributeRelationsTrait;

    protected $fillable = ['name'];
}
