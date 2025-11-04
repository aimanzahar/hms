<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">{{ __('Medical Records') }}</p>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('Clinical Documentation Library') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Access, review, and maintain patient diagnoses, treatment notes, and prescriptions from a single view.') }}
                </p>
            </div>

            <x-action-button :href="route('doctor.medical-records.create')" icon="plus">
                {{ __('Create Medical Record') }}
            </x-action-button>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-app-card :title="__('Medical Record Archive')" :description="__('Filter by patient, appointment, or keywords to locate past consultations.')" >
            <div class="flex flex-col gap-4">
                <x-search-bar
                    :action="route('doctor.medical-records.index')"
                    :query="request('search')"
                    placeholder="{{ __('Search by patient, diagnosis, or notes') }}"
                >
                    <select name="patient_id" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">{{ __('All Patients') }}</option>
                        @foreach($patients ?? [] as $patientOption)
                            <option value="{{ $patientOption->id }}" @selected((string)request('patient_id') === (string)$patientOption->id)>
                                {{ optional($patientOption->user)->name }}
                            </option>
                        @endforeach
                    </select>
                </x-search-bar>

                <x-data-table
                    :headers="[
                        __('Recorded On'),
                        __('Patient'),
                        __('Doctor'),
                        __('Diagnosis'),
                        __('Actions'),
                    ]"
                    :paginate="$medicalRecords instanceof \Illuminate\Contracts\Pagination\Paginator ? $medicalRecords->onEachSide(1)->links() : null"
                    :empty-message="__('No medical records found for the selected filters.')"
                >
                    @foreach ($medicalRecords as $record)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ optional($record->created_at)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') }}
                                <span class="block text-xs text-gray-500">
                                    {{ optional($record->appointment?->appointment_date)->diffForHumans() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div class="flex flex-col">
                                    <span class="font-medium text-gray-900">
                                        {{ optional($record->appointment?->patient?->user)->name ?? __('Unknown Patient') }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ optional($record->appointment?->patient?->user)->email }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div class="flex flex-col">
                                    <span class="font-medium text-gray-900">
                                        {{ optional($record->appointment?->doctor?->user)->name ?? __('Unknown Doctor') }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $record->appointment?->doctor?->specialization }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ Str::limit($record->diagnosis, 100) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <x-action-button :href="route('doctor.medical-records.show', $record)" icon="view" variant="secondary">
                                        {{ __('View') }}
                                    </x-action-button>
                                    <x-action-button :href="route('doctor.medical-records.edit', $record)" icon="edit">
                                        {{ __('Edit') }}
                                    </x-action-button>
                                    <form method="POST" action="{{ route('doctor.medical-records.destroy', $record) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this medical record?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <x-action-button as="button" type="submit" icon="trash" variant="danger">
                                            {{ __('Delete') }}
                                        </x-action-button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-data-table>
            </div>
        </x-app-card>
    </div>
</x-app-layout>