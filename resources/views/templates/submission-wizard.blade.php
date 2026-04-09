@extends('layouts.rapida')

@section('title', 'Submit Report — RAPIDA')

@section('content')
<div class="min-h-screen flex flex-col bg-surface-page">
    {{-- Navigation Header with back button --}}
    <x-organisms.navigation-header currentRoute="report" />

    {{-- Wizard content --}}
    <main class="flex-1 flex items-start justify-center px-4 py-8">
        <x-organisms.submission-wizard :currentStep="1" />
    </main>
</div>
@endsection
