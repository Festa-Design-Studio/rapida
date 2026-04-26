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

        // Locale cookie: must be readable by the PWA service worker and
        // by client-side JS so offline caches can be keyed by language.
        //
        // Device fingerprint cookie: a random UUID that links anonymous
        // reports back to a device for /my-reports re-find (gap-32).
        // Encryption adds no security here (the UUID is already
        // unguessable) and would block the documented demo-persona
        // walk-through (docs/submission/demo-personas.md), which relies
        // on pasting raw persona strings into DevTools.
        $middleware->encryptCookies(except: ['rapida_locale', 'rapida_device_fingerprint']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
