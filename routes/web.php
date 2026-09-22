<?php

use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\InformationController;
use App\Http\Controllers\Storefront\LocaleController;
use App\Http\Controllers\Storefront\OrderConfirmationController;
use App\Http\Controllers\Storefront\OrderTrackingController;
use App\Http\Controllers\Storefront\ProductController;
use App\Http\Controllers\Storefront\ShopController;
use App\Http\Controllers\Storefront\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/lang/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/boutique', [ShopController::class, 'index'])->name('shop.index');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('shop.product');

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/buy-now', [CartController::class, 'buyNow'])->name('buyNow');
    Route::patch('/{variantId}', [CartController::class, 'update'])->whereNumber('variantId')->name('update');
    Route::delete('/{variantId}', [CartController::class, 'remove'])->whereNumber('variantId')->name('remove');
    Route::post('/promo', [CartController::class, 'applyPromo'])->name('promo.apply');
    Route::delete('/promo', [CartController::class, 'removePromo'])->name('promo.remove');
});

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

Route::get('/order/confirmation/{orderNumber}', [OrderConfirmationController::class, 'show'])->name('order.confirmation');

Route::get('/order/track', [OrderTrackingController::class, 'index'])->name('order.track');
Route::post('/order/track', [OrderTrackingController::class, 'lookup'])->name('order.track.lookup');

Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');

Route::get('/aide', [InformationController::class, 'show'])->defaults('page', 'help')->name('pages.help');
Route::get('/a-propos', [InformationController::class, 'show'])->defaults('page', 'about')->name('pages.about');
Route::get('/contact', [InformationController::class, 'show'])->defaults('page', 'contact')->name('pages.contact');
Route::get('/confidentialite', [InformationController::class, 'show'])->defaults('page', 'privacy')->name('pages.privacy');
Route::get('/conditions-generales', [InformationController::class, 'show'])->defaults('page', 'terms')->name('pages.terms');
