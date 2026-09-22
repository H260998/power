<?php

namespace App\Providers;

use App\Models\PromoCode;
use App\Services\CartService;
use App\Services\SettingsService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('checkout', fn (Request $request) => [
            Limit::perMinute(5)->by($request->ip()),
        ]);
        RateLimiter::for('order-tracking', fn (Request $request) => [
            Limit::perMinute(10)->by($request->ip()),
        ]);
        RateLimiter::for('cart-promo', fn (Request $request) => [
            Limit::perMinute(10)->by($request->ip()),
        ]);
        RateLimiter::for('wishlist', fn (Request $request) => [
            Limit::perMinute(60)->by($request->ip()),
        ]);

        View::composer('layouts.storefront', function ($view) {
            $view->with('cartCount', app(CartService::class)->count());
            $view->with('metaPixelId', app(SettingsService::class)->get('meta_pixel_id'));
            $view->with('activePromo', PromoCode::currentlyValid()->latest()->first());
        });
    }
}
