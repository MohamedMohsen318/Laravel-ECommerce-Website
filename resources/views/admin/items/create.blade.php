@extends('layouts.app')

@section('title', 'Add Product')

@section('content')
    <section class="stack">
        <div class="page-head">
            <h1>Add Product</h1>
            <a class="button secondary" href="{{ route('admins.items.index') }}">Back to Products</a>
        </div>

        <div class="card">
            <form class="form" method="POST" action="{{ route('admins.items.store') }}" enctype="multipart/form-data">
                @csrf
                <label class="field">
                    <span>Product name</span>
                    <input type="text" name="name" placeholder="Product name" value="{{ old('name') }}">
                </label>
                <label class="field">
                    <span>Description</span>
                    <textarea name="description" placeholder="Product description">{{ old('description') }}</textarea>
                </label>
                <label class="field">
                    <span>Price</span>
                    <input type="number" name="price" placeholder="Price" value="{{ old('price') }}" step="0.01">
                </label>
                <label class="field">
                    <span>Stock</span>
                    <input type="number" name="stock" placeholder="Stock" value="{{ old('stock') }}">
                </label>
                <label class="field">
                    <span>Status</span>
                    <select name="status">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(old('status') === $status->value)>
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
                            'selectedCategoryIds' => array_map('intval', old('category_ids', [])),
                            'level' => 0,
                        ])
                    </select>
                </label>
                <label class="field">
                    <span>Image</span>
                    <input class="input" type="file" name="image" accept="image/*">
                </label>
                <div class="stack">
                    <h2 style="margin:0; font-size:20px">Variants</h2>
                    @for ($i = 0; $i < 3; $i++)
                        <div class="card">
                            <div class="form">
                                <label class="field">
                                    <span>Variant SKU</span>
                                    <input type="text" name="variants[{{ $i }}][sku]" value="{{ old("variants.$i.sku") }}">
                                </label>
                                <label class="field">
                                    <span>Variant price</span>
                                    <input type="number" name="variants[{{ $i }}][price]" value="{{ old("variants.$i.price") }}" step="0.01">
                                </label>
                                <label class="field">
                                    <span>Discount price</span>
                                    <input type="number" name="variants[{{ $i }}][discount_price]" value="{{ old("variants.$i.discount_price") }}" step="0.01">
                                </label>
                                <label class="field">
                                    <span>Variant stock</span>
                                    <input type="number" name="variants[{{ $i }}][stock]" value="{{ old("variants.$i.stock") }}">
                                </label>
                                <label class="field">
                                    <span>Attribute values</span>
                                    <select name="variants[{{ $i }}][attribute_value_ids][]" multiple>
                                        @foreach ($itemAttributes as $attribute)
                                            <optgroup label="{{ $attribute->name }}">
                                                @foreach ($attribute->values as $value)
                                                    <option value="{{ $value->id }}" @selected(in_array($value->id, old("variants.$i.attribute_value_ids", [])))>
                                                        {{ $value->value }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </label>
                                <label class="checkbox">
                                    <input type="checkbox" name="variants[{{ $i }}][is_active]" value="1" @checked(old("variants.$i.is_active", true))>
                                    <span>Active variant</span>
                                </label>
                            </div>
                        </div>
                    @endfor
                </div>
                <button class="button" type="submit">Save Product</button>
            </form>
        </div>
    </section>
@endsection
