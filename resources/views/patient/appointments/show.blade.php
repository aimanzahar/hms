<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">{{ __('Appointment Details') }}</p>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('Session with :doctor', ['doctor' => optional($appointment->doctor?->user)->name ?? __('Unknown Doctor')]) }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Review your appointment information and access related medical records.') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-action-button :href="route('patient.appointments.index')" icon="arrow-right" variant="secondary">
                    {{ __('Back to Appointments') }}
                </x-action-button>
                @if(in_array($appointment->status, ['pending', 'confirmed']))
                    <form method="POST" action="{{ route('patient.appointments.destroy', $appointment) }}" onsubmit="return confirm('{{ __('Are you sure you want to cancel this appointment?') }}');">
                        @csrf
                        @method('DELETE')
                        <x-action-button as="button" type="submit" icon="trash" variant="danger">
                            {{ __('Cancel Appointment') }}
                        </x-action-button>
                    </form>
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
            <!-- Appointment Overview -->
            <x-app-card class="lg:col-span-2" :title="__('Appointment Overview')" :description="__('Complete details of your scheduled consultation.')">
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Scheduled For') }}</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            {{ optional($appointment->appointment_date)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') ?? __('Not scheduled') }}
                        </dd>
                        <dd class="text-xs text-gray-500">
                            {{ optional($appointment->appointment_date)->diffForHumans() }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                        <dd class="mt-1">
                            <x-status-badge :status="$appointment->status" size="lg" />
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Doctor') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ optional($appointment->doctor?->user)->name ?? __('Unknown') }}
                        </dd>
                        <dd class="text-xs text-blue-600">
                            {{ $appointment->doctor?->specialization ?? __('Specialization not specified') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Consultation Fee') }}</dt>
                        <dd class="mt-1 text-sm font-semibold text-green-600">
                            {{ __('RM :fee', ['fee' => number_format($appointment->doctor?->consultation_fee ?? 0, 2)]) }}
                        </dd>
                    </div>
                </dl>

                <div class="mt-6">
                    <dt class="text-sm font-medium text-gray-500">{{ __('Your Notes') }}</dt>
                    <dd class="mt-2 whitespace-pre-line rounded-lg border border-dashed border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
                        {{ $appointment->notes ?? __('No notes were provided for this appointment.') }}
                    </dd>
                </div>
            </x-app-card>

            <!-- Doctor Information -->
            <x-app-card :title="__('Doctor Information')" :description="__('Details about your healthcare provider.')">
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Name') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ optional($appointment->doctor?->user)->name ?? __('Not provided') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Specialization') }}</dt>
                        <dd class="mt-1 text-sm text-blue-600">
                            {{ $appointment->doctor?->specialization ?? __('Not specified') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('MMC License') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $appointment->doctor?->license_number ?? __('Not provided') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Experience') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ number_format($appointment->doctor?->experience_years ?? 0) }} {{ __('years') }}
                        </dd>
                    </div>
                </dl>

                <div class="mt-4">
                    <x-action-button :href="route('patient.doctors.show', $appointment->doctor)" icon="view" variant="secondary" size="sm">
                        {{ __('View Doctor Profile') }}
                    </x-action-button>
                </div>
            </x-app-card>
        </div>

        <!-- Medical Record (if completed) -->
        @if($appointment->medicalRecord)
            <x-app-card :title="__('Medical Record')" :description="__('Consultation summary and treatment details from your visit.')">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Diagnosis') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $appointment->medicalRecord->diagnosis }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Treatment') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $appointment->medicalRecord->treatment ?? __('No treatment specified') }}
                        </dd>
                    </div>

                    @if($appointment->medicalRecord->prescription)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Prescription') }}</dt>
                            <dd class="mt-1 whitespace-pre-line rounded-lg border border-dashed border-green-200 bg-green-50 p-4 text-sm text-green-900">
                                {{ $appointment->medicalRecord->prescription }}
                            </dd>
                        </div>
                    @endif

                    @if($appointment->medicalRecord->notes)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Additional Notes') }}</dt>
                            <dd class="mt-1 whitespace-pre-line rounded-lg border border-dashed border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
                                {{ $appointment->medicalRecord->notes }}
                            </dd>
                        </div>
                    @endif
                </div>

                <div class="mt-4">
                    <x-action-button :href="route('patient.medical-records.show', $appointment->medicalRecord)" icon="view" variant="secondary" size="sm">
                        {{ __('View Full Medical Record') }}
                    </x-action-button>
                </div>
            </x-app-card>
        @else
            <x-app-card :title="__('Medical Record')" :description="__('Consultation summary will be available after your appointment is completed.')">
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Medical record not yet available') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('Your medical record will be created by the doctor after the consultation is completed.') }}
                    </p>
                </div>
            </x-app-card>
        @endif

        <!-- Billing Information -->
        @if($appointment->bill)
            <x-app-card :title="__('Billing Information')" :description="__('Invoice details for this consultation.')">
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Bill ID') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ __('BILL-:id', ['id' => str_pad($appointment->bill->id, 5, '0', STR_PAD_LEFT)]) }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Total Amount') }}</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            {{ __('RM :amount', ['amount' => number_format($appointment->bill->total_amount, 2)]) }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                        <dd class="mt-1">
                            <x-status-badge :status="$appointment->bill->status" />
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Due Date') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ optional($appointment->bill->due_date)->format('d/m/Y') ?? __('Not set') }}
                        </dd>
                    </div>
                </dl>

                <div class="mt-4">
                    <x-action-button :href="route('patient.bills.show', $appointment->bill)" icon="view" variant="secondary" size="sm">
                        {{ __('View Bill Details') }}
                    </x-action-button>
                </div>
            </x-app-card>
        @endif

        <!-- Appointment Actions -->
        <x-app-card :title="__('Appointment Actions')" :description="__('What you can do with this appointment.')">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @if(in_array($appointment->status, ['pending', 'confirmed']))
                    <div class="text-center">
                        <form method="POST" action="{{ route('patient.appointments.destroy', $appointment) }}" onsubmit="return confirm('{{ __('Are you sure you want to cancel this appointment? This action cannot be undone.') }}');">
                            @csrf
                            @method('DELETE')
                            <x-danger-button as="button" type="submit" class="w-full">
                                {{ __('Cancel Appointment') }}
                            </x-danger-button>
                        </form>
                        <p class="mt-2 text-xs text-gray-500">{{ __('24 hours notice required') }}</p>
                    </div>
                @endif

                <x-action-button :href="route('patient.doctors.show', $appointment->doctor)" icon="view" variant="secondary" class="w-full justify-center">
                    {{ __('Doctor Profile') }}
                </x-action-button>

                @if($appointment->medicalRecord)
                    <x-action-button :href="route('patient.medical-records.show', $appointment->medicalRecord)" icon="view" variant="secondary" class="w-full justify-center">
                        {{ __('Medical Record') }}
                    </x-action-button>
                @endif

                @if($appointment->bill)
                    <x-action-button :href="route('patient.bills.show', $appointment->bill)" icon="view" variant="secondary" class="w-full justify-center">
                        {{ __('View Bill') }}
                    </x-action-button>
                @endif
            </div>
        </x-app-card>
    </div>
</x-app-layout>