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
    <div class="h-16 px-4 md:px-6 flex items-center justify-between max-w-7xl mx-auto">
        {{-- Logo / Brand --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('map-home') }}" class="flex items-center gap-2 min-h-[48px] min-w-[48px]" aria-label="RAPIDA home">
                <x-atoms.icon name="pin" size="md" class="text-rapida-blue-700" />
                <span class="text-h4 font-heading font-bold text-rapida-blue-900 hidden sm:inline">RAPIDA</span>
            </a>
        </div>

        {{-- Center nav (desktop) --}}
        <nav class="hidden md:flex items-center gap-1" aria-label="Main navigation">
            @php
                $navItems = [
                    ['label' => 'Report', 'route' => 'report', 'href' => route('submit')],
                    ['label' => 'Map', 'route' => 'map', 'href' => route('map-home')],
                    ['label' => 'My Reports', 'route' => 'my-reports', 'href' => route('my-reports')],
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
                @if(in_array(auth('undp')->user()->role->value, ['operator', 'superadmin']))
                    <a
                        href="{{ route('admin.index') }}"
                        class="px-4 py-2 rounded-lg text-body-sm font-medium transition-colors duration-150 min-h-[48px] flex items-center
                               {{ $currentRoute === 'admin'
                                   ? 'bg-rapida-blue-100 text-rapida-blue-900'
                                   : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                        @if($currentRoute === 'admin') aria-current="page" @endif
                    >
                        Admin
                    </a>
                @endif
            @endauth
        </nav>

        {{-- Right actions --}}
        <div class="flex items-center gap-2">
            {{-- Sync indicator (Alpine offlineQueue store) --}}
            <div x-data class="hidden sm:block">
                <template x-if="$store.offlineQueue && !$store.offlineQueue.isOnline">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-caption font-medium rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                        <x-atoms.icon name="cloud-offline" size="xs" />
                        Offline
                    </span>
                </template>
                <template x-if="$store.offlineQueue && $store.offlineQueue.isOnline && $store.offlineQueue.pendingCount > 0">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-caption font-medium rounded-full bg-rapida-blue-50 text-rapida-blue-900 border border-rapida-blue-100">
                        <x-atoms.icon name="cloud-upload" size="xs" />
                        <span x-text="$store.offlineQueue.pendingCount + ' syncing'"></span>
                    </span>
                </template>
                <template x-if="$store.offlineQueue && $store.offlineQueue.isOnline && $store.offlineQueue.pendingCount === 0">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-caption font-medium rounded-full bg-green-50 text-green-800 border border-green-200">
                        <x-atoms.icon name="cloud-upload" size="xs" />
                        Online
                    </span>
                </template>
            </div>

            {{-- Language switcher --}}
            <div class="hidden md:block">
                <x-molecules.language-switcher
                    current="{{ session('locale', 'en') }}"
                    :languages="['en' => 'English', 'fr' => 'French', 'ar' => 'Arabic']"
                />
            </div>

            {{-- Safe Exit --}}
            <x-atoms.button
                variant="safe-exit"
                size="md"
                aria-label="Safe exit — quickly leave this page"
                onclick="window.location.href='https://www.google.com'"
            >
                <x-atoms.icon name="shield-exit" size="sm" />
                Safe Exit
            </x-atoms.button>
        </div>
    </div>
</header>
