<!doctype html>
@php
    $locale = app()->getLocale();
    $isArabic = $locale === 'ar';
    $label = fn (string $en, string $ar) => $isArabic ? $ar : $en;
@endphp
<html lang="{{ $locale }}" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Commerce Hub')</title>

    <style>
        :root {
            color-scheme: light;
            --ink: #151821;
            --muted: #687080;
            --line: #dde3ec;
            --brand: #0f766e;
            --brand-dark: #115e59;
            --accent: #e11d48;
            --gold: #d97706;
            --surface: #ffffff;
            --surface-2: #f7f9fc;
            --soft: #eef7f6;
            --bg: #f4f1ec;
            --danger: #dc2626;
            --success: #047857;
            --shadow: 0 18px 38px rgba(21, 24, 33, .09);
            --radius: 8px;
        }

        * { box-sizing: border-box; }

        html { min-height: 100%; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: {{ $isArabic ? '"Segoe UI", Tahoma, Arial, sans-serif' : 'Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif' }};
            color: var(--ink);
            background:
                linear-gradient(145deg, rgba(15, 118, 110, .11), transparent 34%),
                linear-gradient(315deg, rgba(225, 29, 72, .08), transparent 32%),
                var(--bg);
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(21, 24, 33, .035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(21, 24, 33, .035) 1px, transparent 1px);
            background-size: 34px 34px;
            mask-image: linear-gradient(to bottom, rgba(0,0,0,.75), transparent 72%);
        }

        a {
            color: var(--brand);
            text-decoration: none;
            font-weight: 800;
        }

        h1, h2, h3, p { margin-top: 0; }
        h1 { font-size: clamp(30px, 4vw, 50px); line-height: 1.06; letter-spacing: 0; }
        h2 { font-size: clamp(22px, 2vw, 30px); }
        h3 { font-size: 18px; }
        p { line-height: 1.75; }

        input, select, textarea, button {
            font: inherit;
        }

        input, select, textarea {
            width: 100%;
            min-height: 42px;
            border: 1px solid var(--line);
            border-radius: 7px;
            padding: 10px 12px;
            color: var(--ink);
            background: #fff;
            outline: none;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, .14);
        }

        img { max-width: 100%; }

        .nav {
            position: sticky;
            top: 0;
            z-index: 10;
            display: grid;
            grid-template-columns: auto 1fr;
            align-items: center;
            gap: 18px;
            padding: 14px clamp(16px, 4vw, 38px);
            background: rgba(255,255,255,.88);
            border-bottom: 1px solid rgba(221, 227, 236, .9);
            backdrop-filter: blur(18px);
        }

        .brand {
            color: var(--ink);
            font-size: 20px;
            font-weight: 950;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
        }

        .brand::before {
            content: "";
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background:
                linear-gradient(135deg, var(--brand) 0 49%, transparent 50%),
                linear-gradient(315deg, var(--accent) 0 49%, var(--gold) 50%);
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.42);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .nav-links:last-child {
            justify-content: flex-end;
        }

        .nav-links a:not(.button):not(.brand) {
            color: #3b4352;
            padding: 9px 10px;
            border-radius: 7px;
        }

        .nav-links a:not(.button):not(.brand):hover {
            background: var(--soft);
            color: var(--brand-dark);
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 0;
            border-radius: 7px;
            padding: 10px 15px;
            background: var(--brand);
            color: #fff;
            cursor: pointer;
            font: inherit;
            font-weight: 900;
            min-height: 42px;
            box-shadow: 0 8px 18px rgba(15, 118, 110, .18);
        }

        .button:hover { background: var(--brand-dark); color: #fff; }

        .button.secondary {
            background: #fff;
            color: var(--ink);
            border: 1px solid var(--line);
            box-shadow: none;
        }

        .button.secondary:hover {
            background: var(--surface-2);
            color: var(--brand-dark);
        }

        .button.danger {
            background: var(--danger);
            color: #fff;
            box-shadow: 0 8px 18px rgba(220, 38, 38, .14);
        }

        .container {
            position: relative;
            width: min(1200px, calc(100% - 32px));
            margin: 32px auto 54px;
        }

        .stack {
            display: grid;
            gap: 18px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 18px;
        }

        .card {
            background: rgba(255,255,255,.94);
            border: 1px solid rgba(221, 227, 236, .95);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
        }

        .page-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 18px;
            flex-wrap: wrap;
            padding: 24px;
            border: 1px solid rgba(221, 227, 236, .9);
            border-radius: var(--radius);
            background: linear-gradient(135deg, rgba(255,255,255,.96), rgba(238,247,246,.9));
            box-shadow: var(--shadow);
        }

        .page-head h1 { margin-bottom: 8px; }
        .muted { color: var(--muted); }
        .text-center { text-align: center; }

        .alert {
            padding: 13px 15px;
            border-radius: 7px;
            margin-bottom: 16px;
            border: 1px solid transparent;
            font-weight: 800;
        }

        .alert.success {
            background: #ecfdf5;
            color: var(--success);
            border-color: #bbf7d0;
        }

        .alert.error {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #fecaca;
        }

        .form, .field {
            display: grid;
            gap: 8px;
        }

        .field span, .field label, label.field {
            font-weight: 850;
        }

        .checkbox {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            font-weight: 750;
        }

        .checkbox input {
            width: 17px;
            min-height: 17px;
        }

        .actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .flex-between {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
        }

        .table th,
        .table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--line);
            text-align: start;
            vertical-align: middle;
        }

        .table th {
            background: var(--surface-2);
            color: #4b5563;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0;
        }

        .table tr:last-child td { border-bottom: 0; }

        .pill-list {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-height: 30px;
            padding: 5px 10px;
            border-radius: 999px;
            background: var(--soft);
            color: var(--brand-dark);
            border: 1px solid rgba(15, 118, 110, .16);
            font-size: 13px;
            font-weight: 850;
        }

        .catalog-card {
            display: grid;
            grid-template-rows: 210px 1fr;
            padding: 0;
            overflow: hidden;
        }

        .catalog-body {
            display: grid;
            gap: 14px;
            padding: 17px;
        }

        .catalog-title {
            margin-bottom: 7px;
            font-size: 21px;
        }

        .catalog-description {
            margin: 0;
            min-height: 50px;
        }

        .catalog-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: auto;
        }

        .media-link {
            display: block;
            color: inherit;
        }

        .card-media,
        .image-placeholder {
            width: 100%;
            height: 100%;
            min-height: 210px;
            object-fit: cover;
            background: linear-gradient(135deg, #d1fae5, #fee2e2 48%, #fde68a);
        }

        .image-placeholder {
            display: grid;
            place-items: center;
            color: #4b5563;
            font-weight: 900;
        }

        .detail-layout {
            display: grid;
            grid-template-columns: minmax(280px, .9fr) minmax(0, 1.1fr);
            gap: 20px;
            align-items: start;
        }

        .detail-media {
            overflow: hidden;
            border-radius: var(--radius);
            border: 1px solid var(--line);
            min-height: 420px;
            background: #fff;
            box-shadow: var(--shadow);
        }

        .detail-media .card-media,
        .detail-media .image-placeholder {
            min-height: 420px;
        }

        .price {
            color: var(--accent);
            font-size: 22px;
            font-weight: 950;
        }

        .stock {
            margin: 5px 0 0;
            color: var(--muted);
            font-weight: 750;
        }

        .pagination {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .hidden {
            display: none;
        }

        .link-button {
            border: 0;
            background: transparent;
            padding: 0;
            color: var(--brand);
            cursor: pointer;
            font: inherit;
            font-weight: 850;
            text-decoration: underline;
        }

        .reviews-header,
        .rating-summary,
        .comment-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .reviews-header {
            justify-content: space-between;
        }

        .rating-stars {
            color: var(--gold);
            font-weight: 950;
            letter-spacing: 0;
        }

        .review-item,
        .comment-item {
            display: grid;
            gap: 10px;
            padding: 14px;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            background: var(--surface);
        }

        .rating-input {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .rating-input label {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            width: auto;
            font-weight: 850;
        }

        .rating-input input {
            width: 17px;
            min-height: 17px;
        }

        @media (max-width: 920px) {
            .nav {
                grid-template-columns: 1fr;
            }

            .nav-links,
            .nav-links:last-child {
                justify-content: center;
            }

            .detail-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .container {
                width: min(100% - 20px, 1200px);
                margin-top: 18px;
            }

            .nav {
                padding: 12px 10px;
            }

            .nav-links a:not(.button):not(.brand),
            .button {
                padding: 9px 10px;
                font-size: 14px;
            }

            .card,
            .page-head {
                padding: 16px;
            }

            .table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>

<body>

<nav class="nav">

    <div class="nav-links">
        <a class="brand" href="{{ route('home') }}">{{ $label('Commerce Hub', 'كومرس هب') }}</a>
        <a class="button secondary"
           href="{{ url('/lang/' . ($isArabic ? 'en' : 'ar')) }}">
            {{ $isArabic ? 'English' : 'عربي' }}
        </a>
    </div>

    <div class="nav-links">
        <a href="{{ route('products.index') }}">{{ $label('Products', 'المنتجات') }}</a>
        <a href="{{ route('categories.index') }}">{{ $label('Categories', 'الأقسام') }}</a>

        <x-cart-icon />

        @if (auth(\App\Enums\AuthGuard::Admins->value)->check())
            <a href="{{ route('admins.dashboard') }}">{{ $label('Dashboard', 'لوحة التحكم') }}</a>
            <a href="{{ route('admins.items.index') }}">{{ $label('Manage Products', 'إدارة المنتجات') }}</a>
            <a href="{{ route('admins.categories.index') }}">{{ $label('Manage Categories', 'إدارة الأقسام') }}</a>
            <a href="{{ route('admins.discounts.index') }}">{{ $label('Discounts', 'الخصومات') }}</a>
            <a href="{{ route('admins.orders.index') }}">{{ $label('Orders', 'الطلبات') }}</a>
            <a href="{{ route('admins.reviews.index') }}">{{ $label('Reviews', 'التقييمات') }}</a>
            <a href="{{ route('admins.comments.index') }}">{{ $label('Comments', 'التعليقات') }}</a>

            @if (auth(\App\Enums\AuthGuard::Admins->value)->user()?->hasRole(\App\Enums\AdminRole::SuperAdmin->value))
                <a class="button" href="{{ route('admins.admins.create') }}">{{ $label('Add Admin', 'إضافة مدير') }}</a>
                <a href="{{ route('admins.admins.index') }}">{{ $label('Admins', 'المديرون') }}</a>
                <a href="{{ route('admins.permissions.index') }}">{{ $label('Permissions', 'الصلاحيات') }}</a>
            @endif

            <form method="POST" action="{{ route('admins.logout') }}">
                @csrf
                <button class="button secondary" type="submit">{{ $label('Log out', 'تسجيل الخروج') }}</button>
            </form>

        @elseif (auth()->check())

            <a href="{{ route('profile') }}">{{ $label('My Account', 'حسابي') }}</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="button secondary" type="submit">{{ $label('Log out', 'تسجيل الخروج') }}</button>
            </form>

        @else
            <a href="{{ route('login') }}">{{ $label('Log in', 'تسجيل الدخول') }}</a>
            <a class="button" href="{{ route('register') }}">{{ $label('Create Account', 'إنشاء حساب') }}</a>
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
