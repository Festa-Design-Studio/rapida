<?php

namespace App\Http\Middleware;

use App\Models\Crisis;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the active UI locale for public reporter-facing routes.
 *
 * Resolution order (highest wins). Each signal is gated on the current
 * candidate list: when a Crisis is bound to the route, candidates are that
 * crisis's available_languages; otherwise they are the globally supported
 * locales from config. A signal is only honoured if it points to a locale
 * inside the candidate list.
 *
 *  1. Explicit user toggle for this session — session('locale')
 *  2. Authenticated Account preferred_language
 *  3. Persistent anonymous preference — rapida_locale cookie
 *  4. Browser preference — Accept-Language header (negotiated, de-regioned)
 *  5. Crisis default_language (fallback when no better signal exists)
 *  6. config('app.fallback_locale')
 *
 * The crisis default is deliberately a fallback, not a mandate: a crisis
 * operator offering [en, fr, ar] is providing a menu, not a prescription.
 * If the reporter's browser prefers a language inside that menu, the
 * browser wins.
 */
class SetLocaleFromCrisis
{
    public function handle(Request $request, Closure $next): Response
    {
        $crisis = $request->route('crisis') instanceof Crisis
            ? $request->route('crisis')
            : null;

        $supported = config('app.supported_locales', ['en']);
        $candidates = $crisis?->available_languages ?: $supported;

        $locale = $this->pick($request, $crisis, $candidates);

        if ($locale && in_array($locale, $supported, true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }

    /**
     * Walk the resolution chain and return the first signal that points
     * to a locale inside the candidate list. Returns null only if every
     * source is empty or non-matching — the caller then falls through
     * to the framework's configured fallback.
     */
    protected function pick(Request $request, ?Crisis $crisis, array $candidates): ?string
    {
        // (1) Session toggle — set by OnboardingController@setLanguage.
        $session = session('locale');
        if ($this->accepts($session, $candidates)) {
            return $session;
        }

        // (2) Authenticated Account preference.
        $account = $request->user('web');
        if ($account && $this->accepts($account->preferred_language, $candidates)) {
            return $account->preferred_language;
        }

        // (3) Persistent anonymous preference in the cookie jar.
        $cookie = $request->cookie('rapida_locale');
        if ($this->accepts($cookie, $candidates)) {
            return $cookie;
        }

        // (4) Browser hint — parse Accept-Language ourselves because
        //     Symfony's getPreferredLanguage($candidates) returns
        //     $candidates[0] when no Accept-Language header is present,
        //     which would silently override the crisis default. We only
        //     want to honour the header when it genuinely exists.
        if ($request->headers->has('Accept-Language')) {
            foreach ($request->getLanguages() as $tag) {
                $base = strtolower(substr((string) $tag, 0, 2));
                if ($this->accepts($base, $candidates)) {
                    return $base;
                }
            }
        }

        // (5) Crisis default — only when nothing above matched.
        if ($crisis && $this->accepts($crisis->default_language, $candidates)) {
            return $crisis->default_language;
        }

        // (6) Caller will apply config('app.fallback_locale').
        return null;
    }

    protected function accepts(?string $locale, array $candidates): bool
    {
        return is_string($locale) && $locale !== '' && in_array($locale, $candidates, true);
    }
}
