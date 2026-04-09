@extends('layouts.rapida')

@section('title', 'Welcome — RAPIDA')

@section('content')
<div x-data="{ showHowItWorks: false }" class="min-h-screen bg-gradient-to-b from-rapida-blue-50 to-slate-50 flex flex-col items-center justify-center px-4 py-12">
    <div class="w-full max-w-md text-center space-y-8">
        {{-- Logo --}}
        <div class="flex flex-col items-center gap-3">
            <x-atoms.icon name="check-circle" size="xl" class="text-rapida-blue-900" />
            <h1 class="text-display font-heading font-bold text-rapida-blue-900 tracking-tight">RAPIDA</h1>
        </div>

        {{-- Welcome message --}}
        <div class="space-y-3">
            <h2 class="text-h1 font-heading font-semibold text-slate-900">{{ __('onboarding.headline') }}</h2>
            <p class="text-body text-slate-600">{{ __('onboarding.subtext') }}</p>
        </div>

        {{-- Language Switcher --}}
        <div class="flex justify-center">
            <x-molecules.language-switcher
                current="{{ session('locale', 'en') }}"
                :languages="$availableLanguages"
            />
        </div>

        {{-- Trust block --}}
        <div class="flex justify-around text-center pt-2">
            <div class="flex flex-col items-center gap-1">
                <x-atoms.icon name="shield-exit" size="md" class="text-rapida-blue-700" />
                <span class="text-caption text-slate-500">{{ __('onboarding.trust_private') }}</span>
            </div>
            <div class="flex flex-col items-center gap-1">
                <x-atoms.icon name="cloud-offline" size="md" class="text-rapida-blue-700" />
                <span class="text-caption text-slate-500">{{ __('onboarding.trust_offline') }}</span>
            </div>
            <div class="flex flex-col items-center gap-1">
                <x-atoms.icon name="language" size="md" class="text-rapida-blue-700" />
                <span class="text-caption text-slate-500">{{ __('onboarding.trust_languages') }}</span>
            </div>
        </div>

        {{-- Actions --}}
        <form action="{{ route('onboarding.language') }}" method="POST" class="space-y-3 pt-4">
            @csrf
            <input type="hidden" name="language" id="selected-language" value="{{ session('locale', 'en') }}" />
            <x-atoms.button variant="primary" size="lg" class="w-full" type="submit">
                {{ __('onboarding.continue') }}
            </x-atoms.button>
            <x-atoms.button
                variant="ghost"
                size="md"
                class="w-full"
                type="button"
                @click.prevent="showHowItWorks = true"
            >
                {{ __('onboarding.how_it_works') }}
            </x-atoms.button>
        </form>
    </div>

    {{-- Slide-up "How it works" panel --}}
    <div
        x-show="showHowItWorks"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-8"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-8"
        x-cloak
        class="fixed inset-0 z-50 flex items-end sm:items-center justify-center"
        @click.self="showHowItWorks = false"
    >
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/20" @click="showHowItWorks = false"></div>

        {{-- Panel --}}
        <div class="relative w-full max-w-md bg-white rounded-t-2xl sm:rounded-2xl shadow-lg p-6 pb-8 mx-4 mb-0 sm:mb-auto space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-h2 font-heading font-semibold text-slate-900">{{ __('onboarding.how_title') }}</h3>
                <button @click="showHowItWorks = false" class="h-10 w-10 flex items-center justify-center rounded-full hover:bg-slate-100 transition-colors" aria-label="Close">
                    <x-atoms.icon name="close" size="md" class="text-slate-500" />
                </button>
            </div>

            <div class="space-y-5">
                @foreach([1, 2, 3, 4] as $step)
                    <div class="flex items-start gap-4">
                        <div class="h-10 w-10 shrink-0 rounded-full bg-rapida-blue-50 flex items-center justify-center">
                            <x-atoms.icon name="{{ __("onboarding.step_{$step}_icon") }}" size="md" class="text-rapida-blue-700" />
                        </div>
                        <div>
                            <p class="text-body font-medium text-slate-900">{{ __("onboarding.step_{$step}_title") }}</p>
                            <p class="text-body-sm text-slate-500 mt-0.5">{{ __("onboarding.step_{$step}_body") }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <x-atoms.button
                variant="primary"
                size="lg"
                class="w-full"
                type="button"
                @click="showHowItWorks = false"
            >
                {{ __('onboarding.got_it') }}
            </x-atoms.button>
        </div>
    </div>
</div>
@endsection
