@extends('layouts.rapida')

@section('title', 'Dashboard — RAPIDA')

@section('content')
@php
    $stats = [
        'totalReports' => 247,
        'byDamageLevel' => [
            'minimal' => 98,
            'partial' => 112,
            'complete' => 37,
        ],
        'byCrisisType' => [
            'natural' => 156,
            'technological' => 52,
            'human-made' => 39,
        ],
        'syncedCount' => 231,
        'pendingCount' => 16,
        'recentReports' => [
            [
                'photo' => null,
                'damageLevel' => 'partial',
                'infrastructureType' => 'road',
                'location' => '14 Elm Street, District 3',
                'description' => 'Road cracked after flooding.',
                'reporterName' => 'Ahmed K.',
                'submittedAt' => '2026-03-27 08:15',
                'syncStatus' => 'synced',
            ],
            [
                'photo' => null,
                'damageLevel' => 'complete',
                'infrastructureType' => 'bridge',
                'location' => 'Al-Nahr Bridge, Route 5',
                'description' => 'Bridge collapsed.',
                'reporterName' => 'Fatima R.',
                'submittedAt' => '2026-03-27 07:40',
                'syncStatus' => 'synced',
            ],
            [
                'photo' => null,
                'damageLevel' => 'minimal',
                'infrastructureType' => 'power',
                'location' => 'Block 7, Industrial Zone',
                'description' => 'Power lines sagging.',
                'reporterName' => 'Carlos M.',
                'submittedAt' => '2026-03-27 06:20',
                'syncStatus' => 'pending',
            ],
        ],
    ];

    $mapReports = [
        ['lat' => 33.89, 'lng' => 35.50, 'damageLevel' => 'partial'],
        ['lat' => 33.88, 'lng' => 35.51, 'damageLevel' => 'complete'],
        ['lat' => 33.87, 'lng' => 35.49, 'damageLevel' => 'minimal'],
        ['lat' => 33.895, 'lng' => 35.505, 'damageLevel' => 'partial'],
    ];
@endphp

<div class="min-h-screen flex flex-col bg-surface-page">
    {{-- Navigation Header (coordinator variant) --}}
    <x-organisms.navigation-header currentRoute="dashboard" />

    {{-- Dashboard content --}}
    <main class="flex-1 px-4 md:px-6 py-8">
        <div class="max-w-7xl mx-auto">
            <div class="lg:flex lg:gap-8">
                {{-- Main analytics --}}
                <div class="flex-1 min-w-0">
                    <x-organisms.analytics-dashboard :stats="$stats" />
                </div>

                {{-- Side map panel (desktop) --}}
                <aside class="hidden lg:block w-96 flex-shrink-0 mt-0">
                    <div class="sticky top-20">
                        <h3 class="text-h4 font-heading font-semibold text-slate-900 mb-3">Live Map</h3>
                        <x-organisms.map-organism height="h-[400px]" :reports="$mapReports" />
                    </div>
                </aside>
            </div>

            {{-- Mobile map --}}
            <div class="lg:hidden mt-8">
                <h3 class="text-h4 font-heading font-semibold text-slate-900 mb-3">Live Map</h3>
                <x-organisms.map-organism height="h-64" :reports="$mapReports" />
            </div>
        </div>
    </main>
</div>
@endsection
