<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CartController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\ItemAttributeController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductCommentController;
use App\Http\Controllers\Admin\ProductReviewController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin')->group(function () {

    // Guest

    Route::middleware('guest:admins')->group(function () {

        Route::get('login', [AuthController::class, 'create'])
            ->name('admins.login');

        Route::post('login', [AuthController::class, 'store'])
            ->name('admins.login.store');
    });

    // Auth

    Route::middleware('auth:admins')->group(function () {

        Route::get('dashboard', [DashboardController::class, 'index'])
            ->name('admins.dashboard');

        Route::post('logout', [AuthController::class, 'destroy'])
            ->name('admins.logout');

        // Categories

        Route::get('categories', [CategoryController::class, 'index'])
            ->name('admins.categories.index');

        Route::get('categories/create', [CategoryController::class, 'create'])
            ->name('admins.categories.create');

        Route::post('categories', [CategoryController::class, 'store'])
            ->name('admins.categories.store');

        Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])
            ->name('admins.categories.edit');

        Route::put('categories/{category}', [CategoryController::class, 'update'])
            ->name('admins.categories.update');

        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])
            ->name('admins.categories.destroy');

        // Items

        Route::get('products', [ItemController::class, 'index'])
            ->name('admins.items.index');
        Route::get('products/create', [ItemController::class, 'create'])
            ->name('admins.items.create');
        Route::post('products', [ItemController::class, 'store'])
            ->name('admins.items.store');
        Route::get('products/{item}/edit', [ItemController::class, 'edit'])
            ->name('admins.items.edit');
        Route::put('products/{item}', [ItemController::class, 'update'])
            ->name('admins.items.update');
        Route::delete('products/{item}', [ItemController::class, 'destroy'])
            ->name('admins.items.destroy');

        // Item Attributes

        Route::get('item-attributes', [ItemAttributeController::class, 'index'])
            ->name('admins.item-attributes.index');
        Route::get('item-attributes/create', [ItemAttributeController::class, 'create'])
            ->name('admins.item-attributes.create');
        Route::post('item-attributes', [ItemAttributeController::class, 'store'])
            ->name('admins.item-attributes.store');
        Route::get('item-attributes/{item_attribute}/edit', [ItemAttributeController::class, 'edit'])
            ->name('admins.item-attributes.edit');
        Route::put('item-attributes/{item_attribute}', [ItemAttributeController::class, 'update'])
            ->name('admins.item-attributes.update');
        Route::delete('item-attributes/{item_attribute}', [ItemAttributeController::class, 'destroy'])
            ->name('admins.item-attributes.destroy');

        // Admins

        Route::middleware('role:super-admin,admins')->group(function () {

            Route::get('admins', [AdminController::class, 'index'])
                ->name('admins.admins.index');

            Route::get('admins/create', [AdminController::class, 'create'])
                ->name('admins.admins.create');

            Route::post('admins', [AdminController::class, 'store'])
                ->name('admins.admins.store');

            Route::delete('admins/{admin}', [AdminController::class, 'destroy'])
                ->name('admins.admins.destroy');
        });

        // Discounts

        Route::get('discounts', [DiscountController::class, 'index'])
            ->name('admins.discounts.index');

        Route::get('discounts/create', [DiscountController::class, 'create'])
            ->name('admins.discounts.create');

        Route::post('discounts', [DiscountController::class, 'store'])
            ->name('admins.discounts.store');

        Route::get('discounts/{discount}/edit', [DiscountController::class, 'edit'])
            ->name('admins.discounts.edit');

        Route::put('discounts/{discount}', [DiscountController::class, 'update'])
            ->name('admins.discounts.update');

        Route::delete('discounts/{discount}', [DiscountController::class, 'destroy'])
            ->name('admins.discounts.destroy');

        Route::put('discounts/{discount}/toggle', [DiscountController::class, 'toggle'])
            ->name('admins.discounts.toggle');

        Route::get('discounts/{discount}/stats', [DiscountController::class, 'stats'])
            ->name('admins.discounts.stats');

        // Permissions

        Route::get('permissions', [PermissionController::class, 'index'])
            ->name('admins.permissions.index');

        Route::get('permissions/{permission}/edit', [PermissionController::class, 'edit'])
            ->name('admins.permissions.edit');

        Route::put('permissions/{permission}', [PermissionController::class, 'update'])
            ->name('admins.permissions.update');

        // Carts

        Route::get('carts', [CartController::class, 'index'])
            ->name('admins.carts.index');

        Route::get('carts/{cart}', [CartController::class, 'show'])
            ->name('admins.carts.show');

        Route::put('carts/{cart}/status', [CartController::class, 'update'])
            ->name('admins.carts.update-status');

        Route::delete('carts/{cart}', [CartController::class, 'destroy'])
            ->name('admins.carts.destroy');

        // Orders

        Route::get('orders', [OrderController::class, 'index'])
            ->name('admins.orders.index');

        Route::get('orders/{order}', [OrderController::class, 'show'])
            ->name('admins.orders.show');

        // Product Feedback: Reviews

        Route::get('reviews', [ProductReviewController::class, 'index'])
            ->name('admins.reviews.index')
            ->middleware('permission:view-product-feedback');

        Route::delete('reviews/{review}', [ProductReviewController::class, 'destroy'])
            ->name('admins.reviews.destroy')
            ->middleware('permission:delete-product-feedback');

        // Product Feedback: Comments

        Route::get('comments', [ProductCommentController::class, 'index'])
            ->name('admins.comments.index')
            ->middleware('permission:view-product-feedback');

        Route::delete('comments/{comment}', [ProductCommentController::class, 'destroy'])
            ->name('admins.comments.destroy')
            ->middleware('permission:delete-product-feedback');
    });
});
