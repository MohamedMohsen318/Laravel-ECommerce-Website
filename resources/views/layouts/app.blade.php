<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Commerce Hub')</title>

    <style>
        :root {
            --ink: #172033;
            --muted: #667085;
            --line: #d9dee8;
            --brand: #2563eb;
            --brand-dark: #1e3a8a;
            --accent: #f59e0b;
            --bg: #eef2f7;
            --danger: #dc2626;
            --shadow: 0 18px 45px rgba(23, 32, 51, .08);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Inter, system-ui, sans-serif;
            color: var(--ink);
            background: linear-gradient(180deg, #fff, var(--bg));
        }

        a {
            color: var(--brand);
            text-decoration: none;
            font-weight: 700;
        }

        /* NAV */
        .nav {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 28px;
            background: rgba(255,255,255,.92);
            border-bottom: 1px solid var(--line);
            backdrop-filter: blur(14px);
        }

        .brand {
            font-size: 20px;
            font-weight: 900;
            color: var(--ink);
        }

        .nav-links {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .lang-switch a {
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid var(--line);
        }

        .active-lang {
            background: var(--brand-dark);
            color: #fff !important;
        }

        .container {
            width: min(1180px, 100% - 32px);
            margin: 30px auto;
        }

        .alert {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 12px;
        }

        .alert.success { background: #ecfdf5; color: #047857; }
        .alert.error { background: #fef2f2; color: #b91c1c; }
    </style>
</head>

<body>

<nav class="nav">

    <x-cart-icon />

    {{-- LEFT --}}
    <a class="brand" href="{{ route('home') }}">Commerce Hub</a>

    {{-- CENTER --}}
    <div class="nav-links">
        <a href="{{ route('products.index') }}">Products</a>
        <a href="{{ route('categories.index') }}">Categories</a>
    </div>

    {{-- RIGHT --}}
    <div class="nav-links">

        {{-- LANG SWITCH --}}
        <div class="lang-switch">
            <a class="{{ app()->getLocale() == 'ar' ? 'active-lang' : '' }}"
               href="{{ route('lang.switch','ar') }}">AR</a>

            <a class="{{ app()->getLocale() == 'en' ? 'active-lang' : '' }}"
               href="{{ route('lang.switch','en') }}">EN</a>
        </div>

        {{-- AUTH --}}
        @if (auth(\App\Enums\AuthGuard::Admins->value)->check())

            <a href="{{ route('admins.dashboard') }}">Dashboard</a>

            <form method="POST" action="{{ route('admins.logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>

        @elseif(auth()->check())

            <a href="{{ route('profile') }}">Account</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Logout</button>
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
        <div class="alert error">{{ $errors->first() }}</div>
    @endif

    @yield('content')

</main>

</body>
</html>
