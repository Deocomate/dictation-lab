<?php

use App\Http\Middleware\EnsureDailyDictationLimit;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'dictation.limit' => EnsureDailyDictationLimit::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'checkout/sepay/ipn',
        ]);

        // Redirect unauthenticated users on the admin guard to the admin login page
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('admin/*') || $request->is('admin')) {
                return route('admin.auth.login');
            }

            return route('admin.auth.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
