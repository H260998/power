<?php

use App\Http\Controllers\Ajax\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/wishlist-products', [WishlistController::class, 'products'])->middleware('throttle:wishlist')->name('wishlist.products');
