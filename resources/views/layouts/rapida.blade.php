<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'RAPIDA'))</title>

    {{-- Favicon + PWA --}}
    <link rel="icon" type="image/svg+xml" href="/icons/favicon.svg">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1a3a4a">

    {{-- Self-hosted fonts — declared here (not in CSS) to avoid Vite dev server URL rewriting --}}
    <link rel="preload" href="/fonts/inter/Inter-SemiBold.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/noto-sans/NotoSans-Regular.woff2" as="font" type="font/woff2" crossorigin>
    <style>
        @font-face { font-family: 'Inter'; src: url('/fonts/inter/Inter-Regular.woff2') format('woff2'); font-weight: 400; font-display: swap; }
        @font-face { font-family: 'Inter'; src: url('/fonts/inter/Inter-Medium.woff2') format('woff2'); font-weight: 500; font-display: swap; }
        @font-face { font-family: 'Inter'; src: url('/fonts/inter/Inter-SemiBold.woff2') format('woff2'); font-weight: 600; font-display: swap; }
        @font-face { font-family: 'Inter'; src: url('/fonts/inter/Inter-Bold.woff2') format('woff2'); font-weight: 700; font-display: swap; }
        @font-face { font-family: 'Noto Sans'; src: url('/fonts/noto-sans/NotoSans-Regular.woff2') format('woff2'); font-weight: 400; font-display: swap; }
        @font-face { font-family: 'Noto Sans'; src: url('/fonts/noto-sans/NotoSans-Medium.woff2') format('woff2'); font-weight: 500; font-display: swap; }
        @font-face { font-family: 'Noto Sans'; src: url('/fonts/noto-sans/NotoSans-Bold.woff2') format('woff2'); font-weight: 700; font-display: swap; }
        @font-face { font-family: 'Noto Sans Arabic'; src: url('/fonts/noto-sans-arabic/NotoSansArabic-Regular.woff2') format('woff2'); font-weight: 400; font-display: swap; }
        @font-face { font-family: 'Noto Sans Arabic'; src: url('/fonts/noto-sans-arabic/NotoSansArabic-Bold.woff2') format('woff2'); font-weight: 700; font-display: swap; }
        {{-- Gap-45: explicit Cyrillic subset for Russian. Trusted as bundled in
             Noto Sans WOFF2 too, but the explicit subset guards against a future
             rebuild that trims the file to Latin-only. --}}
        @font-face { font-family: 'Noto Sans Cyrillic'; src: url('/fonts/noto-sans-cyrillic/NotoSans-Cyrillic-Regular.woff2') format('woff2'); font-weight: 400; font-display: swap; unicode-range: U+0400-04FF, U+0500-052F, U+2DE0-2DFF, U+A640-A69F; }
        @font-face { font-family: 'Noto Sans Cyrillic'; src: url('/fonts/noto-sans-cyrillic/NotoSans-Cyrillic-Bold.woff2') format('woff2'); font-weight: 700; font-display: swap; unicode-range: U+0400-04FF, U+0500-052F, U+2DE0-2DFF, U+A640-A69F; }
        {{-- Gap-45: Noto Sans CJK SC (Simplified Chinese). Full glyph coverage,
             ~7MB but loaded only when :lang(zh) selector matches via the body
             font-family cascade — Latin/Arabic/Cyrillic users do not pay the cost. --}}
        @font-face { font-family: 'Noto Sans SC'; src: url('/fonts/noto-sans-cjk-sc/NotoSansSC-Regular.woff2') format('woff2'); font-weight: 400; font-display: swap; unicode-range: U+4E00-9FFF, U+3000-303F, U+FF00-FFEF; }
        @font-face { font-family: 'Noto Sans SC'; src: url('/fonts/noto-sans-cjk-sc/NotoSansSC-Bold.woff2') format('woff2'); font-weight: 700; font-display: swap; unicode-range: U+4E00-9FFF, U+3000-303F, U+FF00-FFEF; }
    </style>

    {{-- Vite assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface-page text-text-primary font-sans text-body antialiased">
    @yield('content')

    {{--
        Floating language switcher for the crisis report wizard.

        The wizard is deliberately chromeless — no <x-organisms.navigation-header>
        — so it has no inherited language switcher, which was forcing English
        speakers on French-default crises to rely on browser translation.
        Every other reporter surface (map-home, my-reports, confirmation,
        submission-wizard, analytics-dashboard, report-detail) already
        renders navigation-header, which contains its own switcher; onboarding
        renders an inline badge-variant switcher. Adding the pill globally
        duplicates those. So this is an allowlist of exactly one route.

        PRD "always visible" is satisfied across the app as a union:
        navigation-header on chromed pages, badge variant on onboarding,
        and this pill on the chromeless wizard.
    --}}
    @if(request()->routeIs('crisis.show'))
        @isset($__rapidaLanguageMenu)
            <div
                class="fixed top-inner end-inner z-40"
                style="padding-top: env(safe-area-inset-top); padding-inline-end: env(safe-area-inset-right);"
                data-testid="global-language-switcher"
            >
                <x-molecules.language-switcher
                    :current="app()->getLocale()"
                    :languages="$__rapidaLanguageMenu"
                    variant="dropdown"
                />
            </div>
        @endisset
    @endif
</body>
</html>
