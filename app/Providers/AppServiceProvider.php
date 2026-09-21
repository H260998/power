<?php

namespace App\Providers;

use App\Services\CartService;
use App\Services\SettingsService;
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
        View::composer('layouts.storefront', function ($view) {
            $view->with('cartCount', app(CartService::class)->count());
            $view->with('metaPixelId', app(SettingsService::class)->get('meta_pixel_id'));
        });
    }
}
