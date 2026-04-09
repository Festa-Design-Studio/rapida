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

    {{-- Font preconnect — performance-optimised --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- Inter — headings and UI (400 Regular, 500 Medium, 600 SemiBold, 700 Bold) --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Noto Sans — body and all content (400 Regular, 500 Medium, 700 Bold) --}}
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;700&display=swap" rel="stylesheet">

    {{-- Noto Sans Arabic — conditional, loaded only when locale is Arabic --}}
    @if(app()->getLocale() === 'ar')
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;500;700&display=swap" rel="stylesheet">
    @endif

    {{-- Vite assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface-page text-text-primary font-sans text-body antialiased">
    @yield('content')
</body>
</html>
