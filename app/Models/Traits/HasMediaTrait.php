<?php

namespace App\Models\Traits;

use App\Enums\MediaType;
use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HasMediaTrait
{
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function setMedia(UploadedFile|string $file, MediaType|string $type, string $directory): Media
    {
        $path = $file instanceof UploadedFile
            ? $file->store($directory, 'public')
            : $file;

        return $this->media()->create([
            'type' => $type instanceof MediaType ? $type->value : $type,
            'file' => $path,
        ]);
    }

    public function getFirstImageUrl(): ?string
    {
        $media = $this->media->firstWhere('type', MediaType::Image->value)
            ?? $this->media()->where('type', MediaType::Image->value)->first();

        return $media ? Storage::disk('public')->url($media->file) : null;
    }
}
