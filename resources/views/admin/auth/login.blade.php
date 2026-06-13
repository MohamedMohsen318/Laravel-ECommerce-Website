@extends('layouts.app')

@section('title', 'Admin Login')

@section('content')
    <section class="card">
        <h1>Admin Login</h1>
        <form class="form" method="POST" action="{{ route('admins.login') }}">
            @csrf
            <label class="field">
                <span>Email</span>
                <input class="input" type="email" name="email" value="{{ old('email') }}" required autofocus>
            </label>
            <label class="field">
                <span>Password</span>
                <input class="input" type="password" name="password" required>
            </label>
            <label class="checkbox">
                <input type="checkbox" name="remember" value="1">
                <span>Remember me</span>
            </label>
            <button class="button" type="submit">Login</button>
        </form>
    </section>
@endsection
