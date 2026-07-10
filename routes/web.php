<?php

use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\CartClearController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CartDiscountController;
use App\Http\Controllers\User\CategoryController;
use App\Http\Controllers\User\ItemController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\ProductCommentController;
use App\Http\Controllers\User\ProductReviewController;
use App\Http\Controllers\User\ProfileController;
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

    Route::get('register', [AuthController::class, 'create'])
        ->name('register');

    Route::post('register', [AuthController::class, 'store']);

    Route::get('login', [AuthController::class, 'edit'])
        ->name('login');

    Route::post('login', [AuthController::class, 'update']);
});


// Auth

Route::middleware('auth')->group(function () {

    Route::post('logout', [AuthController::class, 'destroy'])
        ->name('logout');

    Route::get('profile', [ProfileController::class, 'show'])
        ->name('profile');

    Route::put('profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::post('orders', [OrderController::class, 'store'])
        ->name('orders.store');

    Route::get('orders', [OrderController::class, 'index'])
        ->name('orders.index');

    Route::get('orders/{order}', [OrderController::class, 'show'])
        ->name('orders.show');

    Route::post('products/{item}/reviews', [ProductReviewController::class, 'store'])
        ->name('products.reviews.store');

    Route::delete('products/{item}/reviews', [ProductReviewController::class, 'destroy'])
        ->name('products.reviews.destroy');

    Route::post('products/{item}/comments', [ProductCommentController::class, 'store'])
        ->name('products.comments.store');

    Route::put('comments/{comment}', [ProductCommentController::class, 'update'])
        ->name('comments.update');

    Route::delete('comments/{comment}', [ProductCommentController::class, 'destroy'])
        ->name('comments.destroy');
});


// Public Catalog

Route::get('categories', [CategoryController::class, 'index'])
    ->name('categories.index');

Route::get('categories/{path}', [CategoryController::class, 'show'])
    ->where('path', '.*')
    ->name('categories.show');

Route::get('products', [ItemController::class, 'index'])
    ->name('products.index');

Route::get('products/{item}', [ItemController::class, 'show'])
    ->name('products.show');

Route::get('products/{item}/variants', [ItemController::class, 'variants'])
    ->name('products.variants');


// Cart

Route::get('cart', [CartController::class, 'index'])
    ->name('cart.index');

Route::post('cart/items', [CartController::class, 'store'])
    ->name('cart.add');

Route::put('cart/items/{itemId}', [CartController::class, 'update'])
    ->name('cart.update');

Route::delete('cart/items/{itemId}', [CartController::class, 'destroy'])
    ->name('cart.remove');

Route::delete('cart', [CartClearController::class, 'destroy'])
    ->name('cart.clear');

Route::post('cart/discount', [CartDiscountController::class, 'store'])
    ->name('cart.discount.apply');

Route::delete('cart/discount', [CartDiscountController::class, 'destroy'])
    ->name('cart.discount.remove');
