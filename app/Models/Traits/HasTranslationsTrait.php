<?php

namespace App\Models\Traits;

use App\Models\ModelTranslation;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasTranslationsTrait
{
    public function translations(): MorphMany
    {
        return $this->morphMany(
            ModelTranslation::class,
            'model'
        );
    }

    public function translate($locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations()
            ->where('locale', $locale)
            ->first();
    }

    public function setTranslation($locale, $content): void
    {
        $data = [
            'name' => $content[$locale]['name'] ?? null,
            'description' => $content[$locale]['description'] ?? null,
        ];

        $this->translations()->updateOrCreate(
            ['locale' => $locale],
            $data
        );
    }
}
