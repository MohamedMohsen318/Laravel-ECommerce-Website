@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <section class="card">
        <h1>Profile</h1>
        <form class="form" method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')
            <label class="field">
                <span>Name</span>
                <input class="input" type="text" name="name" value="{{ old('name', $user->name) }}" required>
            </label>
            <label class="field">
                <span>Email</span>
                <input class="input" type="email" name="email" value="{{ old('email', $user->email) }}" required>
            </label>
            <label class="field">
                <span>Phone</span>
                <input class="input" type="text" name="phone" value="{{ old('phone', $user->phone) }}">
            </label>
            <label class="field">
                <span>Address</span>
                <textarea name="address" rows="3">{{ old('address', $user->address) }}</textarea>
            </label>
                <label class="field">
                    <span>New password</span>
                    <input class="input" type="password" name="password">
                </label>
            <label class="field">
                <span>Confirm new password</span>
                <input class="input" type="password" name="password_confirmation">
            </label>
            <button class="button" type="submit">Save profile</button>
        </form>
    </section>
@endsection
