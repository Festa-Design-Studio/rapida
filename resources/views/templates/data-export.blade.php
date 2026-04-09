@extends('layouts.rapida')

@section('title', 'Export Data — RAPIDA')

@section('content')
@php
    $availableFormats = ['csv', 'geojson', 'pdf'];
@endphp

<div class="min-h-screen flex flex-col bg-surface-page">
    {{-- Navigation Header --}}
    <x-organisms.navigation-header currentRoute="dashboard" />

    {{-- Export content --}}
    <main class="flex-1 px-4 md:px-6 py-8 max-w-3xl mx-auto w-full">
        <header class="mb-8">
            <h1 class="text-h1 font-heading font-bold text-slate-900">Export Data</h1>
            <p class="text-body text-slate-600 mt-1">Download report data for analysis. All exports are anonymized to protect community members.</p>
        </header>

        <x-organisms.data-export :formats="$availableFormats" />
    </main>
</div>
@endsection
