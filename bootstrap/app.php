<?php

use App\Http\Middleware\SecurityHeaders;
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
            // Setup is deliberately stateless so it also works before the
            // database-backed sessions table has been created.
            Route::middleware([])
                ->group(__DIR__.'/../routes/setup.php');

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
        $middleware->append(SecurityHeaders::class);
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
        $exceptions->report(function (Throwable $exception): void {
            $message = preg_replace('/\s+/', ' ', $exception->getMessage()) ?? $exception->getMessage();
            $message = preg_replace('#(postgres(?:ql)?://[^:]+:)[^@]+@#i', '$1[redacted]@', $message) ?? $message;

            error_log(sprintf(
                'Laravel exception: %s: %s',
                $exception::class,
                substr($message, 0, 1000),
            ));
        })->stop();

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
