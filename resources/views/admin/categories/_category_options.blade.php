@foreach ($categories as $category)
    <option value="{{ $category['id'] }}" @selected(in_array((int) $category['id'], $selectedCategoryIds ?? [], true))>
        {{ str_repeat('-- ', $category['level'] ?? $level ?? 0) }}{{ $category['name'] }}
    </option>
@endforeach
