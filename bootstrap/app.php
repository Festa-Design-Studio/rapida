<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo('/login');

        // Locale preference cookie must be readable by the PWA service
        // worker and by client-side JS so offline caches can be keyed
        // by language. Excluding it from encryption keeps the raw value
        // ("en", "fr", "ar", ...) accessible via document.cookie.
        $middleware->encryptCookies(except: ['rapida_locale']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
