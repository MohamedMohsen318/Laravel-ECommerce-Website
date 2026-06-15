@extends('layouts.app')

@section('title', 'Manage Categories')

@section('content')
    <section class="stack">
        <div class="page-head">
            <h1>Manage Categories</h1>
            <a class="button" href="{{ route('admins.categories.create') }}">Add Category</a>
        </div>

        <div class="card">
            <table class="table">
                <thead>
                    <tr><th>Name</th><th>Slug</th><th>Products</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>{{ $category->translate('en')?->name ?? $category->slug }}</td>
                            <td>{{ $category->slug }}</td>
                            <td>{{ $category->items_count ?? 0 }}</td>
                            <td>{{ $category->is_active ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <div class="actions">
                                    <a class="button secondary" href="{{ route('admins.categories.edit', $category) }}">Edit</a>
                                    <form method="POST" action="{{ route('admins.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="button danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="muted">No categories yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
