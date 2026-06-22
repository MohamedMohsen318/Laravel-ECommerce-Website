<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CartController as AdminCartController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PermissionController as AdminPermissionController;
use App\Http\Controllers\Admin\AdminController as AdminAdminController;
use App\Http\Controllers\Admin\ItemController as AdminItemController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\CartClearController;
use App\Http\Controllers\User\CartCouponController;
use App\Http\Controllers\User\CartController as UserCartController;
use App\Http\Controllers\User\CategoryController;
use App\Http\Controllers\User\ItemController as UserItemController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\CouponController;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;



// Home
Route::get('/', fn () => view('welcome'))
    ->name('home');


//lang

Route::get('/lang/{locale}', function ($locale) {

    if (in_array($locale, ['ar', 'en'])) {
        Session::put('locale', $locale);
    }

    return redirect()->back();
});


// AUTH (USER)
Route::middleware('guest')->group(function () {

    Route::get('/register', [AuthController::class, 'create'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'store']);

    Route::get('/login', [AuthController::class, 'edit'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'update']);
});

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'destroy'])
        ->name('logout');

    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
});



// Categories (user)
Route::get('/categories', [CategoryController::class, 'index'])
    ->name('categories.index');

Route::get('/categories/{path}', [CategoryController::class, 'show'])
    ->where('path', '.*')
    ->name('categories.show');


// Products (user)
Route::get('/products', [UserItemController::class, 'index'])
    ->name('products.index');

Route::get('/products/{item}', [UserItemController::class, 'show'])
    ->name('products.show');

Route::prefix('cart')
    ->name('cart.')
    ->group(function () {
        Route::get('/', [UserCartController::class, 'index'])
            ->name('index');
        Route::post('/items', [UserCartController::class, 'store'])
            ->name('add');
        Route::put('/items/{itemId}', [UserCartController::class, 'update'])
            ->name('update');
        Route::delete('/items/{itemId}', [UserCartController::class, 'destroy'])
            ->name('remove');
        Route::delete('/', [CartClearController::class, 'destroy'])
            ->name('clear');
        Route::post('/coupon', [CartCouponController::class, 'store'])
            ->name('coupon.apply');
        Route::delete('/coupon', [CartCouponController::class, 'destroy'])
            ->name('coupon.remove');
    });

Route::middleware('auth')->group(function () {
    Route::post('/orders', [UserOrderController::class, 'store'])
        ->name('orders.store');
    Route::post('coupon/apply',    [CouponController::class, 'apply'])->name('coupon.apply');
    Route::delete('coupon/remove', [CouponController::class, 'remove'])->name('coupon.remove');
});


// ADMIN PANEL

Route::prefix('admin')
    ->name('admins.')
    ->group(function () {

        // Guest Admin
        Route::middleware('guest:admins')->group(function () {
            Route::get('/login', [AdminAuthController::class, 'create'])
                ->name('login');
            Route::post('/login', [AdminAuthController::class, 'store'])
                ->name('login.store');

        });

        // Auth Admin
        Route::middleware('auth:admins')->group(function () {
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])
                ->name('dashboard');
            Route::post('/logout', [AdminAuthController::class, 'destroy'])
                ->name('logout');

            Route::resource('coupons', Admin\CouponController::class);

            Route::patch('coupons/{coupon}/toggle', [Admin\CouponController::class, 'toggle'])
                ->name('coupons.toggle');

            Route::get('coupons/{coupon}/stats', [Admin\CouponController::class, 'stats'])
                ->name('coupons.stats');
// ADMIN CATEGORIES
            Route::prefix('categories')
                ->name('categories.')
                ->group(function () {

                    Route::get('/', [AdminCategoryController::class, 'index'])
                        ->name('index');

                    Route::get('/create', [AdminCategoryController::class, 'create'])
                        ->name('create');

                    Route::post('/', [AdminCategoryController::class, 'store'])
                        ->name('store');

                    Route::get('/{category}/edit', [AdminCategoryController::class, 'edit'])
                        ->name('edit');

                    Route::put('/{category}', [AdminCategoryController::class, 'update'])
                        ->name('update');

                    Route::delete('/{category}', [AdminCategoryController::class, 'destroy'])
                        ->name('destroy');
                });

// ADMIN ITEMS
            Route::prefix('products')
                ->name('items.')
                ->group(function () {

                    Route::get('/', [AdminItemController::class, 'index'])
                        ->name('index');

                    Route::get('/create', [AdminItemController::class, 'create'])
                        ->name('create');

                    Route::post('/', [AdminItemController::class, 'store'])
                        ->name('store');

                    Route::get('/{item}/edit', [AdminItemController::class, 'edit'])
                        ->name('edit');

                    Route::put('/{item}', [AdminItemController::class, 'update'])
                        ->name('update');

                    Route::delete('/{item}', [AdminItemController::class, 'destroy'])
                        ->name('destroy');
                });

            // PERMISSIONS
            Route::get('/permissions', [AdminPermissionController::class, 'index'])
                ->name('permissions.index');
            Route::get('/permissions/{admin}/edit', [AdminPermissionController::class, 'edit'])
                ->name('permissions.edit');
            Route::put('/permissions/{admin}', [AdminPermissionController::class, 'update'])
                ->name('permissions.update');

            Route::prefix('carts')
                ->name('carts.')
                ->group(function () {
                    Route::get('/', [AdminCartController::class, 'index'])
                        ->name('index');
                    Route::get('/{cart}', [AdminCartController::class, 'show'])
                        ->name('show');
                    Route::put('/{cart}/status', [AdminCartController::class, 'update'])
                        ->name('update-status');
                    Route::delete('/{cart}', [AdminCartController::class, 'destroy'])
                        ->name('destroy');
                });

            Route::prefix('orders')
                ->name('orders.')
                ->group(function () {
                    Route::get('/', [AdminOrderController::class, 'index'])
                        ->name('index');
                    Route::get('/{order}', [AdminOrderController::class, 'show'])
                        ->name('show');
                });

            // ADMINS (SUPER ADMIN ONLY)
            Route::middleware('role:super-admin,admins')
                ->group(function () {
                    Route::resource('admins', AdminAdminController::class)
                        ->only(['index', 'create', 'store', 'destroy']);
                });
        });
    });
