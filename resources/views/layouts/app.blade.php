<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'Laravel Store'))</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; color: #1f2937; background: #f8fafc; }
        a { color: #2563eb; text-decoration: none; }
        .nav { display: flex; justify-content: space-between; align-items: center; padding: 16px 32px; background: #fff; border-bottom: 1px solid #e5e7eb; }
        .nav-links { display: flex; align-items: center; gap: 16px; }
        .container { width: min(1080px, calc(100% - 32px)); margin: 32px auto; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
        .form { display: grid; gap: 16px; max-width: 560px; }
        .field { display: grid; gap: 6px; }
        .input, .select, textarea { width: 100%; box-sizing: border-box; border: 1px solid #d1d5db; border-radius: 6px; padding: 10px 12px; font: inherit; background: #fff; }
        .button { border: 0; border-radius: 6px; padding: 10px 14px; background: #111827; color: #fff; cursor: pointer; font: inherit; display: inline-flex; align-items: center; justify-content: center; }
        .button.secondary { background: #e5e7eb; color: #111827; }
        .button.danger { background: #dc2626; }
        .alert { padding: 12px 14px; border-radius: 6px; margin-bottom: 16px; }
        .alert.success { background: #dcfce7; color: #166534; }
        .alert.error { background: #fee2e2; color: #991b1b; }
        .table { width: 100%; border-collapse: collapse; background: #fff; }
        .table th, .table td { border-bottom: 1px solid #e5e7eb; padding: 12px; text-align: left; vertical-align: top; }
        .actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .muted { color: #6b7280; }
        .stack { display: grid; gap: 16px; }
        .checkbox { display: flex; gap: 8px; align-items: center; }
    </style>
</head>
<body>
    <nav class="nav">
        <a href="{{ route('home') }}">{{ config('app.name', 'Laravel Store') }}</a>
        <div class="nav-links">
            <a href="{{ route('categories.index') }}">Categories</a>
            @if (auth('admin')->check())
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('admin.categories.index') }}">Admin Categories</a>
                @if (auth('admin')->user()?->hasRole('super-admin'))
                    <a href="{{ route('admin.permissions.index') }}">Permissions</a>
                @endif
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="button secondary" type="submit">Logout</button>
                </form>
            @elseif (auth()->check())
                <a href="{{ route('profile') }}">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="button secondary" type="submit">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
            @endif
        </div>
    </nav>

    <main class="container">
        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert error">
                {{ $errors->first() }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
