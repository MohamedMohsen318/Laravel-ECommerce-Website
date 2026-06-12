@extends('layouts.app')

@section('title', 'Admin Permissions')

@section('content')
    <section class="stack">
        <div class="actions">
            <div style="margin-right: auto;">
                <h1>Admin Permissions</h1>
                <p class="muted">Super admin area for roles and direct permissions.</p>
            </div>
        </div>

        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Admin</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Permissions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($admins as $admin)
                        <tr>
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ $admin->roles->pluck('name')->join(', ') ?: 'No roles' }}</td>
                            <td>{{ $admin->permissions->pluck('name')->join(', ') ?: 'No direct permissions' }}</td>
                            <td>
                                <a class="button secondary" href="{{ route('admin.permissions.edit', $admin) }}">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="muted">No admins found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
