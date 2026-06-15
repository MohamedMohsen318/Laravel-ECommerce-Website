@csrf

<label class="field">
    <span>Parent category</span>
    <select class="select" name="parent_id">
        <option value="">No parent</option>
        @foreach ($selectCategories as $selectCategory)
            <option value="{{ $selectCategory->id }}" @selected(old('parent_id', $category->parent_id ?? null) == $selectCategory->id)>
                {{ $selectCategory->translate('en')?->name ?? $selectCategory->slug }}
            </option>
        @endforeach
    </select>
</label>

<label class="checkbox">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true))>
    <span>Active</span>
</label>

<label class="field">
    <span>Name</span>
    <input class="input" type="text" name="translations[en][name]" value="{{ old('translations.en.name', $category?->translate('en')?->name ?? '') }}" required>
</label>

<label class="field">
    <span>Description</span>
    <textarea name="translations[en][description]" rows="3">{{ old('translations.en.description', $category?->translate('en')?->description ?? '') }}</textarea>
</label>

<input type="hidden" name="translations[ar][name]" value="">
<input type="hidden" name="translations[ar][description]" value="">

<label class="field">
    <span>Image</span>
    <input class="input" type="file" name="image" accept="image/*">
</label>

<button class="button" type="submit">{{ $buttonText }}</button>
