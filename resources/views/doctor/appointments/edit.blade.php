<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">{{ __('Update Appointment') }}</p>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('Manage Appointment with :patient', ['patient' => optional($appointment->patient?->user)->name ?? __('Unknown Patient')]) }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Use the form below to confirm, complete, or cancel this booking while leaving internal notes for your team.') }}
                </p>
            </div>

            <x-action-button :href="route('doctor.appointments.show', $appointment)" icon="view" variant="secondary">
                {{ __('Back to Details') }}
            </x-action-button>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if ($errors->any())
            <x-alert type="error" :title="__('Please review the form')">
                {{ __('Some fields require your attention before we can update this appointment.') }}
            </x-alert>
        @endif

        @if (session('status'))
            <x-alert type="success" :title="__('Success')">
                {{ session('status') }}
            </x-alert>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <x-app-card class="lg:col-span-2" :title="__('Appointment Summary')" :description="__('Quick overview of booking context.')" >
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Scheduled Date & Time') }}</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            {{ optional($appointment->appointment_date)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') ?? __('Not scheduled') }}
                        </dd>
                        <dd class="text-xs text-gray-500">
                            {{ optional($appointment->appointment_date)->diffForHumans() }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Current Status') }}</dt>
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
            </x-app-card>

            <x-app-card :title="__('Follow-up Options')" :description="__('Complete downstream actions once status changes.')" class="h-full">
                <ul class="space-y-4 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                        <div>
                            <p class="font-medium text-gray-900">{{ __('Create Medical Record') }}</p>
                            <p class="text-xs text-gray-500">
                                {{ __('Once completed, record diagnosis, treatment, and prescription details.') }}
                            </p>
                        </div>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1 h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                        <div>
                            <p class="font-medium text-gray-900">{{ __('Generate Bill') }}</p>
                            <p class="text-xs text-gray-500">
                                {{ __('Issue an invoice for consultation fees and procedures after completion.') }}
                            </p>
                        </div>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1 h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                        <div>
                            <p class="font-medium text-gray-900">{{ __('Reschedule If Needed') }}</p>
                            <p class="text-xs text-gray-500">
                                {{ __('Change the appointment date/time inside your calendar system if requested.') }}
                            </p>
                        </div>
                    </li>
                </ul>
            </x-app-card>
        </div>

        <x-app-card :title="__('Update Appointment Status')" :description="__('Confirm, complete, or cancel this booking while leaving internal notes for your team.')" >
            <form method="POST" action="{{ route('doctor.appointments.update', $appointment) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <x-input-label for="status" :value="__('Status *')" />
                        <select
                            id="status"
                            name="status"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400"
                            required
                        >
                            @foreach(['pending', 'confirmed', 'completed', 'cancelled'] as $statusOption)
                                <option value="{{ $statusOption }}" @selected(old('status', $appointment->status) === $statusOption)>
                                    {{ ucfirst($statusOption) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="appointment_date" :value="__('Appointment Date & Time (MYT)')" />
                        <input
                            type="datetime-local"
                            id="appointment_date"
                            name="appointment_date"
                            value="{{ old('appointment_date', optional($appointment->appointment_date)->timezone('Asia/Kuala_Lumpur')->format('Y-m-d\TH:i')) }}"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400"
                            required
                        />
                        <p class="mt-2 text-xs text-gray-500">
                            {{ __('Only adjust when rescheduling. All times should follow Malaysia Time (MYT).') }}
                        </p>
                        <x-input-error :messages="$errors->get('appointment_date')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="notes" :value="__('Doctor Notes')" />
                    <textarea
                        id="notes"
                        name="notes"
                        rows="5"
                        class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="{{ __('Add internal notes or patient instructions here...') }}"
                    >{{ old('notes', $appointment->notes) }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end gap-3">
                    <x-secondary-button :href="route('doctor.appointments.show', $appointment)">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-primary-button>
                        {{ __('Save Changes') }}
                    </x-primary-button>
                </div>
            </form>
        </x-app-card>
    </div>
</x-app-layout>