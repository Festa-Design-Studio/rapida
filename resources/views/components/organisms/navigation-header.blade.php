@props([
    'currentRoute' => null,
    'crisis' => null,
])

<header
    {{ $attributes->class([
        'sticky top-0 z-40 bg-gradient-to-r from-slate-50 to-rapida-blue-50 border-b border-slate-200',
    ]) }}
    role="banner"
>
    {{-- flex-nowrap keeps the row single-line. Fits at 375px because the
         language switcher shows just the language name (no "EN — " prefix)
         and the Safe Exit button is icon-less and label-only. min-w-0 on
         children lets the logo cluster compress when needed. --}}
    <div class="min-h-16 px-3 md:px-6 py-2 flex flex-nowrap items-center justify-between gap-x-3 max-w-7xl mx-auto">
        {{-- Logo / Brand --}}
        <div class="flex items-center min-w-0">
            <a href="{{ route('map-home') }}" class="flex items-center min-h-[48px] min-w-[48px]" aria-label="RAPIDA home">
                <x-atoms.logo size="md" variant="responsive" class="text-rapida-blue-700" />
            </a>
        </div>

        {{-- Center nav (desktop) --}}
        <nav class="hidden md:flex items-center gap-1" aria-label="Main navigation">
            @php
                $navItems = [
                    ['label' => __('rapida.nav_report'), 'route' => 'report', 'href' => route('submit')],
                    ['label' => __('rapida.nav_map'), 'route' => 'map', 'href' => route('map-home')],
                    ['label' => __('rapida.nav_my_reports'), 'route' => 'my-reports', 'href' => route('my-reports')],
                ];
            @endphp
            @foreach($navItems as $item)
                <a
                    href="{{ $item['href'] }}"
                    class="px-4 py-2 rounded-lg text-body-sm font-medium transition-colors duration-150 min-h-[48px] flex items-center
                           {{ $currentRoute === $item['route']
                               ? 'bg-rapida-blue-100 text-rapida-blue-900'
                               : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                    @if($currentRoute === $item['route']) aria-current="page" @endif
                >
                    {{ $item['label'] }}
                </a>
            @endforeach

            @auth('undp')
                <a
                    href="{{ route('dashboard') }}"
                    class="px-4 py-2 rounded-lg text-body-sm font-medium transition-colors duration-150 min-h-[48px] flex items-center
                           {{ $currentRoute === 'dashboard'
                               ? 'bg-rapida-blue-100 text-rapida-blue-900'
                               : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                    @if($currentRoute === 'dashboard') aria-current="page" @endif
                >
                    {{ __('rapida.nav_dashboard') }}
                </a>

                @if(in_array(auth('undp')->user()->role->value, ['operator', 'superadmin']))
                    <a
                        href="{{ route('admin.index') }}"
                        class="px-4 py-2 rounded-lg text-body-sm font-medium transition-colors duration-150 min-h-[48px] flex items-center
                               {{ $currentRoute === 'admin'
                                   ? 'bg-rapida-blue-100 text-rapida-blue-900'
                                   : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                        @if($currentRoute === 'admin') aria-current="page" @endif
                    >
                        {{ __('rapida.nav_admin') }}
                    </a>
                @endif
            @endauth
        </nav>

        {{-- Right actions --}}
        <div class="flex items-center gap-2">
            {{-- Sync indicator (Alpine offlineQueue store) --}}
            <div x-data class="hidden sm:block">
                <template x-if="$store.offlineQueue && !$store.offlineQueue.isOnline">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-caption font-medium rounded-full bg-alert-amber-50 text-alert-amber-900 border border-alert-amber-100">
                        <x-atoms.icon name="cloud-offline" size="xs" />
                        {{ __('rapida.status_offline') }}
                    </span>
                </template>
                <template x-if="$store.offlineQueue && $store.offlineQueue.isOnline && $store.offlineQueue.pendingCount > 0">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-caption font-medium rounded-full bg-rapida-blue-50 text-rapida-blue-900 border border-rapida-blue-100">
                        <x-atoms.icon name="cloud-upload" size="xs" />
                        <span x-text="$store.offlineQueue.pendingCount + ' syncing'"></span>
                    </span>
                </template>
                <template x-if="$store.offlineQueue && $store.offlineQueue.isOnline && $store.offlineQueue.pendingCount === 0">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-caption font-medium rounded-full bg-ground-green-50 text-ground-green-900 border border-ground-green-200">
                        <x-atoms.icon name="cloud-upload" size="xs" />
                        {{ __('rapida.status_online') }}
                    </span>
                </template>
            </div>

            {{-- Language switcher — crisis-aware via $__rapidaLanguageMenu
                 shared from AppServiceProvider's layouts.rapida composer.
                 Current locale reflects middleware-resolved value (not raw
                 session), so switcher badge matches what the user actually
                 sees after Accept-Language/cookie negotiation. --}}
            <x-molecules.language-switcher
                :current="app()->getLocale()"
                :languages="$__rapidaLanguageMenu ?? collect(config('app.supported_locales', ['en']))
                    ->mapWithKeys(fn (string $code) => [$code => config('app.language_names.'.$code, $code)])
                    ->all()"
                variant="dropdown"
            />

            {{-- Logout (UNDP authenticated users) --}}
            @auth('undp')
                <form method="POST" action="{{ route('undp.logout') }}" class="inline">
                    @csrf
                    <x-atoms.button type="submit" variant="ghost" size="sm">
                        {{ __('rapida.nav_logout') }}
                    </x-atoms.button>
                </form>
            @endauth

            {{-- Safe Exit — label-only, no leading icon. The button keeps its
                 ARIA label so assistive tech still announces the destructive
                 escape semantic. --}}
            <x-atoms.button
                variant="safe-exit"
                size="md"
                aria-label="Safe exit — quickly leave this page"
                onclick="window.location.href='https://www.google.com'"
            >
                {{ __('rapida.safe_exit') }}
            </x-atoms.button>
        </div>
    </div>
</header>
