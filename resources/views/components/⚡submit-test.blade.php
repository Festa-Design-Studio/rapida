<?php

use Livewire\Component;

new class extends Component {
    public string $status = 'idle';
    public int $clickCount = 0;
    public ?string $error = null;
    public bool $networkError = false;

    public function testSubmit(): void
    {
        $this->clickCount++;
        $this->status = 'submitted';
        $this->error = null;
    }

    public function testSlowSubmit(): void
    {
        $this->clickCount++;
        sleep(2);
        $this->status = 'slow-submitted';
    }

    public function testErrorSubmit(): void
    {
        $this->clickCount++;
        throw new \RuntimeException('Deliberate test error');
    }

    public function resetTest(): void
    {
        $this->status = 'idle';
        $this->clickCount = 0;
        $this->error = null;
        $this->networkError = false;
    }
};
?>

<div class="max-w-md mx-auto p-6 space-y-6 font-sans">
    <h1 class="text-2xl font-bold">Submit Button Test Page</h1>

    {{-- Status display --}}
    <div class="p-4 rounded-lg bg-slate-100 space-y-2">
        <p><strong>Status:</strong> <span id="test-status">{{ $status }}</span></p>
        <p><strong>Click count:</strong> <span id="test-clicks">{{ $clickCount }}</span></p>
        @if($error)
            <p class="text-crisis-rose-700"><strong>Error:</strong> {{ $error }}</p>
        @endif
    </div>

    {{-- Test 1: Basic wire:click (same as nextStep) --}}
    <div class="space-y-2">
        <h2 class="font-semibold">Test 1: Basic wire:click</h2>
        <button
            type="button"
            wire:click="testSubmit"
            class="w-full h-14 bg-blue-700 text-white font-semibold rounded-lg cursor-pointer active:scale-[0.98]"
        >
            Basic Submit (wire:click)
        </button>
    </div>

    {{-- Test 2: wire:click with loading states (same as the real submit button) --}}
    <div class="space-y-2">
        <h2 class="font-semibold">Test 2: With wire:loading (like real button)</h2>
        <button
            type="button"
            wire:click="testSubmit"
            wire:loading.attr="disabled"
            wire:loading.class="cursor-wait pointer-events-none"
            wire:target="testSubmit"
            class="w-full h-14 bg-green-700 text-white font-semibold rounded-lg cursor-pointer active:scale-[0.98] disabled:opacity-40 disabled:pointer-events-none"
        >
            <span wire:loading.remove wire:target="testSubmit">Submit with Loading States</span>
            <span wire:loading wire:target="testSubmit">Loading...</span>
        </button>
    </div>

    {{-- Test 3: Slow submit (2s delay) --}}
    <div class="space-y-2">
        <h2 class="font-semibold">Test 3: Slow submit (2s delay)</h2>
        <button
            type="button"
            wire:click="testSlowSubmit"
            wire:loading.attr="disabled"
            wire:loading.class="cursor-wait pointer-events-none"
            wire:target="testSlowSubmit"
            class="w-full h-14 bg-amber-600 text-white font-semibold rounded-lg cursor-pointer active:scale-[0.98] disabled:opacity-40 disabled:pointer-events-none"
        >
            <span wire:loading.remove wire:target="testSlowSubmit">Slow Submit (2s)</span>
            <span wire:loading wire:target="testSlowSubmit">Processing...</span>
        </button>
    </div>

    {{-- Test 4: Via x-atoms.button component (exactly like the wizard) --}}
    <div class="space-y-2">
        <h2 class="font-semibold">Test 4: Via x-atoms.button component</h2>
        <x-atoms.button
            variant="primary"
            size="lg"
            wire:click="testSubmit"
            wire:loading.attr="disabled"
            wire:loading.class="cursor-wait pointer-events-none"
            wire:target="testSubmit"
            class="w-full"
        >
            <span wire:loading.remove wire:target="testSubmit">Button Component Submit</span>
            <span wire:loading wire:target="testSubmit" class="inline-flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                Submitting...
            </span>
        </x-atoms.button>
    </div>

    {{-- Reset --}}
    <button
        type="button"
        wire:click="resetTest"
        class="w-full h-10 bg-slate-500 text-white rounded-lg text-sm"
    >
        Reset All
    </button>

    {{-- JS diagnostics --}}
    @script
    <script>
        console.log('[submit-test] $wire available:', typeof $wire !== 'undefined');
        console.log('[submit-test] Livewire available:', typeof Livewire !== 'undefined');
        console.log('[submit-test] $wire.interceptRequest available:', typeof $wire?.interceptRequest);
        console.log('[submit-test] Livewire.interceptRequest available:', typeof Livewire?.interceptRequest);

        Livewire.interceptRequest(({ onError, onFailure }) => {
            console.log('[submit-test] interceptRequest registered');

            onError(({ response, preventDefault }) => {
                console.log('[submit-test] onError:', response.status);
            });

            onFailure(({ error }) => {
                console.log('[submit-test] onFailure:', error);
            });
        });
    </script>
    @endscript
</div>
