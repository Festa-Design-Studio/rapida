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
    </style>

    {{-- Vite assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface-page text-text-primary font-sans text-body antialiased">
    @yield('content')
</body>
</html>
