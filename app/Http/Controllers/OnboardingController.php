<?php

namespace App\Http\Controllers;

use App\Models\Crisis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    /**
     * Persistent cookie that mirrors session('locale') for anonymous
     * reporters. One year, SameSite=Lax, NOT HttpOnly because the PWA
     * service worker reads it to key offline caches by locale.
     */
    protected const LOCALE_COOKIE = 'rapida_locale';

    protected const LOCALE_COOKIE_MINUTES = 60 * 24 * 365;

    public function index(): View
    {
        $crisis = Crisis::where('status', 'active')->first();
        $availableLanguages = $this->resolveLanguageMenu($crisis);

        return view('templates.onboarding', compact('crisis', 'availableLanguages'));
    }

    public function setLanguage(Request $request): RedirectResponse
    {
        $supported = config('app.supported_locales', ['en']);
        $requested = $request->input('language');
        $lang = in_array($requested, $supported, true)
            ? $requested
            : config('app.fallback_locale', 'en');

        session(['locale' => $lang]);
        app()->setLocale($lang);

        // Mirror to the Account record when the reporter is authenticated.
        // Anonymous reporters get persistence via the cookie below.
        $account = $request->user('web');
        if ($account && $account->preferred_language !== $lang) {
            $account->forceFill(['preferred_language' => $lang])->save();
        }

        return redirect()->back()->withCookie(
            Cookie::make(
                name: self::LOCALE_COOKIE,
                value: $lang,
                minutes: self::LOCALE_COOKIE_MINUTES,
                path: '/',
                domain: null,
                secure: $request->isSecure(),
                httpOnly: false,
                raw: false,
                sameSite: 'lax',
            )
        );
    }

    /**
     * Build the [code => endonym] map for the language switcher, scoped
     * to the active crisis's available languages when one exists.
     *
     * @return array<string, string>
     */
    protected function resolveLanguageMenu(?Crisis $crisis): array
    {
        $codes = $crisis?->available_languages ?: config('app.supported_locales', ['en']);
        $names = config('app.language_names', []);

        return collect($codes)
            ->mapWithKeys(fn (string $code) => [$code => $names[$code] ?? $code])
            ->all();
    }
}
