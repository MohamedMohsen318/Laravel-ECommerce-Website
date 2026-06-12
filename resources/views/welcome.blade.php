@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <style>
        .hero {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(280px, 0.8fr);
            gap: 24px;
            align-items: stretch;
        }

        .hero-copy {
            background: #111827;
            color: #fff;
            border-radius: 8px;
            padding: 42px;
            display: grid;
            align-content: center;
            gap: 18px;
            min-height: 360px;
        }

        .hero-copy h1 {
            font-size: clamp(34px, 6vw, 64px);
            line-height: 1;
            margin: 0;
            letter-spacing: 0;
        }

        .hero-copy p {
            color: #d1d5db;
            font-size: 18px;
            line-height: 1.7;
            max-width: 620px;
            margin: 0;
        }

        .hero-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .hero-actions .button.secondary {
            background: #fff;
            color: #111827;
        }

        .hero-panel {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 24px;
            display: grid;
            gap: 16px;
            align-content: center;
        }

        .product-stack {
            display: grid;
            gap: 12px;
        }

        .mini-product {
            display: grid;
            grid-template-columns: 72px 1fr auto;
            gap: 12px;
            align-items: center;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
        }

        .product-thumb {
            width: 72px;
            height: 72px;
            border-radius: 6px;
            background: linear-gradient(135deg, #dbeafe, #fef3c7);
            display: grid;
            place-items: center;
            font-weight: 700;
            color: #111827;
        }

        .mini-product:nth-child(2) .product-thumb {
            background: linear-gradient(135deg, #dcfce7, #e0e7ff);
        }

        .mini-product:nth-child(3) .product-thumb {
            background: linear-gradient(135deg, #fee2e2, #cffafe);
        }

        .section-title {
            margin: 0 0 16px;
            font-size: 28px;
        }

        .feature {
            min-height: 145px;
            display: grid;
            align-content: start;
            gap: 8px;
        }

        .feature h3,
        .mini-product h3 {
            margin: 0;
            font-size: 18px;
        }

        .feature p,
        .mini-product p {
            margin: 0;
            line-height: 1.6;
        }

        .price {
            font-weight: 700;
            color: #111827;
        }

        .store-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .stat {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 18px;
        }

        .stat strong {
            display: block;
            font-size: 26px;
            margin-bottom: 4px;
        }

        @media (max-width: 820px) {
            .hero,
            .store-strip {
                grid-template-columns: 1fr;
            }

            .hero-copy {
                padding: 28px;
                min-height: auto;
            }

            .mini-product {
                grid-template-columns: 64px 1fr;
            }

            .mini-product .price {
                grid-column: 2;
            }
        }
    </style>

    <section class="stack">
        <div class="hero">
            <div class="hero-copy">
                <h1>Fresh picks for every cart</h1>
                <p>
                    Browse organized categories, discover featured essentials, and manage your account from one clean store experience.
                </p>
                <div class="hero-actions">
                    <a class="button secondary" href="{{ route('categories.index') }}">Shop categories</a>
                    @guest
                        <a class="button" href="{{ route('register') }}">Create account</a>
                    @else
                        <a class="button" href="{{ route('profile') }}">View profile</a>
                    @endguest
                </div>
            </div>

            <aside class="hero-panel">
                <h2 class="section-title">Featured today</h2>
                <div class="product-stack">
                    <article class="mini-product">
                        <div class="product-thumb">Bag</div>
                        <div>
                            <h3>Daily essentials</h3>
                            <p class="muted">Reliable products for quick orders.</p>
                        </div>
                        <span class="price">$24</span>
                    </article>
                    <article class="mini-product">
                        <div class="product-thumb">Fit</div>
                        <div>
                            <h3>Active collection</h3>
                            <p class="muted">Comfortable picks for busy days.</p>
                        </div>
                        <span class="price">$39</span>
                    </article>
                    <article class="mini-product">
                        <div class="product-thumb">New</div>
                        <div>
                            <h3>New arrivals</h3>
                            <p class="muted">Fresh categories added weekly.</p>
                        </div>
                        <span class="price">$18</span>
                    </article>
                </div>
            </aside>
        </div>

        <div class="store-strip">
            <div class="stat">
                <strong>Fast</strong>
                <span class="muted">Simple navigation</span>
            </div>
            <div class="stat">
                <strong>Secure</strong>
                <span class="muted">Account access</span>
            </div>
            <div class="stat">
                <strong>Clear</strong>
                <span class="muted">Organized categories</span>
            </div>
            <div class="stat">
                <strong>Ready</strong>
                <span class="muted">Admin management</span>
            </div>
        </div>

        <section>
            <h2 class="section-title">Why shop here</h2>
            <div class="grid">
                <article class="card feature">
                    <h3>Clean category browsing</h3>
                    <p class="muted">Move from main categories into subcategories with a simple store flow.</p>
                </article>
                <article class="card feature">
                    <h3>Personal account</h3>
                    <p class="muted">Register, login, and update your profile using the same consistent design.</p>
                </article>
                <article class="card feature">
                    <h3>Admin ready</h3>
                    <p class="muted">Manage categories, translations, and media from the admin area.</p>
                </article>
            </div>
        </section>
    </section>
@endsection
