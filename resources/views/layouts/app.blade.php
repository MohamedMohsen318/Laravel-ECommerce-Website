<!doctype html>
<html lang="en">
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
            --surface: #ffffff;
            --soft: #f3f6fb;
            --bg: #eef2f7;
            --danger: #dc2626;
            --shadow: 0 18px 45px rgba(23, 32, 51, .08);
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--ink);
            background:
                linear-gradient(180deg, rgba(255,255,255,.78), rgba(238,242,247,.9)),
                radial-gradient(circle at top left, rgba(245,158,11,.16), transparent 36%),
                var(--bg);
        }
        a { color: var(--brand); text-decoration: none; font-weight: 700; }
        h1, h2, h3, p { letter-spacing: 0; }
        h1 { font-size: clamp(30px, 4vw, 46px); line-height: 1.05; margin: 0; }
        h2 { margin: 0 0 14px; }

        .nav {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            padding: 14px 32px;
            background: rgba(255,255,255,.9);
            border-bottom: 1px solid var(--line);
            backdrop-filter: blur(14px);
        }
        .brand {
            color: var(--ink);
            font-size: 20px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .brand::before {
            content: "";
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--brand), var(--accent));
            box-shadow: 0 10px 24px rgba(37,99,235,.22);
        }
        .nav-links { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .nav-links a { color: #344054; padding: 8px 10px; border-radius: 6px; }
        .nav-links a:hover { background: var(--soft); color: var(--brand-dark); }
        .nav-link-featured { background: var(--brand-dark) !important; color: #fff !important; }

        .container { width: min(1120px, calc(100% - 32px)); margin: 34px auto; }
        .card {
            background: rgba(255,255,255,.96);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 24px;
            box-shadow: var(--shadow);
        }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 16px; }
        .form { display: grid; gap: 16px; max-width: 580px; }
        .field { display: grid; gap: 6px; }
        .input, .select, input, textarea, select {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 11px 12px;
            font: inherit;
            background: #fff;
        }
        textarea { min-height: 120px; resize: vertical; }
        .button {
            border: 0;
            border-radius: 6px;
            padding: 10px 14px;
            background: var(--brand);
            color: #fff;
            cursor: pointer;
            font: inherit;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            min-height: 40px;
        }
        .button:hover { filter: brightness(.96); }
        .button.secondary { background: #e8eef8; color: var(--ink); }
        .button.danger { background: var(--danger); }
        .alert { padding: 12px 14px; border-radius: 6px; margin-bottom: 16px; border: 1px solid transparent; }
        .alert.success { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
        .alert.error { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
        .table { width: 100%; border-collapse: collapse; background: #fff; }
        .table th, .table td { border-bottom: 1px solid #e5e7eb; padding: 13px 12px; text-align: left; vertical-align: top; }
        .table th { color: #475467; font-size: 13px; text-transform: uppercase; }
        .actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .muted { color: var(--muted); }
        .stack { display: grid; gap: 18px; }
        .checkbox { display: flex; gap: 8px; align-items: center; }
        .page-head { display: flex; justify-content: space-between; align-items: end; gap: 16px; flex-wrap: wrap; }
        .page-head p { margin: 10px 0 0; max-width: 620px; line-height: 1.6; }

        @media (max-width: 760px) {
            .nav { align-items: flex-start; flex-direction: column; padding: 14px 18px; }
            .container { margin: 22px auto; }
            .card { padding: 18px; }
            .table { display: block; overflow-x: auto; }
        }
    </style>
</head>
<body>
    <nav class="nav">
        <a class="brand" href="{{ route('home') }}">Commerce Hub</a>
        <div class="nav-links">
            <a href="{{ route('products.index') }}">Products</a>
            <a href="{{ route('categories.index') }}">Categories</a>
            @if (auth(\App\Enums\AuthGuard::Admins->value)->check())
                <a href="{{ route('admins.dashboard') }}">Dashboard</a>
                <a href="{{ route('admins.items.index') }}">Manage Products</a>
                <a href="{{ route('admins.categories.index') }}">Manage Categories</a>
                @if (auth(\App\Enums\AuthGuard::Admins->value)->user()?->hasRole(\App\Enums\AdminRole::SuperAdmin->value))
                    <a class="nav-link-featured" href="{{ route('admins.admins.create') }}">Add Admin</a>
                    <a href="{{ route('admins.admins.index') }}">Admins</a>
                    <a href="{{ route('admins.permissions.index') }}">Permissions</a>
                @endif
                <form method="POST" action="{{ route('admins.logout') }}">
                    @csrf
                    <button class="button secondary" type="submit">Log out</button>
                </form>
            @elseif (auth()->check())
                <a href="{{ route('profile') }}">My Account</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="button secondary" type="submit">Log out</button>
                </form>
            @else
                <a href="{{ route('login') }}">Log in</a>
                <a class="nav-link-featured" href="{{ route('register') }}">Create Account</a>
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
