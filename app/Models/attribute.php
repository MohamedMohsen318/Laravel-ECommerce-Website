<?php

namespace App\Models;

use App\Models\Relations\ItemAttributeRelationsTrait;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use ItemAttributeRelationsTrait;

    protected $table = 'attributes';

    protected $fillable = [
        'name',
    ];
}
