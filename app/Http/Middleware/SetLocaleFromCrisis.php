<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromCrisis
{
    public function handle(Request $request, Closure $next): Response
    {
        // Priority: 1) Session locale, 2) Crisis default, 3) App default
        $locale = session('locale');

        if (! $locale) {
            $crisis = $request->route('crisis');
            if ($crisis && method_exists($crisis, 'getAttribute')) {
                $locale = $crisis->default_language;
            }
        }

        if ($locale && in_array($locale, ['en', 'fr', 'ar', 'es', 'ru', 'zh'])) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
