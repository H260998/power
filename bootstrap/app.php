<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(__DIR__.'/../routes/admin.php');

            Route::middleware('web')
                ->prefix('ajax')
                ->name('ajax.')
                ->group(__DIR__.'/../routes/ajax.php');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->web(append: [
            SetLocale::class,
        ]);
        $middleware->redirectGuestsTo(fn ($request) => $request->is('admin*')
            ? route('admin.login')
            : route('home'));
        $middleware->redirectUsersTo(fn (Request $request) => $request->routeIs('admin.*')
            ? route('admin.dashboard')
            : route('home'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function (Response $response, Throwable $exception, Request $request) {
            if ($response->getStatusCode() === 419
                && $request->routeIs('admin.login.attempt')
                && ! $request->expectsJson()) {
                return redirect()->route('admin.login')
                    ->withErrors(['session' => __('admin.session_expired')]);
            }

            return $response;
        });
    })->create();
