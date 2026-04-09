@extends('layouts.rapida')

@section('title', __('account.profile_title') . ' — RAPIDA')

@section('content')
<div class="min-h-screen bg-surface-page">
    <x-organisms.navigation-header currentRoute="profile" />

    <main class="max-w-2xl mx-auto px-4 py-8 space-y-8">
        <header>
            <h1 class="text-h1 font-heading font-bold text-slate-900">{{ __('account.profile_title') }}</h1>
            <div class="flex items-center gap-3 mt-3">
                <x-atoms.badge variant="verified">{{ $account->badge_count }} {{ __('account.your_badges') }}</x-atoms.badge>
                <x-atoms.badge variant="info">{{ $reports->count() }} {{ __('account.your_reports') }}</x-atoms.badge>
            </div>
        </header>

        {{-- Badges --}}
        <section>
            <h2 class="text-h3 font-heading font-semibold text-slate-900 mb-4">{{ __('account.your_badges') }}</h2>
            @if($badges->isEmpty())
                <p class="text-body-sm text-slate-500">{{ __('account.no_badges_yet') }}</p>
            @else
                <div class="flex flex-wrap gap-2">
                    @foreach($badges as $badge)
                        <x-atoms.badge variant="verified" size="lg">
                            {{ ucfirst(str_replace('_', ' ', $badge->badge_key)) }}
                        </x-atoms.badge>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Reports --}}
        <section>
            <h2 class="text-h3 font-heading font-semibold text-slate-900 mb-4">{{ __('account.your_reports') }}</h2>
            @if($reports->isEmpty())
                <p class="text-body-sm text-slate-500">{{ __('account.no_reports_yet') }}</p>
            @else
                <div class="space-y-3">
                    @foreach($reports as $r)
                        <a href="{{ route('report-detail', $r->id) }}" class="block">
                            <div class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:border-rapida-blue-300 transition-colors">
                                <x-atoms.badge variant="{{ $r->damage_level instanceof \App\Enums\DamageLevel ? $r->damage_level->value : $r->damage_level }}">
                                    {{ ucfirst($r->damage_level instanceof \App\Enums\DamageLevel ? $r->damage_level->value : $r->damage_level) }}
                                </x-atoms.badge>
                                <div class="flex-1 min-w-0">
                                    <p class="text-body-sm font-medium text-slate-900 truncate">{{ $r->infrastructure_name ?? ucfirst(str_replace('_', ' ', $r->infrastructure_type)) }}</p>
                                    <p class="text-caption text-slate-500">{{ $r->submitted_at?->format('M d, Y H:i') }}</p>
                                </div>
                                <x-atoms.badge variant="{{ $r->synced_at ? 'synced' : 'pending' }}">
                                    {{ $r->synced_at ? 'Synced' : 'Pending' }}
                                </x-atoms.badge>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Account actions --}}
        <section class="border-t border-slate-100 pt-6 space-y-4">
            <form method="POST" action="{{ route('account.logout') }}">
                @csrf
                <x-atoms.button type="submit" variant="secondary" class="w-full">
                    {{ __('account.btn_logout') }}
                </x-atoms.button>
            </form>

            <form method="POST" action="{{ route('account.destroy') }}"
                  onsubmit="return confirm('{{ __('account.delete_confirm') }}')">
                @csrf
                @method('DELETE')
                <x-atoms.button type="submit" variant="danger" size="sm" class="w-full">
                    {{ __('account.btn_delete_account') }}
                </x-atoms.button>
            </form>
        </section>
    </main>
</div>
@endsection
