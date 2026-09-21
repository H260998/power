<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(): View
    {
        return view('storefront.wishlist');
    }
}
