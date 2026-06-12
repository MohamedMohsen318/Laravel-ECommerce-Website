@extends('layouts.app')

@section('title', 'Edit Admin Permissions')

@section('content')
    <section class="card">
        <h1>Edit Permissions</h1>
        <p class="muted">{{ $admin->name }} - {{ $admin->email }}</p>

        <form class="form" method="POST" action="{{ route('admin.permissions.update', $admin) }}">
            @csrf
            @method('PUT')

            <div class="field">
                <span>Roles</span>
                @foreach ($roles as $role)
                    <label class="checkbox">
                        <input type="checkbox" name="roles[]" value="{{ $role->name }}" @checked($admin->hasRole($role->name))>
                        <span>{{ $role->name }}</span>
                    </label>
                @endforeach
            </div>

            <div class="field">
                <span>Direct permissions</span>
                @foreach ($permissions as $permission)
                    <label class="checkbox">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked($admin->hasDirectPermission($permission->name))>
                        <span>{{ $permission->name }}</span>
                    </label>
                @endforeach
            </div>

            <div class="actions">
                <button class="button" type="submit">Save permissions</button>
                <a class="button secondary" href="{{ route('admin.permissions.index') }}">Cancel</a>
            </div>
        </form>
    </section>
@endsection
