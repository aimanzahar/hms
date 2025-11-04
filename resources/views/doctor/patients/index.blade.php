<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('Patient Directory') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Browse and manage your patients with quick access to their latest activity.') }}
                </p>
            </div>

            <x-action-button :href="route('doctor.appointments.index')" icon="arrow-right">
                {{ __('Go to Appointments') }}
            </x-action-button>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-app-card :title="__('Patient List')" :description="__('Track contact information, visits, medical records, and billing status for your patients.')">
            <div class="flex flex-col gap-4">
                <x-search-bar
                    :action="route('doctor.patients.index')"
                    :query="request('search')"
                    placeholder="{{ __('Search by patient name or email') }}"
                />

                <x-data-table
                    :headers="[
                        __('Patient'),
                        __('Contact'),
                        __('Engagement'),
                        __('Last Visit'),
                        __('Actions'),
                    ]"
                    :paginate="$patients->onEachSide(1)->links()"
                    :empty-message="__('No patients matched your filters yet.')"
                >
                    @foreach ($patients as $patient)
                        <tr class="bg-white">
                            <td class="px-6 py-4 align-top">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-semibold text-gray-900">
                                        {{ optional($patient->user)->name }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ __('Patient ID: :id', ['id' => str_pad($patient->id, 5, '0', STR_PAD_LEFT)]) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="flex flex-col gap-1 text-sm text-gray-600">
                                    <span>{{ optional($patient->user)->email }}</span>
                                    @if($patient->phone)
                                        <span>{{ __('Phone: :phone', ['phone' => $patient->phone]) }}</span>
                                    @endif
                                    @if($patient->emergency_contact)
                                        <span>{{ __('Emergency: :contact', ['contact' => $patient->emergency_contact]) }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <dl class="text-sm text-gray-700 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <dt>{{ __('Appointments') }}</dt>
                                        <dd class="font-semibold">{{ number_format($patient->appointments_count) }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt>{{ __('Medical Records') }}</dt>
                                        <dd class="font-semibold">{{ number_format($patient->medical_records_count) }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt>{{ __('Bills') }}</dt>
                                        <dd class="font-semibold">{{ number_format($patient->bills_count) }}</dd>
                                    </div>
                                </dl>
                            </td>
                            <td class="px-6 py-4 align-top">
                                @php
                                    $latestAppointment = $patient->appointments()->latest('appointment_date')->first();
                                @endphp
                                <div class="text-sm text-gray-600">
                                    @if($latestAppointment?->appointment_date)
                                        <p>{{ $latestAppointment->appointment_date->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') }}</p>
                                        <p class="text-xs text-gray-400">
                                            {{ __('Status: :status', ['status' => ucfirst($latestAppointment->status)]) }}
                                        </p>
                                    @else
                                        <p>{{ __('No visits recorded yet') }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="flex flex-wrap items-center gap-2">
                                    <x-action-button :href="route('doctor.patients.show', $patient)" icon="view" variant="secondary">
                                        {{ __('View') }}
                                    </x-action-button>
                                    <x-action-button :href="route('doctor.appointments.create', ['patient' => $patient])" icon="plus">
                                        {{ __('New Appointment') }}
                                    </x-action-button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-data-table>
            </div>
        </x-app-card>
    </div>
</x-app-layout>