@extends('layouts.rapida')

@section('title', 'Analyst Dashboard — RAPIDA')

@section('content')
    {{-- Navigation header --}}
    <header class="flex items-center justify-between bg-white border-b border-slate-200 px-4 py-3">
        <div class="flex items-center gap-3">
            <x-atoms.logo size="sm" variant="full" class="text-rapida-blue-700" />
            <span class="text-body-sm text-text-secondary">{{ __('rapida.dashboard_analyst') }}</span>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-body-sm text-slate-600">{{ auth('undp')->user()->name }}</span>
            <x-atoms.badge variant="{{ match(auth('undp')->user()->role->value) {
                'superadmin', 'operator' => 'verified',
                'analyst' => 'synced',
                'field_coordinator' => 'pending',
                default => 'default',
            } }}">
                {{ ucwords(str_replace('_', ' ', auth('undp')->user()->role->value)) }}
            </x-atoms.badge>
            <form method="POST" action="{{ route('undp.logout') }}">
                @csrf
                <x-atoms.button type="submit" variant="ghost" size="sm">
                    Logout
                </x-atoms.button>
            </form>
        </div>
    </header>

    <x-dashboard.dashboard-nav current="analyst" />

    {{-- Dashboard content --}}
    <main class="max-w-7xl mx-auto px-4 py-6 space-y-6">
        <livewire:dashboard.analytics-panel />
        <livewire:dashboard.verification-queue />
        <livewire:dashboard.export-panel />
    </main>
@endsection
