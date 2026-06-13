@extends('layouts.app')

@section('title', 'Admin Categories')

@section('content')
    <section class="stack">
        <div class="actions">
            <h1 style="margin-right: auto;">Categories</h1>
            <a class="button" href="{{ route('admins.categories.create') }}">Create category</a>
        </div>

        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Items</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>{{ $category->translate('en')?->name ?? $category->slug }}</td>
                            <td>{{ $category->slug }}</td>
                            <td>{{ $category->getTotalItems() }}</td>
                            <td>{{ $category->is_active ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <div class="actions">
                                    <a class="button secondary" href="{{ route('admins.categories.edit', $category) }}">Edit</a>
                                    {{-- FIX #10: إضافة confirmation قبل الحذف --}}
                                    <form method="POST" action="{{ route('admins.categories.destroy', $category) }}"
                                          onsubmit="return confirm('Are you sure you want to delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="button danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="muted">No categories yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
