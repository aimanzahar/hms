<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">{{ __('Medical Record') }}</p>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('Consultation Summary for :patient', ['patient' => optional($medicalRecord->appointment?->patient?->user)->name ?? __('Unknown Patient')]) }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Review diagnosis details, treatment actions, prescriptions, and clinical notes recorded during this visit.') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-action-button :href="route('doctor.medical-records.index')" icon="arrow-right" variant="secondary">
                    {{ __('Back to Records') }}
                </x-action-button>
                <x-action-button :href="route('doctor.medical-records.edit', $medicalRecord)" icon="edit">
                    {{ __('Edit Record') }}
                </x-action-button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-6 lg:grid-cols-3">
            <x-app-card class="lg:col-span-2" :title="__('Consultation Overview')" :description="__('Key context for this medical record.')" >
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Visit Date & Time') }}</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            {{ optional($medicalRecord->appointment?->appointment_date)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') ?? __('Not available') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Recorded On') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ optional($medicalRecord->created_at)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') }}
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
                        <dt class="text-sm font-medium text-gray-500">{{ __('Attending Doctor') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ optional($medicalRecord->appointment?->doctor?->user)->name ?? __('Unknown Doctor') }}
                        </dd>
                        <dd class="text-xs text-gray-500">
                            {{ $medicalRecord->appointment?->doctor?->specialization }}
                        </dd>
                    </div>
                </dl>
            </x-app-card>

            <x-app-card :title="__('Actions')" :description="__('Complete follow-up items associated with this record.')" class="h-full">
                <div class="space-y-4 text-sm text-gray-600">
                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                        <p class="font-medium text-gray-900">{{ __('Update Record') }}</p>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ __('Adjust diagnosis, treatment, or notes if additional information is available.') }}
                        </p>
                        <x-action-button :href="route('doctor.medical-records.edit', $medicalRecord)" icon="edit" size="sm" class="mt-3">
                            {{ __('Edit Medical Record') }}
                        </x-action-button>
                    </div>

                    <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 shadow-sm">
                        <p class="font-medium text-blue-900">{{ __('Go to Appointment') }}</p>
                        <p class="mt-1 text-xs text-blue-700">
                            {{ __('Review appointment context or update its status.') }}
                        </p>
                        <x-action-button :href="route('doctor.appointments.show', $medicalRecord->appointment)" icon="arrow-right" size="sm" variant="secondary" class="mt-3">
                            {{ __('View Appointment') }}
                        </x-action-button>
                    </div>

                    <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 shadow-sm">
                        <p class="font-medium text-amber-900">{{ __('Issue Billing') }}</p>
                        <p class="mt-1 text-xs text-amber-700">
                            {{ __('Ensure an invoice is generated if the consultation requires payment.') }}
                        </p>
                        @if($medicalRecord->appointment?->bill)
                            <x-action-button :href="route('doctor.bills.show', $medicalRecord->appointment->bill)" icon="view" size="sm" class="mt-3">
                                {{ __('Review Existing Bill') }}
                            </x-action-button>
                        @else
                            <x-action-button :href="route('doctor.bills.create', ['appointment' => $medicalRecord->appointment])" icon="plus" size="sm" class="mt-3">
                                {{ __('Create New Bill') }}
                            </x-action-button>
                        @endif
                    </div>
                </div>
            </x-app-card>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <x-app-card :title="__('Diagnosis')" :description="__('Primary observations and medical conclusion from the consultation.')">
                <div class="whitespace-pre-line rounded-lg border border-dashed border-emerald-200 bg-emerald-50 p-5 text-sm leading-relaxed text-emerald-900">
                    {{ $medicalRecord->diagnosis }}
                </div>
            </x-app-card>

            <x-app-card :title="__('Treatment Plan')" :description="__('Recommended procedures, therapies, or follow-up actions.')" >
                <div class="whitespace-pre-line rounded-lg border border-dashed border-blue-200 bg-blue-50 p-5 text-sm leading-relaxed text-blue-900">
                    {{ $medicalRecord->treatment }}
                </div>
            </x-app-card>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <x-app-card :title="__('Prescription')" :description="__('Medication or supplements prescribed to the patient.')" >
                <div class="whitespace-pre-line rounded-lg border border-dashed border-amber-200 bg-amber-50 p-5 text-sm leading-relaxed text-amber-900">
                    {{ $medicalRecord->prescription ?? __('No prescription recorded for this consultation.') }}
                </div>
            </x-app-card>

            <x-app-card :title="__('Additional Notes')" :description="__('Internal notes, reminders, or patient guidance provided.')" >
                <div class="whitespace-pre-line rounded-lg border border-dashed border-gray-200 bg-gray-50 p-5 text-sm leading-relaxed text-gray-800">
                    {{ $medicalRecord->notes ?? __('No additional notes were added.') }}
                </div>
            </x-app-card>
        </div>
    </div>
</x-app-layout>