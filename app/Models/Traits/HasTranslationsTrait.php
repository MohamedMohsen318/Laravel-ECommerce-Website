<?php

namespace App\Models\Traits;

use App\Models\ModelTranslation;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasTranslationsTrait
{
    public function translations(): MorphMany
    {
        return $this->morphMany(ModelTranslation::class, 'model');
    }

    public function translate(string $locale): ?ModelTranslation
    {
        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations()->where('locale', $locale)->first();
    }

    public function setTranslation(string $locale, array $data): ModelTranslation
    {
        $payload = $data[$locale] ?? $data;

        return $this->translations()->updateOrCreate(
            ['locale' => $locale],
            [
                'name' => $payload['name'] ?? '',
                'description' => $payload['description'] ?? null,
            ]
        );
    }

    public function getNameAttribute(): ?string
    {
        return $this->translate(app()->getLocale())?->name
            ?? $this->translate('en')?->name;
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->translate(app()->getLocale())?->description
            ?? $this->translate('en')?->description;
    }
}
