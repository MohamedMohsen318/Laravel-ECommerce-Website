<?php

namespace App\Models;

use App\Models\Relations\ItemOptionRelationsTrait;
use Illuminate\Database\Eloquent\Model;

class ItemOption extends Model
{
    use ItemOptionRelationsTrait;

    protected $fillable = [
        'name',
    ];
}
