<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">{{ __('New Medical Record') }}</p>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('Document Consultation Outcome') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Capture diagnosis, treatment plan, prescriptions, and follow-up notes for the selected appointment.') }}
                </p>
            </div>

            <x-action-button :href="route('doctor.medical-records.index')" icon="arrow-right" variant="secondary">
                {{ __('Back to Records') }}
            </x-action-button>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if ($errors->any())
            <x-alert type="error" :title="__('Please review the form')">
                {{ __('Some fields require your attention before we can save this record.') }}
            </x-alert>
        @endif

        <x-app-card :title="__('Record Details')" :description="__('All fields marked * are required.')" id="medical-record-form">
            <form method="POST" action="{{ route('doctor.medical-records.store') }}" class="space-y-6">
                @csrf

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <x-input-label for="appointment_id" :value="__('Appointment *')" />
                        <select
                            id="appointment_id"
                            name="appointment_id"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400"
                            required
                        >
                            <option value="">{{ __('Select appointment') }}</option>
                            @foreach($appointments ?? [] as $appointmentOption)
                                <option value="{{ $appointmentOption->id }}" @selected(old('appointment_id', request('appointment')) == $appointmentOption->id)>
                                    {{ optional($appointmentOption->appointment_date)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') }} —
                                    {{ optional($appointmentOption->patient?->user)->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('appointment_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label :value="__('Patient')" />
                        <div class="mt-1 rounded-lg border border-dashed border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                            <span x-data="{ appointment: '{{ old('appointment_id', request('appointment')) }}' }"
                                  x-text="appointment ? '{{ __('Patient will populate after saving.') }}' : '{{ __('Select an appointment to auto-fill patient information after saving.') }}'">
                            </span>
                        </div>
                    </div>
                </div>

                <div>
                    <x-input-label for="diagnosis" :value="__('Diagnosis *')" />
                    <textarea
                        id="diagnosis"
                        name="diagnosis"
                        rows="4"
                        class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        required
                    >{{ old('diagnosis') }}</textarea>
                    <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="treatment" :value="__('Treatment Plan *')" />
                    <textarea
                        id="treatment"
                        name="treatment"
                        rows="4"
                        class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        required
                    >{{ old('treatment') }}</textarea>
                    <x-input-error :messages="$errors->get('treatment')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="prescription" :value="__('Prescription')" />
                    <textarea
                        id="prescription"
                        name="prescription"
                        rows="3"
                        class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="{{ __('List medications, dosage, and frequency if applicable.') }}"
                    >{{ old('prescription') }}</textarea>
                    <x-input-error :messages="$errors->get('prescription')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="notes" :value="__('Additional Notes')" />
                    <textarea
                        id="notes"
                        name="notes"
                        rows="3"
                        class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="{{ __('Include follow-up reminders or patient instructions.') }}"
                    >{{ old('notes') }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end gap-3">
                    <x-secondary-button :href="route('doctor.medical-records.index')">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-primary-button>
                        {{ __('Save Record') }}
                    </x-primary-button>
                </div>
            </form>
        </x-app-card>
    </div>
</x-app-layout>