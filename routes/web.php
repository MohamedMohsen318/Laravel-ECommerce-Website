<?php

use App\Http\Controllers\Admin\{
    AdminController,
    AuthController as AdminAuthController,
    CartController as AdminCartController,
    CategoryController as AdminCategoryController,
    DiscountController as AdminDiscountController,
    DashboardController as AdminDashboardController,
    ItemController as AdminItemController,
    ItemOptionController as AdminItemOptionController,
    OrderController as AdminOrderController,
    PermissionController as AdminPermissionController,
    ProductCommentController as AdminCommentController,
    ProductReviewController as AdminReviewController
};

use App\Http\Controllers\User\{
    AuthController,
    CartClearController,
    CartController as UserCartController,
    CartDiscountController,
    CategoryController,
    ItemController as UserItemController,
    OrderController as UserOrderController,
    ProductCommentController,
    ProductReviewController,
    ProfileController
};

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

// Home

Route::view('/', 'welcome')->name('home');

Route::get('/lang/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['ar', 'en']), 404);

    Session::put('locale', $locale);

    return back();
})->name('lang.switch');


// Guest


Route::middleware('guest')->group(function () {

    Route::controller(AuthController::class)->group(function () {
        Route::get('register', 'create')->name('register');
        Route::post('register', 'store');

        Route::get('login', 'edit')->name('login');
        Route::post('login', 'update');
    });
});


// Auth User

Route::middleware('auth')->group(function () {

    Route::post('logout', [AuthController::class, 'destroy'])
        ->name('logout');

    Route::get('profile', [ProfileController::class, 'show'])
        ->name('profile');

    Route::put('profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::post('orders', [UserOrderController::class, 'store'])
        ->name('orders.store');

    Route::get('orders', [UserOrderController::class, 'index'])
        ->name('orders.index');

    Route::get('orders/{order}', [UserOrderController::class, 'show'])
        ->name('orders.show');

    Route::prefix('products/{item}')
        ->name('products.')
        ->group(function () {
            Route::post('reviews', [ProductReviewController::class, 'store'])
                ->name('reviews.store');
            Route::delete('reviews', [ProductReviewController::class, 'destroy'])
                ->name('reviews.destroy');

            Route::post('comments', [ProductCommentController::class, 'store'])
                ->name('comments.store');
        });

    Route::controller(ProductCommentController::class)
        ->prefix('comments/{comment}')
        ->name('comments.')
        ->group(function () {
            Route::put('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
        });
});


// Public Catalog

Route::resource('categories', CategoryController::class)
    ->only(['index']);

Route::get('categories/{path}', [CategoryController::class, 'show'])
    ->where('path', '.*')
    ->name('categories.show');

Route::resource('products', UserItemController::class)
    ->only(['index', 'show'])
    ->parameters(['products' => 'item']);

Route::get('products/{item}/variants', [UserItemController::class, 'variants'])
    ->name('products.variants');


// Cart

Route::prefix('cart')
    ->name('cart.')
    ->controller(UserCartController::class)
    ->group(function () {

        Route::get('/', 'index')->name('index');
        Route::post('/items', 'store')->name('add');
        Route::put('/items/{itemId}', 'update')->name('update');
        Route::delete('/items/{itemId}', 'destroy')->name('remove');
    });

Route::delete('cart', [CartClearController::class, 'destroy'])
    ->name('cart.clear');

Route::controller(CartDiscountController::class)
    ->prefix('cart/discount')
    ->name('cart.discount.')
    ->group(function () {

        Route::post('/', 'store')->name('apply');
        Route::delete('/', 'destroy')->name('remove');
    });


// Admin Panel


Route::prefix('admin')
    ->name('admins.')
    ->group(function () {

        //  Guest Admin


        Route::middleware('guest:admins')
            ->controller(AdminAuthController::class)
            ->group(function () {

                Route::get('login', 'create')->name('login');
                Route::post('login', 'store')->name('login.store');
            });

        // Auth Admin


        Route::middleware('auth:admins')->group(function () {

            Route::get('dashboard', [AdminDashboardController::class, 'index'])
                ->name('dashboard');

            Route::post('logout', [AdminAuthController::class, 'destroy'])
                ->name('logout');

            // Resources


            Route::resource('categories', AdminCategoryController::class)
                ->except(['show']);

            Route::resource('products', AdminItemController::class)
                ->except(['show'])
                ->names('items')
                ->parameters(['products' => 'item']);

            Route::resource('item-options', AdminItemOptionController::class)
                ->except(['show']);

            Route::resource('admins', AdminController::class)
                ->only(['index', 'create', 'store', 'destroy'])
                ->middleware('role:super-admin,admins');

            // Discounts Extra Routes

            Route::resource('discounts', AdminDiscountController::class);

            Route::put(
                'discounts/{discount}/toggle',
                [AdminDiscountController::class, 'toggle']
            )->name('discounts.toggle');

            Route::get(
                'discounts/{discount}/stats',
                [AdminDiscountController::class, 'stats']
            )->name('discounts.stats');

            // Permissions


            Route::resource('permissions', AdminPermissionController::class)
                ->only(['index', 'edit', 'update']);

            // Carts


            Route::prefix('carts')
                ->name('carts.')
                ->controller(AdminCartController::class)
                ->group(function () {

                    Route::get('/', 'index')->name('index');
                    Route::get('{cart}', 'show')->name('show');
                    Route::put('{cart}/status', 'update')
                        ->name('update-status');
                    Route::delete('{cart}', 'destroy')
                        ->name('destroy');
                });

            // Orders

            Route::resource('orders', AdminOrderController::class)
                ->only(['index', 'show']);

            // Product Feedback


            Route::prefix('reviews')
                ->name('reviews.')
                ->controller(AdminReviewController::class)
                ->group(function () {

                    Route::get('/', 'index')->name('index')
                        ->middleware('permission:view-product-feedback');
                    Route::delete('{review}', 'destroy')->name('destroy')
                        ->middleware('permission:delete-product-feedback');
                });
            Route::prefix('comments')
                ->name('comments.')
                ->controller(AdminCommentController::class)
                ->group(function () {

                    Route::get('/', 'index')->name('index')
                        ->middleware('permission:view-product-feedback');
                    Route::delete('{comment}', 'destroy')->name('destroy')
                        ->middleware('permission:delete-product-feedback');
                });
        });
    });
