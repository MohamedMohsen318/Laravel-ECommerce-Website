@extends('layouts.app')

@section('content')
    <div class="container">

        <h1>Create Admin</h1>

        <form action="{{ route('admins.admins.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                @error('name')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                @error('email')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                @error('password')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>

            <button class="btn btn-success">
                Save
            </button>

            <a href="{{ route('admins.admins.index') }}" class="btn btn-secondary">
                Back
            </a>

        </form>

    </div>
@endsection
