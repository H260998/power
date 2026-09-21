<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MarketingController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductColorController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\ProductSizeController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\PromoCodeController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StatisticsController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('categories', CategoryController::class)->except('show');

        Route::resource('products', ProductController::class)->except('show');
        Route::prefix('products/{product}')->name('products.')->group(function () {
            Route::post('sizes', [ProductSizeController::class, 'store'])->name('sizes.store');
            Route::delete('sizes/{size}', [ProductSizeController::class, 'destroy'])->name('sizes.destroy');

            Route::post('colors', [ProductColorController::class, 'store'])->name('colors.store');
            Route::delete('colors/{color}', [ProductColorController::class, 'destroy'])->name('colors.destroy');

            Route::patch('images/{image}', [ProductImageController::class, 'update'])->name('images.update');
            Route::delete('images/{image}', [ProductImageController::class, 'destroy'])->name('images.destroy');

            Route::patch('variants', [ProductVariantController::class, 'update'])->name('variants.update');
        });

        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}/delivery-note', [OrderController::class, 'deliveryNote'])->name('orders.delivery-note');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

        Route::resource('promo-codes', PromoCodeController::class)->except('show');
        Route::patch('promo-codes/{promo_code}/toggle', [PromoCodeController::class, 'toggle'])->name('promo-codes.toggle');

        Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

        Route::get('marketing', [MarketingController::class, 'edit'])->name('marketing');
        Route::put('marketing', [MarketingController::class, 'update'])->name('marketing.update');

        Route::get('statistics', [StatisticsController::class, 'index'])->name('statistics');

    });
});
