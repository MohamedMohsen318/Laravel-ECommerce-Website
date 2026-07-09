@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
    <section class="stack">
        <div class="page-head">
            <h1>Edit Product</h1>
            <a class="button secondary" href="{{ route('admins.items.index') }}">Back to Products</a>
        </div>

        <div class="card">
            <form class="form" method="POST" action="{{ route('admins.items.update', $item) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <label class="field">
                    <span>Product name</span>
                    <input type="text" name="name" value="{{ old('name', $item->name) }}">
                </label>
                <label class="field">
                    <span>Description</span>
                    <textarea name="description">{{ old('description', $item->description) }}</textarea>
                </label>
                <label class="field">
                    <span>Price</span>
                    <input type="number" name="price" value="{{ old('price', $item->price) }}" step="0.01">
                </label>
                <label class="field">
                    <span>Stock</span>
                    <input type="number" name="stock" value="{{ old('stock', $item->stock) }}">
                </label>
                <label class="field">
                    <span>Status</span>
                    <select name="status">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(old('status', $item->status->value) === $status->value)>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <label class="field">
                    <span>Categories</span>
                    <select name="category_ids[]" multiple required>
                        @include('admin.categories._category_options', [
                            'categories' => $selectCategories,
                            'selectedCategoryIds' => array_map('intval', old('category_ids', $item->categories->pluck('id')->all())),
                            'level' => 0,
                        ])
                    </select>
                </label>
                @if ($item->getFirstImageUrl())
                    <a href="{{ $item->getFirstImageUrl() }}" target="_blank" rel="noopener">
                        <img class="card-media" src="{{ $item->getFirstImageUrl() }}" alt="{{ $item->name }}">
                    </a>
                @endif
                <label class="field">
                    <span>Image</span>
                    <input class="input" type="file" name="image" accept="image/*">
                </label>
                <label class="checkbox">
                    <input type="checkbox" name="is_active" {{ $item->is_active ? 'checked' : '' }}>
                    <span>Active</span>
                </label>
                <div class="stack">
                    <h2 style="margin:0; font-size:20px">Variants</h2>
                    @php
                        $variantRows = old('variants', $item->variants->map(fn ($variant) => [
                            'id' => $variant->id,
                            'sku' => $variant->sku,
                            'price' => $variant->price,
                            'discount_price' => $variant->discount_price,
                            'stock' => $variant->stock,
                            'is_active' => $variant->is_active,
                            'option_value_ids' => $variant->optionValues->pluck('id')->all(),
                        ])->all());

                        $variantRows = array_pad($variantRows, count($variantRows) + 2, []);
                    @endphp
                    @foreach ($variantRows as $i => $variant)
                        <div class="card">
                            <div class="form">
                                <input type="hidden" name="variants[{{ $i }}][id]" value="{{ $variant['id'] ?? '' }}">
                                <label class="field">
                                    <span>Variant SKU</span>
                                    <input type="text" name="variants[{{ $i }}][sku]" value="{{ $variant['sku'] ?? '' }}">
                                </label>
                                <label class="field">
                                    <span>Variant price</span>
                                    <input type="number" name="variants[{{ $i }}][price]" value="{{ $variant['price'] ?? '' }}" step="0.01">
                                </label>
                                <label class="field">
                                    <span>Discount price</span>
                                    <input type="number" name="variants[{{ $i }}][discount_price]" value="{{ $variant['discount_price'] ?? '' }}" step="0.01">
                                </label>
                                <label class="field">
                                    <span>Variant stock</span>
                                    <input type="number" name="variants[{{ $i }}][stock]" value="{{ $variant['stock'] ?? '' }}">
                                </label>
                                <label class="field">
                                    <span>Option values</span>
                                    <select name="variants[{{ $i }}][option_value_ids][]" multiple>
                                        @foreach ($itemOptions as $option)
                                            <optgroup label="{{ $option->name }}">
                                                @foreach ($option->values as $value)
                                                    <option value="{{ $value->id }}" @selected(in_array($value->id, $variant['option_value_ids'] ?? []))>
                                                        {{ $value->value }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </label>
                                <label class="checkbox">
                                    <input type="checkbox" name="variants[{{ $i }}][is_active]" value="1" @checked($variant['is_active'] ?? true)>
                                    <span>Active variant</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button class="button" type="submit">Update Product</button>
            </form>
        </div>
    </section>
@endsection
