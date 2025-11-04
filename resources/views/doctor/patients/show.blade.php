<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">{{ __('Patient Overview') }}</p>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ optional($patient->user)->name ?? __('Unknown Patient') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Review medical history, appointments, and billing for this patient.') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-action-button :href="route('doctor.appointments.create', ['patient' => $patient])" icon="plus">
                    {{ __('Schedule Appointment') }}
                </x-action-button>
                <x-action-button :href="route('doctor.medical-records.create', ['appointment' => $patient->appointments->first()?->id])"
                                 icon="edit"
                                 :disabled="$patient->appointments->isEmpty()">
                    {{ __('New Medical Record') }}
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
            <x-app-card class="lg:col-span-2" :title="__('Personal Details')" :description="__('Key information provided during registration.')" >
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Full Name') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ optional($patient->user)->name ?? __('Not provided') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Email Address') }}</dt>
                        <dd class="mt-1 text-sm text-blue-600">
                            {{ optional($patient->user)->email ?? __('Not provided') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Phone Number') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $patient->phone ?? __('Not provided') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Emergency Contact') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $patient->emergency_contact ?? __('Not provided') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Date of Birth') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ optional($patient->date_of_birth)->format('d/m/Y') ?? __('Not provided') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Gender') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $patient->gender ? ucfirst($patient->gender) : __('Not provided') }}
                        </dd>
                    </div>
                </dl>

                <div class="mt-6">
                    <dt class="text-sm font-medium text-gray-500">{{ __('Residential Address') }}</dt>
                    <dd class="mt-2 whitespace-pre-line rounded-lg border border-dashed border-gray-200 bg-gray-50 p-4 text-sm text-gray-800">
                        {{ $patient->address ?? __('No address recorded') }}
                    </dd>
                </div>

                @if ($patient->medical_history)
                    <div class="mt-6">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Medical History Summary') }}</dt>
                        <dd class="mt-2 whitespace-pre-line rounded-lg border border-dashed border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                            {{ $patient->medical_history }}
                        </dd>
                    </div>
                @endif
            </x-app-card>

            <x-app-card :title="__('Engagement Snapshot')" :description="__('Recent activity with your clinic.')" class="h-full">
                <dl class="space-y-4">
                    <div class="flex items-center justify-between">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Total Visits') }}</dt>
                        <dd class="text-lg font-semibold text-gray-900">
                            {{ number_format($patient->appointments_count ?? $patient->appointments->count()) }}
                        </dd>
                    </div>

                    <div class="flex items-center justify-between">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Completed Treatments') }}</dt>
                        <dd class="text-lg font-semibold text-emerald-600">
                            {{ number_format($patient->medical_records_count ?? $patient->medicalRecords->count()) }}
                        </dd>
                    </div>

                    <div class="flex items-center justify-between">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Outstanding Balance') }}</dt>
                        @php
                            $outstanding = $patient->bills->where('status', 'unpaid')->sum('total_amount');
                        @endphp
                        <dd class="text-lg font-semibold {{ $outstanding > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ __('RM :amount', ['amount' => number_format($outstanding, 2)]) }}
                        </dd>
                    </div>

                    <div class="flex items-center justify-between">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Last Appointment') }}</dt>
                        @php
                            $lastAppointment = $patient->appointments->sortByDesc('appointment_date')->first();
                        @endphp
                        <dd class="text-sm text-gray-900">
                            {{ $lastAppointment?->appointment_date?->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') ?? __('Not yet visited') }}
                        </dd>
                    </div>
                </dl>
            </x-app-card>
        </div>

        <x-app-card :title="__('Upcoming & Recent Appointments')" :description="__('Chronological log of appointments with status indicators.')">
            <x-data-table
                :headers="[
                    __('Date & Time'),
                    __('Doctor'),
                    __('Status'),
                    __('Notes'),
                    __('Actions'),
                ]"
                :empty-message="__('No appointments have been recorded yet for this patient.')"
            >
                @foreach ($patient->appointments->sortByDesc('appointment_date') as $appointment)
                    <tr>
                        <td class="px-6 py-4 align-top text-sm text-gray-900">
                            {{ optional($appointment->appointment_date)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') }}
                        </td>
                        <td class="px-6 py-4 align-top text-sm text-gray-700">
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-900">
                                    {{ optional($appointment->doctor?->user)->name ?? __('Unknown Doctor') }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $appointment->doctor?->specialization }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 align-top">
                            <x-status-badge :status="$appointment->status" />
                        </td>
                        <td class="px-6 py-4 align-top text-sm text-gray-600">
                            {{ Str::limit($appointment->notes, 100) ?: __('No notes recorded') }}
                        </td>
                        <td class="px-6 py-4 align-top">
                            <div class="flex flex-wrap items-center gap-2">
                                <x-action-button :href="route('doctor.appointments.show', $appointment)" icon="view" variant="secondary">
                                    {{ __('View') }}
                                </x-action-button>
                                <x-action-button :href="route('doctor.appointments.edit', $appointment)" icon="edit">
                                    {{ __('Update') }}
                                </x-action-button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-data-table>
        </x-app-card>

        <div class="grid gap-6 lg:grid-cols-2">
            <x-app-card :title="__('Medical Records')" :description="__('Diagnoses, treatment plans, and prescriptions logged for this patient.')">
                <x-data-table
                    :headers="[
                        __('Recorded On'),
                        __('Doctor'),
                        __('Summary'),
                        __('Actions'),
                    ]"
                    :empty-message="__('No medical records are available yet.')"
                >
                    @foreach ($patient->medicalRecords as $record)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ optional($record->created_at)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ optional($record->appointment?->doctor?->user)->name ?? __('Unknown Doctor') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <p class="font-medium text-gray-900">{{ Str::limit($record->diagnosis, 80) }}</p>
                                <p class="text-xs text-gray-500">{{ Str::limit($record->treatment, 100) }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <x-action-button :href="route('doctor.medical-records.show', $record)" icon="view" variant="secondary">
                                        {{ __('View') }}
                                    </x-action-button>
                                    <x-action-button :href="route('doctor.medical-records.edit', $record)" icon="edit">
                                        {{ __('Edit') }}
                                    </x-action-button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-data-table>
            </x-app-card>

            <x-app-card :title="__('Billing Records')" :description="__('Invoices and payment statuses for services rendered.')" >
                <x-data-table
                    :headers="[
                        __('Invoice'),
                        __('Amount'),
                        __('Status'),
                        __('Due Date'),
                        __('Actions'),
                    ]"
                    :empty-message="__('No billing entries have been issued yet.')"
                >
                    @foreach ($patient->bills as $bill)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ __('BILL-:id', ['id' => str_pad($bill->id, 5, '0', STR_PAD_LEFT)]) }}
                                <span class="block text-xs text-gray-500">
                                    {{ optional($bill->created_at)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                {{ __('RM :amount', ['amount' => number_format($bill->total_amount, 2)]) }}
                            </td>
                            <td class="px-6 py-4">
                                <x-status-badge :status="$bill->status" />
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ optional($bill->due_date)->format('d/m/Y') ?? __('Not set') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <x-action-button :href="route('doctor.bills.show', $bill)" icon="view" variant="secondary">
                                        {{ __('Details') }}
                                    </x-action-button>
                                    <x-action-button :href="route('doctor.bills.edit', $bill)" icon="edit">
                                        {{ __('Update') }}
                                    </x-action-button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-data-table>
            </x-app-card>
        </div>
    </div>
</x-app-layout>