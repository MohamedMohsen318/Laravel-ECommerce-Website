<?php

use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PermissionController as AdminPermissionController;
use App\Http\Controllers\Admin\AdminController as AdminAdminController;
use App\Http\Controllers\Admin\ItemController as AdminItemController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CartController as AdminCartController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\CategoryController;
use App\Http\Controllers\User\ItemController as UserItemController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\User\CartController as UserCartController;
use Illuminate\Support\Facades\Route;

// Home

Route::get('/', fn () => view('welcome'))
    ->name('home');

// Language Switch


Route::get('/lang/{lang}', [LanguageController::class, 'switch'])
    ->name('lang.switch');

// USER AUTH

Route::middleware('guest')->group(function () {

    Route::get('/register', [AuthController::class, 'showRegistrationForm'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/profile', [AuthController::class, 'showProfile'])
        ->name('profile');

    Route::put('/profile', [AuthController::class, 'updateProfile'])
        ->name('profile.update');
});

// USER CATEGORIES & PRODUCTS


Route::get('/categories', [CategoryController::class, 'index'])
    ->name('categories.index');

Route::get('/categories/{path}', [CategoryController::class, 'show'])
    ->where('path', '.*')->name('categories.show');

Route::get('/products', [UserItemController::class, 'index'])
    ->name('products.index');

Route::get('/products/{item}', [UserItemController::class, 'show'])
    ->name('products.show');

// USER CART


Route::middleware('auth')->group(function () {

    Route::prefix('cart')
        ->name('cart.')
        ->group(function () {

            Route::get('/', [UserCartController::class, 'index'])
                ->name('index');

            Route::post('/add', [UserCartController::class, 'add'])
                ->name('add');

            Route::put('/{itemId}', [UserCartController::class, 'update'])
                ->name('update');

            Route::delete('/{itemId}', [UserCartController::class, 'remove'])
                ->name('remove');

            Route::delete('/clear', [UserCartController::class, 'clear'])
                ->name('clear');

            Route::post('/coupon', [UserCartController::class, 'applyCoupon'])
                ->name('coupon.apply');

            Route::delete('/coupon', [UserCartController::class, 'removeCoupon'])
                ->name('coupon.remove');
        });
});

// USER ORDERS


Route::middleware('auth')->group(function () {

    Route::post('/orders', [UserOrderController::class, 'store'])
        ->name('orders.store');

    Route::get('/orders', [UserOrderController::class, 'index'])
        ->name('orders.index');

    Route::get('/orders/{order}', [UserOrderController::class, 'show'])
        ->name('orders.show');
});

// ADMIN PANEL


Route::prefix('admin')
    ->name('admins.')
    ->group(function () {

        // Guest Admin

        Route::middleware('guest:admins')->group(function () {

            Route::get('/login', [AdminAuthController::class, 'showLoginForm'])
                ->name('login');

            Route::post('/login', [AdminAuthController::class, 'login'])
                ->name('login.store');
        });

        // Auth Admin

        Route::middleware('auth:admins')->group(function () {

            Route::get('/dashboard', [AdminDashboardController::class, 'index'])
                ->name('dashboard');

            Route::post('/logout', [AdminAuthController::class, 'logout'])
                ->name('logout');

            // Categories

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

            // Products

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

            // Orders

            Route::prefix('orders')
                ->name('orders.')
                ->group(function () {

                    Route::get('/', [AdminOrderController::class, 'index'])
                        ->name('index');

                    Route::get('/{order}', [AdminOrderController::class, 'show'])
                        ->name('show');
                });

           // Carts

            Route::prefix('carts')
                ->name('carts.')
                ->group(function () {

                    Route::get('/', [AdminCartController::class, 'index'])->name('index');
                    Route::get('/{cart}', [AdminCartController::class, 'show'])
                        ->name('show');

                    Route::patch('/{cart}/status', [AdminCartController::class, 'updateStatus'])
                        ->name('update-status');

                    Route::delete('/{cart}', [AdminCartController::class, 'destroy'])
                        ->name('destroy');
                });

            // Permissions
            Route::get('/permissions', [AdminPermissionController::class, 'index'])
                ->name('permissions.index');

            Route::get('/permissions/{admin}/edit', [AdminPermissionController::class, 'edit'])
                ->name('permissions.edit');

            Route::put('/permissions/{admin}', [AdminPermissionController::class, 'update'])
                ->name('permissions.update');

            // Creat Super Admin
            Route::middleware('role:super-admin,admins')->group(function () {

                Route::resource('admins', AdminAdminController::class)
                    ->only(['index', 'create', 'store', 'destroy']);
            });
        });
    });
