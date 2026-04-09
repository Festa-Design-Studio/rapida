@props([
    'crisisType' => 'earthquake',
])

@php
    $guidelines = [
        'earthquake' => [
            ['icon' => '📸', 'text' => __('wizard.photo_guide_front', ['default' => 'Stand facing the FRONT of the building'])],
            ['icon' => '🏠', 'text' => __('wizard.photo_guide_whole', ['default' => 'Capture the whole building in the frame'])],
            ['icon' => '🔍', 'text' => __('wizard.photo_guide_closeup', ['default' => 'Take a close-up of the most damaged area'])],
            ['icon' => '🚫', 'text' => __('wizard.photo_guide_no_faces', ['default' => 'Do not photograph people or faces'])],
            ['icon' => '📍', 'text' => __('wizard.photo_guide_gps', ['default' => 'GPS is captured from where you stand — stand at the front'])],
        ],
        'wildfire' => [
            ['icon' => '📸', 'text' => __('wizard.photo_guide_perimeter', ['default' => 'Photograph from a safe distance — do not approach'])],
            ['icon' => '🏠', 'text' => __('wizard.photo_guide_whole', ['default' => 'Capture the whole building in the frame'])],
            ['icon' => '🔍', 'text' => __('wizard.photo_guide_closeup', ['default' => 'Take a close-up of the most damaged area'])],
            ['icon' => '🚫', 'text' => __('wizard.photo_guide_no_faces', ['default' => 'Do not photograph people or faces'])],
            ['icon' => '📍', 'text' => __('wizard.photo_guide_gps', ['default' => 'GPS is captured from where you stand'])],
        ],
    ];

    $guide = $guidelines[$crisisType] ?? $guidelines['earthquake'];
@endphp

<div
    x-data="{
        visible: !sessionStorage.getItem('rapida_photo_guidance_shown'),
        dismiss() {
            sessionStorage.setItem('rapida_photo_guidance_shown', 'true');
            this.visible = false;
            this.$dispatch('photo-guidance-dismissed');
        }
    }"
    x-show="visible"
    x-transition:enter="transition ease-out duration-calm"
    x-transition:enter-start="translate-y-full"
    x-transition:enter-end="translate-y-0"
    x-transition:leave="transition ease-in duration-fast"
    x-transition:leave-start="translate-y-0"
    x-transition:leave-end="translate-y-full"
    class="fixed inset-x-0 bottom-0 z-40 bg-white rounded-t-xl shadow-lg border-t border-grey-100 p-padding-card pb-8"
    role="complementary"
    aria-label="Photo guidance"
    @class(['motion-reduce:transition-none'])
>
    <div class="max-w-md mx-auto space-y-element">
        <h3 class="text-h4 font-heading font-semibold text-text-primary text-center">
            {{ __('wizard.photo_guide_title', ['default' => 'How to take a useful photo']) }}
        </h3>

        <ul class="space-y-micro">
            @foreach($guide as $item)
                <li class="flex items-start gap-3">
                    <span class="text-body shrink-0">{{ $item['icon'] }}</span>
                    <span class="text-body-sm text-text-primary">{{ $item['text'] }}</span>
                </li>
            @endforeach
        </ul>

        <x-atoms.button
            variant="primary"
            class="w-full"
            @click="dismiss()"
            x-data="{
                label: '{{ __("rapida.photo_guidance_cta_first") }}',
                async init() {
                    try {
                        const result = await navigator.permissions.query({ name: 'camera' });
                        if (result.state === 'granted') this.label = '{{ __("rapida.photo_guidance_cta_repeat") }}';
                    } catch(e) {}
                }
            }"
            x-text="label"
        >
            {{ __('rapida.photo_guidance_cta_first') }}
        </x-atoms.button>
    </div>
</div>
