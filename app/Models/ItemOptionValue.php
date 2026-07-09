<?php

namespace App\Models;

use App\Models\Relations\ItemOptionValueRelationsTrait;
use Illuminate\Database\Eloquent\Model;

class ItemOptionValue extends Model
{
    use ItemOptionValueRelationsTrait;

    protected $fillable = [
        'item_option_id',
        'value',
    ];
}
