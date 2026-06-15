@extends('layouts.app')

@section('title', 'Add Product')

@section('content')
    <section class="stack">
        <div class="page-head">
            <h1>Add Product</h1>
            <a class="button secondary" href="{{ route('admins.items.index') }}">Back to Products</a>
        </div>

        <div class="card">
            <form class="form" method="POST" action="{{ route('admins.items.store') }}">
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
                <button class="button" type="submit">Save Product</button>
            </form>
        </div>
    </section>
@endsection
