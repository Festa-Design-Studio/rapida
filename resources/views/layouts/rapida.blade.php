<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'RAPIDA'))</title>

    {{-- PWA manifest and theme --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1a3a4a">

    {{-- Fonts self-hosted in public/fonts/ — no CDN dependency for offline PWA (Gap C5) --}}
    <link rel="preload" href="/fonts/inter/Inter-SemiBold.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/noto-sans/NotoSans-Regular.woff2" as="font" type="font/woff2" crossorigin>

    {{-- Vite assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface-page text-text-primary font-sans text-body antialiased">
    @yield('content')
</body>
</html>
