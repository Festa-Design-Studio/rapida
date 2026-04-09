@props([
    'currentStep' => 1,
])

@php
    $stepLabels = ['Photo', 'Location', 'Damage', 'Details', 'Review'];
@endphp

<div
    x-data="{
        step: @js((int) $currentStep),
        totalSteps: 5,
        next() { if (this.step < this.totalSteps) this.step++ },
        back() { if (this.step > 1) this.step-- },
    }"
    {{ $attributes->class(['w-full max-w-2xl mx-auto']) }}
    role="form"
    aria-label="Damage report submission wizard"
>
    {{-- Progress indicator --}}
    <div class="mb-6">
        <x-atoms.progress-step
            x-bind:current="step"
            :total="5"
            variant="dots"
            :labels="$stepLabels"
        />
        <x-atoms.progress-step
            x-bind:current="step"
            :total="5"
            variant="counter"
            :labels="$stepLabels"
        />
    </div>

    {{-- Step 1: Photo Upload --}}
    <div x-show="step === 1" x-transition role="group" aria-label="Step 1: Photo Upload">
        <x-molecules.form-field-group name="photo" label="Damage Photo" help="Take a photo of the damage or select from your gallery.">
            <x-atoms.photo-upload name="photo" label="Upload evidence photo" />
        </x-molecules.form-field-group>
    </div>

    {{-- Step 2: Location --}}
    <div x-show="step === 2" x-transition role="group" aria-label="Step 2: Location">
        <x-molecules.form-field-group name="location" label="Location" required help="Enter the address or describe the location of the damage.">
            <x-atoms.text-input
                name="location"
                placeholder="e.g. 14 Elm Street, District 3"
                required
            />
        </x-molecules.form-field-group>
    </div>

    {{-- Step 3: Damage Classification --}}
    <div x-show="step === 3" x-transition role="group" aria-label="Step 3: Damage Classification">
        <x-molecules.damage-classification name="damage_level" required />
    </div>

    {{-- Step 4: Details --}}
    <div x-show="step === 4" x-transition role="group" aria-label="Step 4: Infrastructure and Crisis Details">
        <div class="space-y-6">
            <x-molecules.infrastructure-type name="infrastructure_type" />
            <x-molecules.crisis-type name="crisis_type" />

            <x-molecules.form-field-group name="debris" label="Is there debris blocking access?" optional>
                <x-atoms.checkbox
                    name="debris_blocking"
                    value="yes"
                    label="Yes, debris is blocking access to this location"
                />
            </x-molecules.form-field-group>
        </div>
    </div>

    {{-- Step 5: Review & Submit --}}
    <div x-show="step === 5" x-transition role="group" aria-label="Step 5: Review and Submit">
        <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
            <h3 class="text-h4 font-heading font-semibold text-slate-900">Review Your Report</h3>
            <p class="text-body-sm text-slate-600">Please review the information below before submitting.</p>

            <div class="divide-y divide-slate-100 text-body-sm">
                <div class="py-3 flex justify-between">
                    <span class="text-slate-500">Photo</span>
                    <span class="text-slate-900">Attached</span>
                </div>
                <div class="py-3 flex justify-between">
                    <span class="text-slate-500">Location</span>
                    <span class="text-slate-900">Provided</span>
                </div>
                <div class="py-3 flex justify-between">
                    <span class="text-slate-500">Damage Level</span>
                    <span class="text-slate-900">Selected</span>
                </div>
                <div class="py-3 flex justify-between">
                    <span class="text-slate-500">Infrastructure</span>
                    <span class="text-slate-900">Selected</span>
                </div>
            </div>

            <x-atoms.button variant="primary" size="lg" class="w-full" type="submit">
                Submit Report
            </x-atoms.button>
        </div>
    </div>

    {{-- Navigation --}}
    <div class="mt-6 flex items-center justify-between">
        <div>
            <x-atoms.button
                variant="secondary"
                size="md"
                x-show="step > 1"
                @click="back()"
                aria-label="Go to previous step"
            >
                Back
            </x-atoms.button>
        </div>

        <x-atoms.button
            variant="ghost"
            size="sm"
            type="submit"
            aria-label="Submit what you have so far"
        >
            Submit what you have
        </x-atoms.button>

        <div>
            <x-atoms.button
                variant="primary"
                size="md"
                x-show="step < 5"
                @click="next()"
                aria-label="Go to next step"
            >
                Next
            </x-atoms.button>
        </div>
    </div>
</div>
