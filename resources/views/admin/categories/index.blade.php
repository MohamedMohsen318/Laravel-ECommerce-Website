@extends('layouts.app')

@section('title','Categories')

@section('content')

    <section class="card">

        <h1>Categories</h1>

        <a class="button" href="{{ route('admins.categories.create') }}">
            Add Category
        </a>

        <table class="table">

            <thead>
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Products</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody>

            @forelse($categories as $cat)

                <tr>
                    <td>
                        {{ str_repeat('-- ', $cat['level']) }}
                        {{ $cat['name'] }}
                    </td>

                    <td>{{ $cat['slug'] }}</td>

                    <td>{{ $cat['items_count'] }}</td>

                    <td>
                        {{ $cat['is_active'] ? 'Active' : 'Inactive' }}
                    </td>

                    <td>
                        <a href="{{ route('admins.categories.edit',$cat['id']) }}">
                            Edit
                        </a>

                        <form method="POST"
                              action="{{ route('admins.categories.destroy',$cat['id']) }}">
                            @csrf
                            @method('DELETE')

                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="5">No categories</td>
                </tr>
            @endforelse

            </tbody>

        </table>

    </section>

@endsection
