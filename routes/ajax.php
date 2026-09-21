<?php

use App\Http\Controllers\Ajax\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/wishlist-products', [WishlistController::class, 'products'])->name('wishlist.products');
