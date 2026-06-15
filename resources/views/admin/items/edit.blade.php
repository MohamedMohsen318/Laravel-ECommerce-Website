@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
    <section class="stack">
        <div class="page-head">
            <h1>Edit Product</h1>
            <a class="button secondary" href="{{ route('admins.items.index') }}">Back to Products</a>
        </div>

        <div class="card">
            <form class="form" method="POST" action="{{ route('admins.items.update', $item) }}">
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
                <label class="checkbox">
                    <input type="checkbox" name="is_active" {{ $item->is_active ? 'checked' : '' }}>
                    <span>Active</span>
                </label>
                <button class="button" type="submit">Update Product</button>
            </form>
        </div>
    </section>
@endsection
