@props(['current' => 'crises'])

<nav class="bg-white border-b border-slate-200 px-4 md:px-6">
    <div class="max-w-7xl mx-auto flex gap-1">
        @foreach([
            'crises' => ['label' => 'Crises', 'route' => 'admin.crises.index'],
            'landmarks' => ['label' => 'Landmarks', 'route' => 'admin.landmarks.index'],
            'users' => ['label' => 'Users', 'route' => 'admin.users.index'],
        ] as $key => $item)
            <a href="{{ route($item['route']) }}"
               class="px-4 py-3 text-body-sm font-medium border-b-2 transition-colors
                      {{ $current === $key
                          ? 'border-rapida-blue-700 text-rapida-blue-900'
                          : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}"
               @if($current === $key) aria-current="page" @endif
            >
                {{ $item['label'] }}
            </a>
        @endforeach
    </div>
</nav>
