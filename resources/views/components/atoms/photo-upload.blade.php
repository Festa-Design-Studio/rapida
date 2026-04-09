@props([
    'name' => 'photo',
    'label' => null,
    'required' => false,
    'maxSize' => 10,
    'accept' => 'image/jpeg,image/png,image/webp,image/heic',
    'help' => null,
    'error' => null,
    'multiple' => false,
    'maxFiles' => 3,
])

@php
    $inputId = $attributes->get('id', $name);
    $errorId = $error ? "{$inputId}-error" : null;
@endphp

<div
    x-data="{
        state: 'empty',
        previewUrl: null,
        errorMessage: '',
        triggerInput() { this.$refs.fileInput.click() },
        handleFile(e) {
            const file = e.target.files[0];
            if (!file) return;
            const maxBytes = {{ $maxSize }} * 1024 * 1024;
            if (file.size > maxBytes) {
                this.state = 'error';
                this.errorMessage = 'Photo is too large. Maximum size is {{ $maxSize }} MB.';
                return;
            }
            this.previewUrl = URL.createObjectURL(file);
            this.state = 'preview';
        },
        removePhoto() {
            this.previewUrl = null;
            this.state = 'empty';
            this.$refs.fileInput.value = '';
        },
        handleDrop(e) {
            this.state = 'empty';
            const file = e.dataTransfer.files[0];
            if (file) {
                const dt = new DataTransfer();
                dt.items.add(file);
                this.$refs.fileInput.files = dt.files;
                this.handleFile({ target: { files: [file] } });
            }
        }
    }"
    class="flex flex-col gap-1.5"
>
    @if($label)
        <span class="text-label font-medium text-slate-700">
            {{ $label }}
            @if($required)
                <span class="text-red-600 ml-0.5" aria-hidden="true">*</span>
            @endif
        </span>
    @endif

    <div
        x-bind:class="{
            'border-slate-300 bg-slate-50': state === 'empty',
            'border-rapida-blue-500 bg-rapida-blue-50': state === 'dragover',
            'border-rapida-blue-700 bg-white': state === 'preview',
            'border-red-600 bg-red-50': state === 'error'
        }"
        class="relative flex flex-col items-center justify-center
               min-h-[160px] rounded-xl border-2 border-dashed
               transition-colors duration-150 cursor-pointer"
        role="button"
        tabindex="0"
        x-bind:aria-label="state === 'preview' ? 'Photo selected. Click to change.' : 'Upload photo of damage{{ $required ? ' — required' : '' }}'"
        @click="state !== 'preview' && triggerInput()"
        @keydown.enter.prevent="state !== 'preview' ? triggerInput() : null"
        @dragover.prevent="state = 'dragover'"
        @dragleave="state = 'empty'"
        @drop.prevent="handleDrop"
    >
        {{-- Empty state --}}
        <template x-if="state === 'empty' || state === 'dragover'">
            <div class="flex flex-col items-center gap-3 p-6 text-center">
                <svg class="w-12 h-12 text-slate-400" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                    <rect x="6" y="10" width="36" height="28" rx="4" stroke="currentColor" stroke-width="2"/>
                    <circle cx="18" cy="22" r="4" stroke="currentColor" stroke-width="2"/>
                    <path d="M6 32l10-8 6 4 10-10 10 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div>
                    <p class="text-body-sm font-medium text-slate-700" x-text="state === 'dragover' ? 'Drop your photo here' : 'Take a photo or choose from gallery'"></p>
                    <p class="text-caption text-slate-400 mt-1">JPEG, PNG, WebP or HEIC — max {{ $maxSize }} MB</p>
                </div>
            </div>
        </template>

        {{-- Preview state --}}
        <template x-if="state === 'preview'">
            <div class="w-full">
                <img x-bind:src="previewUrl" class="w-full rounded-t-lg object-cover max-h-48" alt="Damage photo preview"/>
                <div class="flex items-center justify-center gap-4 px-4 py-2 bg-slate-50 rounded-b-lg border-t border-slate-200">
                    <button type="button" @click="triggerInput()" class="text-body-sm font-medium text-rapida-blue-700 hover:text-rapida-blue-900">Change</button>
                    <span class="text-slate-300">|</span>
                    <button type="button" @click="removePhoto()" class="text-body-sm font-medium text-red-600 hover:text-red-800">Remove</button>
                </div>
            </div>
        </template>

        {{-- Error state --}}
        <template x-if="state === 'error'">
            <div class="flex flex-col items-center gap-3 p-6 text-center" @click="triggerInput()">
                <svg class="w-10 h-10 text-red-400" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-body-sm text-red-700" x-text="errorMessage"></p>
                <p class="text-caption text-slate-500">Tap to try again</p>
            </div>
        </template>

        <input
            x-ref="fileInput"
            type="file"
            name="{{ $name }}"
            accept="{{ $accept }}"
            capture="environment"
            @if($multiple) multiple @endif
            class="sr-only"
            aria-hidden="true"
            @change="handleFile"
        />
    </div>

    @if($error)
        <p id="{{ $errorId }}" role="alert" class="text-body-sm text-red-700">{{ $error }}</p>
    @endif

    @if($help)
        <p class="text-body-sm text-slate-500">{{ $help }}</p>
    @else
        <p class="text-body-sm text-slate-500">Your photo will be stored securely and used only for damage assessment by UNDP.</p>
    @endif
</div>
