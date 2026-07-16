<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelTranslation extends Model
{
    protected $table = 'model_translations';

    protected $fillable = ['model_id', 'model_type', 'locale', 'name', 'description'];
}
