@extends('layouts.app')

@section('title', 'Commerce Hub')

@section('content')
    <style>
        .hero {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(280px, .9fr);
            gap: 18px;
            align-items: stretch;
        }
        .hero-copy {
            color: #fff;
            border-radius: 8px;
            padding: clamp(28px, 5vw, 54px);
            display: grid;
            align-content: center;
            gap: 18px;
            min-height: 420px;
            background:
                linear-gradient(135deg, rgba(30,58,138,.96), rgba(37,99,235,.88)),
                url("https://images.unsplash.com/photo-1555529669-e69e7aa0ba9a?auto=format&fit=crop&w=1400&q=80") center/cover;
            overflow: hidden;
        }
        .hero-copy h1 { max-width: 760px; }
        .hero-copy p {
            color: #dbeafe;
            font-size: 18px;
            line-height: 1.7;
            max-width: 680px;
            margin: 0;
        }
        .hero-actions { display: flex; gap: 12px; flex-wrap: wrap; }
        .hero-actions .button.secondary { background: #fff; color: #111827; }
        .hero-panel { display: grid; gap: 14px; align-content: center; }
        .mini-product {
            display: grid;
            grid-template-columns: 76px 1fr auto;
            gap: 12px;
            align-items: center;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            background: #fff;
        }
        .product-thumb {
            width: 76px;
            height: 76px;
            border-radius: 8px;
            background: linear-gradient(135deg, #dbeafe, #fef3c7);
            display: grid;
            place-items: center;
            font-weight: 900;
            color: #1f2937;
        }
        .mini-product:nth-child(3) .product-thumb { background: linear-gradient(135deg, #dcfce7, #e0e7ff); }
        .mini-product:nth-child(4) .product-thumb { background: linear-gradient(135deg, #fee2e2, #cffafe); }
        .mini-product h3, .feature h3 { margin: 0; font-size: 18px; }
        .mini-product p, .feature p { margin: 0; line-height: 1.6; }
        .price { font-weight: 900; color: #111827; }
        .section-title { margin: 0; font-size: 28px; }
        .store-strip { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
        .stat {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 18px;
        }
        .stat strong { display: block; font-size: 25px; margin-bottom: 4px; }
        .feature { min-height: 148px; display: grid; align-content: start; gap: 8px; }

        @media (max-width: 860px) {
            .hero, .store-strip { grid-template-columns: 1fr; }
            .hero-copy { min-height: 340px; }
            .mini-product { grid-template-columns: 68px 1fr; }
            .mini-product .price { grid-column: 2; }
        }
    </style>

    <section class="stack">
        <div class="hero">
            <div class="hero-copy">
                <h1>Curated shopping, clear management, one polished store.</h1>
                <p>
                    Browse products, explore categories, and place orders through a clean storefront connected to a focused admin dashboard.
                </p>
                <div class="hero-actions">
                    <a class="button secondary" href="{{ route('products.index') }}">Shop Now</a>
                    <a class="button" href="{{ route('categories.index') }}">Explore Categories</a>
                    @guest
                        <a class="button" href="{{ route('register') }}">Create Account</a>
                    @else
                        <a class="button" href="{{ route('profile') }}">My Account</a>
                    @endguest
                </div>
            </div>

            <aside class="card hero-panel">
                <h2 class="section-title">Today Picks</h2>
                <article class="mini-product">
                    <div class="product-thumb">Bag</div>
                    <div>
                        <h3>Daily Essentials</h3>
                        <p class="muted">Ready-to-order staples for quick checkout.</p>
                    </div>
                    <span class="price">$24</span>
                </article>
                <article class="mini-product">
                    <div class="product-thumb">Fit</div>
                    <div>
                        <h3>Practical Sets</h3>
                        <p class="muted">Useful selections for everyday needs.</p>
                    </div>
                    <span class="price">$39</span>
                </article>
                <article class="mini-product">
                    <div class="product-thumb">New</div>
                    <div>
                        <h3>Fresh Arrivals</h3>
                        <p class="muted">Categories and products kept up to date.</p>
                    </div>
                    <span class="price">$18</span>
                </article>
            </aside>
        </div>

        <div class="store-strip">
            <div class="stat"><strong>Fast</strong><span class="muted">Clear navigation</span></div>
            <div class="stat"><strong>Secure</strong><span class="muted">Protected accounts</span></div>
            <div class="stat"><strong>Organized</strong><span class="muted">Linked categories</span></div>
            <div class="stat"><strong>Managed</strong><span class="muted">Complete admin tools</span></div>
        </div>

        <section>
            <h2 class="section-title">A Complete Store Experience</h2>
            <div class="grid" style="margin-top: 16px;">
                <article class="card feature">
                    <h3>Category Browsing</h3>
                    <p class="muted">Move from parent categories to subcategories without losing context.</p>
                </article>
                <article class="card feature">
                    <h3>Customer Accounts</h3>
                    <p class="muted">Customers can sign in and manage their profile from the same polished interface.</p>
                </article>
                <article class="card feature">
                    <h3>Admin Workspace</h3>
                    <p class="muted">Manage products, categories, admins, permissions, and orders from one place.</p>
                </article>
            </div>
        </section>
    </section>
@endsection
