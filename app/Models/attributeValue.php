<?php

namespace App\Models;

use App\Models\Relations\ItemAttributeValueRelationsTrait;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    use ItemAttributeValueRelationsTrait;

    protected $table = 'attribute_values';

    protected $fillable = [
        'attribute_id',
        'value',
    ];
}
