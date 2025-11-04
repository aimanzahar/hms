<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">{{ __('Appointment Details') }}</p>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('Session with :patient', ['patient' => optional($appointment->patient?->user)->name ?? __('Unknown Patient')]) }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Review booking information, manage the appointment status, and access related medical records or billing entries.') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-action-button :href="route('doctor.appointments.index')" icon="arrow-right" variant="secondary">
                    {{ __('Back to Appointments') }}
                </x-action-button>
                <x-action-button :href="route('doctor.appointments.edit', $appointment)" icon="edit">
                    {{ __('Update Status') }}
                </x-action-button>
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
            <x-app-card class="lg:col-span-2" :title="__('Appointment Overview')" :description="__('Key timing and patient contact information.')" >
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
                        <dt class="text-sm font-medium text-gray-500">{{ __('Patient') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ optional($appointment->patient?->user)->name ?? __('Unknown') }}
                        </dd>
                        <dd class="text-xs text-gray-500">
                            {{ optional($appointment->patient?->user)->email }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Patient Contact') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $appointment->patient?->phone ?? __('Not provided') }}
                        </dd>
                        <dd class="text-xs text-gray-500">
                            {{ __('Emergency: :contact', ['contact' => $appointment->patient?->emergency_contact ?? __('Not provided')]) }}
                        </dd>
                    </div>
                </dl>

                <div class="mt-6">
                    <dt class="text-sm font-medium text-gray-500">{{ __('Doctor Notes') }}</dt>
                    <dd class="mt-2 whitespace-pre-line rounded-lg border border-dashed border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
                        {{ $appointment->notes ?? __('No notes were recorded for this appointment.') }}
                    </dd>
                </div>
            </x-app-card>

            <x-app-card :title="__('Actions')" :description="__('Complete your workflow for this session.')" class="h-full">
                <div class="space-y-4">
                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                        <p class="text-sm font-medium text-gray-900">{{ __('Update Status') }}</p>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ __('Mark the appointment as confirmed, completed, or cancelled.') }}
                        </p>
                        <x-action-button :href="route('doctor.appointments.edit', $appointment)" icon="edit" size="sm" class="mt-3">
                            {{ __('Update Appointment') }}
                        </x-action-button>
                    </div>

                    <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
                        <p class="text-sm font-medium text-emerald-900">{{ __('Create Medical Record') }}</p>
                        <p class="mt-1 text-xs text-emerald-700">
                            {{ __('Document diagnosis, treatment, prescription, and follow-up notes once consultation is completed.') }}
                        </p>
                        <x-action-button :href="route('doctor.medical-records.create', ['appointment' => $appointment])" icon="plus" size="sm" variant="success" class="mt-3">
                            {{ __('Add Medical Record') }}
                        </x-action-button>
                    </div>

                    <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 shadow-sm">
                        <p class="text-sm font-medium text-amber-900">{{ __('Generate Bill') }}</p>
                        <p class="mt-1 text-xs text-amber-700">
                            {{ __('Prepare an invoice for this visit, including consultation fees and procedures.') }}
                        </p>
                        <x-action-button :href="route('doctor.bills.create', ['appointment' => $appointment])" icon="plus" size="sm" class="mt-3">
                            {{ __('Issue Bill') }}
                        </x-action-button>
                    </div>

                    <div class="rounded-lg border border-rose-200 bg-rose-50 p-4 shadow-sm">
                        <p class="text-sm font-medium text-rose-900">{{ __('Cancel Appointment') }}</p>
                        <p class="mt-1 text-xs text-rose-700">
                            {{ __('Cancel when the patient requests or circumstances prevent the consultation.') }}
                        </p>
                        <form method="POST" action="{{ route('doctor.appointments.destroy', $appointment) }}" class="mt-3" onsubmit="return confirm('{{ __('Are you sure you want to cancel this appointment? This action cannot be undone.') }}');">
                            @csrf
                            @method('DELETE')
                            <x-action-button as="button" type="submit" icon="trash" variant="danger" size="sm">
                                {{ __('Cancel Appointment') }}
                            </x-action-button>
                        </form>
                    </div>
                </div>
            </x-app-card>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <x-app-card :title="__('Linked Medical Record')" :description="__('Review detailed documentation related to this appointment.')">
                @if($appointment->medicalRecord)
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Diagnosis') }}</dt>
                            <dd class="mt-2 whitespace-pre-line rounded-lg border border-dashed border-gray-200 bg-gray-50 p-4 text-sm text-gray-800">
                                {{ $appointment->medicalRecord->diagnosis }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Treatment Plan') }}</dt>
                            <dd class="mt-2 whitespace-pre-line rounded-lg border border-dashed border-gray-200 bg-gray-50 p-4 text-sm text-gray-800">
                                {{ $appointment->medicalRecord->treatment }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Prescription') }}</dt>
                            <dd class="mt-2 whitespace-pre-line rounded-lg border border-dashed border-gray-200 bg-gray-50 p-4 text-sm text-gray-800">
                                {{ $appointment->medicalRecord->prescription ?? __('No prescription recorded') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Additional Notes') }}</dt>
                            <dd class="mt-2 whitespace-pre-line rounded-lg border border-dashed border-gray-200 bg-gray-50 p-4 text-sm text-gray-800">
                                {{ $appointment->medicalRecord->notes ?? __('No additional notes recorded.') }}
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-6 flex items-center gap-3">
                        <x-action-button :href="route('doctor.medical-records.show', $appointment->medicalRecord)" icon="view" variant="secondary">
                            {{ __('View Record') }}
                        </x-action-button>
                        <x-action-button :href="route('doctor.medical-records.edit', $appointment->medicalRecord)" icon="edit">
                            {{ __('Edit Record') }}
                        </x-action-button>
                    </div>
                @else
                    <p class="text-sm text-gray-600">
                        {{ __('No medical record has been created for this appointment yet.') }}
                    </p>
                    <x-action-button :href="route('doctor.medical-records.create', ['appointment' => $appointment])" icon="plus" class="mt-4">
                        {{ __('Create Medical Record') }}
                    </x-action-button>
                @endif
            </x-app-card>

            <x-app-card :title="__('Billing Information')" :description="__('Invoices generated from this appointment.')">
                @if($appointment->bill)
                    <dl class="space-y-4">
                        <div class="flex items-center justify-between">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Invoice Number') }}</dt>
                            <dd class="text-sm font-semibold text-gray-900">
                                {{ __('BILL-:id', ['id' => str_pad($appointment->bill->id, 5, '0', STR_PAD_LEFT)]) }}
                            </dd>
                        </div>

                        <div class="flex items-center justify-between">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Total Amount') }}</dt>
                            <dd class="text-lg font-semibold text-gray-900">
                                {{ __('RM :amount', ['amount' => number_format($appointment->bill->total_amount, 2)]) }}
                            </dd>
                        </div>

                        <div class="flex items-center justify-between">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                            <dd>
                                <x-status-badge :status="$appointment->bill->status" />
                            </dd>
                        </div>

                        <div class="flex items-center justify-between">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Due Date') }}</dt>
                            <dd class="text-sm text-gray-900">
                                {{ optional($appointment->bill->due_date)->format('d/m/Y') ?? __('Not set') }}
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-6 flex items-center gap-3">
                        <x-action-button :href="route('doctor.bills.show', $appointment->bill)" icon="view" variant="secondary">
                            {{ __('View Bill') }}
                        </x-action-button>
                        <x-action-button :href="route('doctor.bills.edit', $appointment->bill)" icon="edit">
                            {{ __('Edit Bill') }}
                        </x-action-button>
                    </div>
                @else
                    <p class="text-sm text-gray-600">
                        {{ __('No bill has been generated for this appointment yet.') }}
                    </p>
                    <x-action-button :href="route('doctor.bills.create', ['appointment' => $appointment])" icon="plus" class="mt-4">
                        {{ __('Generate Bill') }}
                    </x-action-button>
                @endif
            </x-app-card>
        </div>
    </div>
</x-app-layout>