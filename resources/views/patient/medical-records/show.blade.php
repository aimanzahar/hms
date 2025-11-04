<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">{{ __('Medical Record Details') }}</p>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('Consultation Record') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Complete details of your medical consultation and treatment plan.') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-action-button :href="route('patient.medical-records.index')" icon="arrow-right" variant="secondary">
                    {{ __('Back to Records') }}
                </x-action-button>
                @if($medicalRecord->appointment->bill)
                    <x-action-button :href="route('patient.bills.show', $medicalRecord->appointment->bill)" icon="view">
                        {{ __('View Bill') }}
                    </x-action-button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <x-alert type="success" :title="__('Success')">
                {{ session('status') }}
            </x-alert>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Appointment Information -->
            <x-app-card class="lg:col-span-2" :title="__('Consultation Details')" :description="__('Information about your appointment and healthcare provider.')">
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Appointment Date') }}</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            {{ optional($medicalRecord->appointment->appointment_date)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') }}
                        </dd>
                        <dd class="text-xs text-gray-500">
                            {{ optional($medicalRecord->appointment->appointment_date)->diffForHumans() }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                        <dd class="mt-1">
                            <x-status-badge :status="$medicalRecord->appointment->status" size="lg" />
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Doctor') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ optional($medicalRecord->appointment->doctor?->user)->name ?? __('Unknown') }}
                        </dd>
                        <dd class="text-xs text-blue-600">
                            {{ $medicalRecord->appointment->doctor?->specialization ?? __('Specialization not specified') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('License Number') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $medicalRecord->appointment->doctor?->license_number ?? __('Not provided') }}
                        </dd>
                    </div>
                </dl>

                @if($medicalRecord->appointment->notes)
                    <div class="mt-6">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Your Notes') }}</dt>
                        <dd class="mt-2 whitespace-pre-line rounded-lg border border-dashed border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
                            {{ $medicalRecord->appointment->notes }}
                        </dd>
                    </div>
                @endif
            </x-app-card>

            <!-- Quick Actions -->
            <x-app-card :title="__('Quick Actions')" :description="__('Access related information.')">
                <div class="space-y-3">
                    <x-action-button :href="route('patient.doctors.show', $medicalRecord->appointment->doctor)" icon="view" variant="secondary" class="w-full justify-center">
                        {{ __('Doctor Profile') }}
                    </x-action-button>

                    <x-action-button :href="route('patient.appointments.show', $medicalRecord->appointment)" icon="view" variant="secondary" class="w-full justify-center">
                        {{ __('Appointment Details') }}
                    </x-action-button>

                    @if($medicalRecord->appointment->bill)
                        <x-action-button :href="route('patient.bills.show', $medicalRecord->appointment->bill)" icon="view" class="w-full justify-center">
                            {{ __('View Bill') }}
                        </x-action-button>
                    @endif

                    <div class="pt-3 border-t border-gray-200">
                        <p class="text-xs text-gray-500 text-center">
                            {{ __('Need to discuss this record?') }}<br>
                            <a href="mailto:{{ optional($medicalRecord->appointment->doctor?->user)->email }}" class="text-blue-600 hover:text-blue-800">
                                {{ __('Contact your doctor') }}
                            </a>
                        </p>
                    </div>
                </div>
            </x-app-card>
        </div>

        <!-- Medical Information -->
        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Diagnosis -->
            <x-app-card :title="__('Diagnosis')" :description="__('Medical assessment and findings from your consultation.')">
                <div class="whitespace-pre-line rounded-lg border border-dashed border-emerald-200 bg-emerald-50 p-5 text-sm leading-relaxed text-emerald-900">
                    {{ $medicalRecord->diagnosis }}
                </div>
            </x-app-card>

            <!-- Treatment -->
            <x-app-card :title="__('Treatment Plan')" :description="__('Recommended treatment and care instructions.')">
                @if($medicalRecord->treatment)
                    <div class="whitespace-pre-line rounded-lg border border-dashed border-blue-200 bg-blue-50 p-5 text-sm leading-relaxed text-blue-900">
                        {{ $medicalRecord->treatment }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No treatment specified') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('No specific treatment plan was recorded for this consultation.') }}
                        </p>
                    </div>
                @endif
            </x-app-card>
        </div>

        <!-- Prescription -->
        @if($medicalRecord->prescription)
            <x-app-card :title="__('Prescription')" :description="__('Medications and dosage instructions prescribed during this consultation.')">
                <div class="whitespace-pre-line rounded-lg border border-dashed border-purple-200 bg-purple-50 p-5 text-sm leading-relaxed text-purple-900">
                    {{ $medicalRecord->prescription }}
                </div>

                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">{{ __('Important Medication Notes') }}</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>{{ __('Take medications exactly as prescribed') }}</li>
                                    <li>{{ __('Complete the full course of antibiotics if prescribed') }}</li>
                                    <li>{{ __('Report any side effects to your doctor immediately') }}</li>
                                    <li>{{ __('Keep medications out of reach of children') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </x-app-card>
        @endif

        <!-- Additional Notes -->
        @if($medicalRecord->notes)
            <x-app-card :title="__('Additional Notes')" :description="__('Further observations and recommendations from your healthcare provider.')">
                <div class="whitespace-pre-line rounded-lg border border-dashed border-gray-200 bg-gray-50 p-5 text-sm leading-relaxed text-gray-900">
                    {{ $medicalRecord->notes }}
                </div>
            </x-app-card>
        @endif

        <!-- Follow-up Information -->
        <x-app-card :title="__('Follow-up & Next Steps')" :description="__('Recommendations for continued care and monitoring.')">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">{{ __('Schedule Follow-up') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('Book another appointment if recommended by your doctor.') }}</p>
                        <x-action-button :href="route('patient.appointments.create', ['doctor' => $medicalRecord->appointment->doctor])" icon="plus" size="sm" class="mt-2">
                            {{ __('Book Follow-up') }}
                        </x-action-button>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">{{ __('Monitor Your Health') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('Keep track of your symptoms and report any changes to your doctor.') }}</p>
                    </div>
                </div>
            </div>
        </x-app-card>

        <!-- Record Metadata -->
        <x-app-card :title="__('Record Information')" :description="__('Technical details about this medical record.')">
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Created') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ optional($medicalRecord->created_at)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Last Updated') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ optional($medicalRecord->updated_at)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Record ID') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">
                        MR-{{ str_pad($medicalRecord->id, 6, '0', STR_PAD_LEFT) }}
                    </dd>
                </div>
            </dl>
        </x-app-card>
    </div>
</x-app-layout>