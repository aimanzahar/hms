<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('Doctor Profile') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Manage your professional information and review upcoming engagements.') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-action-button :href="route('doctor.appointments.index')" icon="arrow-right" variant="secondary">
                    {{ __('View Appointments') }}
                </x-action-button>
                <x-action-button :href="route('doctor.medical-records.index')" icon="arrow-right">
                    {{ __('Medical Records') }}
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

        @if ($errors->any())
            <x-alert type="error" :title="__('Please review the form')">
                {{ __('Some fields require your attention before we can save your profile.') }}
            </x-alert>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <x-app-card class="lg:col-span-2" :title="__('Professional Summary')" :description="__('This information is visible to patients when they browse your profile.')">
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Name') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ optional($doctor?->user)->name ?? __('Not provided') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Email') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ optional($doctor?->user)->email ?? __('Not provided') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Specialisation') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $doctor?->specialization ?? __('Not provided') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('MMC Licence Number') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $doctor?->license_number ?? __('Not provided') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Experience (years)') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ number_format($doctor?->experience_years ?? 0) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Consultation Fee') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $doctor ? __('RM :amount',['amount'=>number_format($doctor->consultation_fee,2)]) : __('Not provided') }}
                        </dd>
                    </div>
                </dl>
            </x-app-card>

            <x-app-card :title="__('Quick Stats')" :description="__('Snapshot of your current workload.')" class="h-full">
                <dl class="space-y-4">
                    <div class="flex items-center justify-between">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Total Appointments') }}</dt>
                        <dd class="text-lg font-semibold text-gray-900">
                            {{ number_format($statistics['total_appointments'] ?? 0) }}
                        </dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Upcoming Today onwards') }}</dt>
                        <dd class="text-lg font-semibold text-blue-600">
                            {{ number_format($statistics['upcoming_appointments'] ?? 0) }}
                        </dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Unique Patients') }}</dt>
                        <dd class="text-lg font-semibold text-emerald-600">
                            {{ number_format($statistics['unique_patients'] ?? 0) }}
                        </dd>
                    </div>
                </dl>
            </x-app-card>
        </div>

        <x-app-card :title="__('Update Profile')" :description="__('Keep your credentials and fees up to date to ensure accurate patient information.')" id="doctor-profile-form">
            <form method="POST" action="{{ route('doctor.profile.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <x-input-label for="specialization" :value="__('Specialisation *')" />
                        <x-text-input
                            id="specialization"
                            name="specialization"
                            type="text"
                            class="mt-1 block w-full"
                            :value="old('specialization', $doctor?->specialization)"
                            required
                            autocomplete="organization-title"
                        />
                        <x-input-error :messages="$errors->get('specialization')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="license_number" :value="__('MMC Licence Number *')" />
                        <x-text-input
                            id="license_number"
                            name="license_number"
                            type="text"
                            class="mt-1 block w-full uppercase tracking-wide"
                            :value="old('license_number', $doctor?->license_number)"
                            required
                            autocomplete="off"
                        />
                        <x-input-error :messages="$errors->get('license_number')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="experience_years" :value="__('Experience (Years) *')" />
                        <x-text-input
                            id="experience_years"
                            name="experience_years"
                            type="number"
                            min="0"
                            class="mt-1 block w-full"
                            :value="old('experience_years', $doctor?->experience_years)"
                            required
                        />
                        <x-input-error :messages="$errors->get('experience_years')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="consultation_fee" :value="__('Consultation Fee (RM) *')" />
                        <x-text-input
                            id="consultation_fee"
                            name="consultation_fee"
                            type="number"
                            step="0.01"
                            min="0"
                            class="mt-1 block w-full"
                            :value="old('consultation_fee', $doctor?->consultation_fee)"
                            required
                        />
                        <x-input-error :messages="$errors->get('consultation_fee')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2">
                    <x-secondary-button type="reset">
                        {{ __('Reset') }}
                    </x-secondary-button>
                    <x-primary-button>
                        {{ __('Save changes') }}
                    </x-primary-button>
                </div>
            </form>
        </x-app-card>

        <x-app-card :title="__('Upcoming Appointments')" :description="__('All times are displayed in Malaysia Time (MYT).')">
            <x-search-bar
                :action="route('doctor.appointments.index')"
                :query="request('search')"
                placeholder="{{ __('Search by patient name, status or notes') }}"
            />

            <div class="mt-4">
                <x-data-table
                    :headers="[
                        __('Date & Time'),
                        __('Patient'),
                        __('Status'),
                        __('Notes'),
                        __('Actions'),
                    ]"
                    :empty-message="__('No upcoming appointments at the moment.')"
                >
                    @forelse($doctor?->appointments ?? [] as $appointment)
                        <tr class="bg-white">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ optional($appointment->appointment_date)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ optional($appointment->patient?->user)->name ?? __('Unknown patient') }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ optional($appointment->patient?->user)->email }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <x-status-badge :status="$appointment->status" />
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ Str::limit($appointment->notes, 80) ?: __('No notes recorded') }}
                            </td>
                            <td class="px-6 py-4">
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
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                {{ __('You have no upcoming appointments today. Enjoy a well-deserved break!') }}
                            </td>
                        </tr>
                    @endforelse
                </x-data-table>
            </div>
        </x-app-card>
    </div>
</x-app-layout>