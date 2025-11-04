<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">{{ __('Doctor Profile') }}</p>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ optional($doctor->user)->name ?? __('Dr. Unknown') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Learn more about this healthcare professional and their services.') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-action-button :href="route('patient.doctors.index')" icon="arrow-right" variant="secondary">
                    {{ __('Back to Doctors') }}
                </x-action-button>
                <x-action-button :href="route('patient.appointments.create', ['doctor' => $doctor])" icon="plus">
                    {{ __('Book Appointment') }}
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
            <!-- Doctor Information -->
            <x-app-card class="lg:col-span-2" :title="__('Professional Details')" :description="__('Qualifications and experience information.')">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Full Name') }}</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            {{ optional($doctor->user)->name ?? __('Not provided') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Specialization') }}</dt>
                        <dd class="mt-1 text-sm text-blue-600 font-medium">
                            {{ $doctor->specialization ?? __('Not specified') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('MMC License Number') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $doctor->license_number ?? __('Not provided') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Years of Experience') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ number_format($doctor->experience_years ?? 0) }} {{ __('years') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Consultation Fee') }}</dt>
                        <dd class="mt-1 text-lg font-semibold text-green-600">
                            {{ __('RM :fee', ['fee' => number_format($doctor->consultation_fee ?? 0, 2)]) }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Email') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ optional($doctor->user)->email ?? __('Not provided') }}
                        </dd>
                    </div>
                </div>

                @if($doctor->bio)
                    <div class="mt-6">
                        <dt class="text-sm font-medium text-gray-500">{{ __('About') }}</dt>
                        <dd class="mt-2 text-sm text-gray-700 leading-relaxed">
                            {{ $doctor->bio }}
                        </dd>
                    </div>
                @endif
            </x-app-card>

            <!-- Quick Actions -->
            <x-app-card :title="__('Book Appointment')" :description="__('Schedule a consultation with this doctor.')">
                <div class="space-y-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">
                            {{ __('RM :fee', ['fee' => number_format($doctor->consultation_fee ?? 0, 2)]) }}
                        </div>
                        <div class="text-sm text-gray-500">{{ __('per consultation') }}</div>
                    </div>

                    <x-action-button :href="route('patient.appointments.create', ['doctor' => $doctor])" icon="plus" class="w-full justify-center">
                        {{ __('Schedule Appointment') }}
                    </x-action-button>

                    <div class="text-xs text-gray-500 text-center">
                        {{ __('Appointments are subject to doctor availability') }}
                    </div>
                </div>
            </x-app-card>
        </div>

        <!-- Recent Appointments (if any) -->
        @if(isset($recentAppointments) && $recentAppointments->count() > 0)
            <x-app-card :title="__('Recent Patient Reviews')" :description="__('Feedback from recent consultations.')">
                <div class="space-y-4">
                    @foreach($recentAppointments->take(3) as $appointment)
                        @if($appointment->medicalRecord)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ optional($appointment->patient?->user)->name ?? __('Anonymous') }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ optional($appointment->appointment_date)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y') }}
                                            </div>
                                        </div>
                                        <div class="mt-1 text-sm text-gray-600">
                                            {{ Str::limit($appointment->medicalRecord->diagnosis ?? '', 100) }}
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= 4 ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </x-app-card>
        @endif

        <!-- Education & Certifications -->
        @if($doctor->education || $doctor->certifications)
            <x-app-card :title="__('Education & Certifications')" :description="__('Professional qualifications and training.')">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    @if($doctor->education)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Education') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">
                                {{ $doctor->education }}
                            </dd>
                        </div>
                    @endif

                    @if($doctor->certifications)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Certifications') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">
                                {{ $doctor->certifications }}
                            </dd>
                        </div>
                    @endif
                </div>
            </x-app-card>
        @endif

        <!-- Contact Information -->
        <x-app-card :title="__('Contact Information')" :description="__('How to reach this doctor for appointments.')">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Email') }}</dt>
                    <dd class="mt-1 text-sm text-blue-600">
                        {{ optional($doctor->user)->email ?? __('Not provided') }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Phone') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ __('Contact through appointment booking') }}
                    </dd>
                </div>
            </div>

            <div class="mt-6">
                <x-action-button :href="route('patient.appointments.create', ['doctor' => $doctor])" icon="plus" variant="secondary">
                    {{ __('Book Appointment Now') }}
                </x-action-button>
            </div>
        </x-app-card>
    </div>
</x-app-layout>