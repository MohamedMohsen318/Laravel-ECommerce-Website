@extends('layouts.app')

@section('content')
    <div class="container">

        <h1>Admins</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <a href="{{ route('admins.admins.create') }}" class="btn btn-primary mb-3">
            Create Admin
        </a>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody>
            @foreach($admins as $admin)
                <tr>
                    <td>{{ $admin->id }}</td>
                    <td>{{ $admin->name }}</td>
                    <td>{{ $admin->email }}</td>
                    <td>{{ $admin->created_at }}</td>

                    <td>
                        <form action="{{ route('admins.admins.destroy', $admin->id) }}"
                              method="POST"
                              onsubmit="return confirm('Are you sure?')">

                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm">
                                Delete
                            </button>

                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            {{ $admins->links() }}
        </div>

    </div>
@endsection
