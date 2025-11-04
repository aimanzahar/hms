<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">{{ __('Edit Medical Record') }}</p>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('Update Consultation Notes for :patient', ['patient' => optional($medicalRecord->appointment?->patient?->user)->name ?? __('Unknown Patient')]) }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Modify diagnosis, treatment, prescriptions, and additional observations for this appointment.') }}
                </p>
            </div>

            <x-action-button :href="route('doctor.medical-records.show', $medicalRecord)" icon="view" variant="secondary">
                {{ __('Back to Record') }}
            </x-action-button>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if ($errors->any())
            <x-alert type="error" :title="__('Please review the form')">
                {{ __('Some fields require your attention before we can save changes.') }}
            </x-alert>
        @endif

        <x-app-card :title="__('Consultation Context')" :description="__('Summary of appointment details for reference.')" >
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Visit Date & Time') }}</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">
                        {{ optional($medicalRecord->appointment?->appointment_date)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') ?? __('Not available') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Patient') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ optional($medicalRecord->appointment?->patient?->user)->name ?? __('Unknown Patient') }}
                    </dd>
                    <dd class="text-xs text-gray-500">
                        {{ optional($medicalRecord->appointment?->patient?->user)->email }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Doctor') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ optional($medicalRecord->appointment?->doctor?->user)->name ?? __('Unknown Doctor') }}
                    </dd>
                    <dd class="text-xs text-gray-500">
                        {{ $medicalRecord->appointment?->doctor?->specialization }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Record Created On') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ optional($medicalRecord->created_at)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') }}
                    </dd>
                </div>
            </dl>
        </x-app-card>

        <x-app-card :title="__('Update Medical Record')" :description="__('Ensure all mandatory details are accurate before saving.')" >
            <form method="POST" action="{{ route('doctor.medical-records.update', $medicalRecord) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label :value="__('Appointment')" />
                    <div class="mt-1 rounded-lg border border-dashed border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                        {{ optional($medicalRecord->appointment?->appointment_date)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') }}
                        • {{ optional($medicalRecord->appointment?->patient?->user)->name }}
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
                    >{{ old('diagnosis', $medicalRecord->diagnosis) }}</textarea>
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
                    >{{ old('treatment', $medicalRecord->treatment) }}</textarea>
                    <x-input-error :messages="$errors->get('treatment')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="prescription" :value="__('Prescription')" />
                    <textarea
                        id="prescription"
                        name="prescription"
                        rows="3"
                        class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="{{ __('List medication names, dosage, and frequency if applicable.') }}"
                    >{{ old('prescription', $medicalRecord->prescription) }}</textarea>
                    <x-input-error :messages="$errors->get('prescription')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="notes" :value="__('Additional Notes')" />
                    <textarea
                        id="notes"
                        name="notes"
                        rows="3"
                        class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="{{ __('Include internal remarks or patient-specific reminders.') }}"
                    >{{ old('notes', $medicalRecord->notes) }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end gap-3">
                    <x-secondary-button :href="route('doctor.medical-records.show', $medicalRecord)">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-primary-button>
                        {{ __('Save Changes') }}
                    </x-primary-button>
                </div>
            </form>
        </x-app-card>
    </div>
</x-app-layout>