<?php

namespace App\Models;

use App\Models\Relations\ItemAttributeValueRelationsTrait;
use Illuminate\Database\Eloquent\Model;

class ItemAttributeValue extends Model
{
    use ItemAttributeValueRelationsTrait;

    protected $fillable = ['item_attribute_id', 'value'];
}
