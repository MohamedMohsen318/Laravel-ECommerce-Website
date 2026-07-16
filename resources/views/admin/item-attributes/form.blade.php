<label class="field">
    <span>Name</span>
    <input type="text" name="name" value="{{ old('name', $attribute?->name) }}" required>
</label>

<div class="stack">
    <h2 style="margin:0; font-size:20px">Values</h2>
    @php
        $values = old('values', $attribute?->values->pluck('value')->all() ?? ['']);
        $values = array_pad($values, max(3, count($values) + 1), '');
    @endphp
    @foreach ($values as $index => $value)
        <label class="field">
            <span>Value {{ $index + 1 }}</span>
            <input type="text" name="values[]" value="{{ $value }}">
        </label>
    @endforeach
</div>
