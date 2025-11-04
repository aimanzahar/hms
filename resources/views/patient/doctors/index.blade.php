<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">{{ __('Find Doctors') }}</p>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('Browse Healthcare Professionals') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Find and connect with qualified doctors for your healthcare needs.') }}
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
        <x-app-card :title="__('Search Doctors')" :description="__('Filter by specialization to find the right healthcare professional.')">
            <form method="GET" action="{{ route('patient.doctors.index') }}" class="flex flex-col gap-4 sm:flex-row">
                <div class="flex-1">
                    <x-search-bar
                        :action="route('patient.doctors.index')"
                        :query="request('search')"
                        placeholder="{{ __('Search by doctor name or specialization') }}"
                        class="w-full"
                    >
                        <input type="hidden" name="specialization" value="{{ request('specialization') }}">
                    </x-search-bar>
                </div>

                <div class="sm:w-64">
                    <select name="specialization" onchange="this.form.submit()" class="block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">{{ __('All Specializations') }}</option>
                        <option value="Pakar Perubatan Am" @selected(request('specialization') === 'Pakar Perubatan Am')>{{ __('General Medicine') }}</option>
                        <option value="Pakar Pediatrik" @selected(request('specialization') === 'Pakar Pediatrik')>{{ __('Pediatrics') }}</option>
                        <option value="Pakar Kardiologi" @selected(request('specialization') === 'Pakar Kardiologi')>{{ __('Cardiology') }}</option>
                        <option value="Pakar Bedah" @selected(request('specialization') === 'Pakar Bedah')>{{ __('Surgery') }}</option>
                        <option value="Pakar Obstetrik & Ginekologi" @selected(request('specialization') === 'Pakar Obstetrik & Ginekologi')>{{ __('Obstetrics & Gynecology') }}</option>
                        <option value="Pakar Ortopedik" @selected(request('specialization') === 'Pakar Ortopedik')>{{ __('Orthopedics') }}</option>
                        <option value="Pakar Dermatologi" @selected(request('specialization') === 'Pakar Dermatologi')>{{ __('Dermatology') }}</option>
                        <option value="Pakar Oftalmologi" @selected(request('specialization') === 'Pakar Oftalmologi')>{{ __('Ophthalmology') }}</option>
                        <option value="Pakar Psikiatri" @selected(request('specialization') === 'Pakar Psikiatri')>{{ __('Psychiatry') }}</option>
                    </select>
                </div>
            </form>
        </x-app-card>

        <!-- Doctors Grid -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($doctors as $doctor)
                <x-app-card class="hover:shadow-lg transition-shadow duration-200">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ optional($doctor->user)->name ?? __('Dr. Unknown') }}
                            </h3>

                            <p class="text-sm text-blue-600 font-medium">
                                {{ $doctor->specialization ?? __('Specialization not specified') }}
                            </p>

                            <div class="mt-2 space-y-1">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ __('MMC :license', ['license' => $doctor->license_number ?? 'Not provided']) }}
                                </div>

                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ __(':years years experience', ['years' => $doctor->experience_years ?? 0]) }}
                                </div>

                                <div class="flex items-center text-sm font-semibold text-green-600">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    {{ __('RM :fee', ['fee' => number_format($doctor->consultation_fee ?? 0, 2)]) }}
                                </div>
                            </div>

                            <div class="mt-4 flex gap-2">
                                <x-action-button :href="route('patient.doctors.show', $doctor)" icon="view" variant="secondary" size="sm">
                                    {{ __('View Profile') }}
                                </x-action-button>
                                <x-action-button :href="route('patient.appointments.create', ['doctor' => $doctor])" icon="plus" size="sm">
                                    {{ __('Book Appointment') }}
                                </x-action-button>
                            </div>
                        </div>
                    </div>
                </x-app-card>
            @empty
                <div class="col-span-full">
                    <x-app-card>
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No doctors found') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ __('Try adjusting your search criteria or browse all specializations.') }}
                            </p>
                            <div class="mt-6">
                                <x-action-button :href="route('patient.doctors.index')" icon="refresh" variant="secondary">
                                    {{ __('Clear Filters') }}
                                </x-action-button>
                            </div>
                        </div>
                    </x-app-card>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($doctors instanceof \Illuminate\Contracts\Pagination\Paginator && $doctors->hasPages())
            <div class="flex justify-center">
                {{ $doctors->onEachSide(1)->links() }}
            </div>
        @endif
    </div>
</x-app-layout>