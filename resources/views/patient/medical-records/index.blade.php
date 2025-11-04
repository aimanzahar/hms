<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">{{ __('Medical Records') }}</p>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('My Health History') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Access your complete medical history, diagnoses, and treatment records from all consultations.') }}
                </p>
            </div>

            <x-action-button :href="route('patient.appointments.index')" icon="arrow-right" variant="secondary">
                {{ __('My Appointments') }}
            </x-action-button>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <x-alert type="success" :title="__('Success')">
                {{ session('status') }}
            </x-alert>
        @endif

        <!-- Search and Filter -->
        <x-app-card :title="__('Search Medical Records')" :description="__('Find specific consultations by date, doctor, or keywords.')">
            <x-search-bar
                :action="route('patient.medical-records.index')"
                :query="request('search')"
                placeholder="{{ __('Search by diagnosis, doctor name, or treatment') }}"
            >
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end">
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700">{{ __('From Date') }}</label>
                        <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700">{{ __('To Date') }}</label>
                        <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                </div>
            </x-search-bar>
        </x-app-card>

        <!-- Medical Records List -->
        <x-app-card :title="__('Medical History')" :description="__('Chronological list of your medical consultations and treatments.')">
            <x-data-table
                :headers="[
                    __('Date'),
                    __('Doctor'),
                    __('Diagnosis'),
                    __('Treatment'),
                    __('Actions'),
                ]"
                :paginate="$medicalRecords instanceof \Illuminate\Contracts\Pagination\Paginator ? $medicalRecords->onEachSide(1)->links() : null"
                :empty-message="__('No medical records found. Your records will appear here after completed consultations.')"
            >
                @foreach ($medicalRecords as $record)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="flex flex-col">
                                <span class="font-medium">
                                    {{ optional($record->appointment->appointment_date)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y') }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ optional($record->appointment->appointment_date)->format('g:i A') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-900">
                                    {{ optional($record->appointment->doctor?->user)->name ?? __('Unknown Doctor') }}
                                </span>
                                <span class="text-xs text-blue-600">
                                    {{ $record->appointment->doctor?->specialization ?? __('Not specified') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="max-w-xs">
                                {{ Str::limit($record->diagnosis, 60) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <div class="max-w-xs">
                                {{ Str::limit($record->treatment ?? '', 60) ?: __('No treatment specified') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <x-action-button :href="route('patient.medical-records.show', $record)" icon="view" variant="secondary">
                                {{ __('View Details') }}
                            </x-action-button>
                        </td>
                    </tr>
                @endforeach
            </x-data-table>
        </x-app-card>

        <!-- Health Summary -->
        <x-app-card :title="__('Health Summary')" :description="__('Overview of your medical history and current health status.')">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">
                        {{ $stats['total_records'] ?? 0 }}
                    </div>
                    <div class="text-sm text-gray-500">{{ __('Total Records') }}</div>
                </div>

                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">
                        {{ $stats['this_year'] ?? 0 }}
                    </div>
                    <div class="text-sm text-gray-500">{{ __('This Year') }}</div>
                </div>

                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">
                        {{ $stats['unique_doctors'] ?? 0 }}
                    </div>
                    <div class="text-sm text-gray-500">{{ __('Doctors Consulted') }}</div>
                </div>
            </div>

            @if($stats['total_records'] > 0)
                <div class="mt-6">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">{{ __('Recent Activity') }}</h4>
                    <div class="space-y-3">
                        @foreach($medicalRecords->take(3) as $record)
                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ Str::limit($record->diagnosis, 80) }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ optional($record->appointment->appointment_date)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y') }}
                                        • {{ optional($record->appointment->doctor?->user)->name }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </x-app-card>

        <!-- Health Tips -->
        <x-app-card :title="__('Health Tips')" :description="__('General advice for maintaining your health records.')">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">{{ __('Keep Records Updated') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('Regular health check-ups help maintain comprehensive medical records.') }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">{{ __('Share with Care') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('Only share medical information with authorized healthcare providers.') }}</p>
                    </div>
                </div>
            </div>
        </x-app-card>
    </div>
</x-app-layout>