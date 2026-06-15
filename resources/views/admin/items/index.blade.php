@extends('layouts.app')

@section('title', 'Manage Products')

@section('content')
    <section class="stack">
        <div class="page-head">
            <h1>Manage Products</h1>
            <a class="button" href="{{ route('admins.items.create') }}">Add Product</a>
        </div>

        <div class="card">
            <table class="table">
                <thead>
                    <tr><th>Product</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->price }} EGP</td>
                            <td>{{ $item->stock }}</td>
                            <td>{{ $item->status->label() }}</td>
                            <td>
                                <div class="actions">
                                    <a class="button secondary" href="{{ route('admins.items.edit', $item) }}">Edit</a>
                                    <form method="POST" action="{{ route('admins.items.destroy', $item) }}" onsubmit="return confirm('Delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="button danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="muted">No products yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
