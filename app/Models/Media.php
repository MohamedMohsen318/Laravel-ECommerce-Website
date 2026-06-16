<?php

namespace App\Models;

use App\Enums\MediaType;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'model_id',
        'model_type',
        'type',
        'file',
    ];

    public function model()
    {
        return $this->morphTo();
    }

    protected $casts = [
        'type' => MediaType::class,
    ];
}
