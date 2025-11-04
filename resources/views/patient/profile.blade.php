<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">{{ __('Patient Profile') }}</p>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('My Health Information') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Manage your personal details, medical history, and healthcare preferences.') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-action-button :href="route('patient.appointments.index')" icon="arrow-right" variant="secondary">
                    {{ __('My Appointments') }}
                </x-action-button>
                <x-action-button :href="route('patient.medical-records.index')" icon="arrow-right">
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
            <!-- Personal Details Card -->
            <x-app-card class="lg:col-span-2" :title="__('Personal Details')" :description="__('Your basic information and contact details.')">
                <form method="POST" action="{{ route('patient.profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <x-input-label for="name" value="{{ __('Full Name') }}" />
                            <x-text-input
                                id="name"
                                name="name"
                                type="text"
                                class="mt-1 block w-full"
                                :value="old('name', optional($patient->user)->name)"
                                required
                            />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" value="{{ __('Email Address') }}" />
                            <x-text-input
                                id="email"
                                name="email"
                                type="email"
                                class="mt-1 block w-full"
                                :value="old('email', optional($patient->user)->email)"
                                required
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="date_of_birth" value="{{ __('Date of Birth') }}" />
                            <x-text-input
                                id="date_of_birth"
                                name="date_of_birth"
                                type="date"
                                class="mt-1 block w-full"
                                :value="old('date_of_birth', optional($patient->date_of_birth)->format('Y-m-d'))"
                            />
                            <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="gender" value="{{ __('Gender') }}" />
                            <select id="gender" name="gender" class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <option value="">{{ __('Select gender') }}</option>
                                <option value="male" @selected(old('gender', $patient->gender) === 'male')>{{ __('Male') }}</option>
                                <option value="female" @selected(old('gender', $patient->gender) === 'female')>{{ __('Female') }}</option>
                                <option value="other" @selected(old('gender', $patient->gender) === 'other')>{{ __('Other') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="phone" value="{{ __('Phone Number') }}" />
                            <x-text-input
                                id="phone"
                                name="phone"
                                type="tel"
                                class="mt-1 block w-full"
                                :value="old('phone', $patient->phone)"
                                placeholder="e.g., +60123456789"
                            />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="emergency_contact" value="{{ __('Emergency Contact') }}" />
                            <x-text-input
                                id="emergency_contact"
                                name="emergency_contact"
                                type="tel"
                                class="mt-1 block w-full"
                                :value="old('emergency_contact', $patient->emergency_contact)"
                                placeholder="e.g., +60123456789"
                            />
                            <x-input-error :messages="$errors->get('emergency_contact')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-input-label for="address" value="{{ __('Address') }}" />
                        <textarea
                            id="address"
                            name="address"
                            rows="3"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="{{ __('Enter your full address') }}"
                        >{{ old('address', $patient->address) }}</textarea>
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-4">
                        <x-secondary-button type="reset">
                            {{ __('Reset') }}
                        </x-secondary-button>
                        <x-primary-button type="submit">
                            {{ __('Update Profile') }}
                        </x-primary-button>
                    </div>
                </form>
            </x-app-card>

            <!-- Statistics Card -->
            <x-app-card :title="__('Health Statistics')" :description="__('Your healthcare activity overview.')">
                <dl class="space-y-4">
                    <div class="flex items-center justify-between">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Upcoming Appointments') }}</dt>
                        <dd class="text-lg font-semibold text-blue-600">
                            {{ number_format($statistics['upcoming_appointments'] ?? 0) }}
                        </dd>
                    </div>

                    <div class="flex items-center justify-between">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Completed Appointments') }}</dt>
                        <dd class="text-lg font-semibold text-green-600">
                            {{ number_format($statistics['completed_appointments'] ?? 0) }}
                        </dd>
                    </div>

                    <div class="flex items-center justify-between">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Medical Records') }}</dt>
                        <dd class="text-lg font-semibold text-purple-600">
                            {{ number_format($statistics['medical_records_count'] ?? 0) }}
                        </dd>
                    </div>

                    <div class="flex items-center justify-between">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Outstanding Bills') }}</dt>
                        <dd class="text-lg font-semibold text-red-600">
                            {{ __('RM :amount', ['amount' => number_format($statistics['unpaid_bills'] ?? 0, 2)]) }}
                        </dd>
                    </div>
                </dl>
            </x-app-card>
        </div>

        <!-- Medical History -->
        <x-app-card :title="__('Medical History')" :description="__('Share your medical background, allergies, and important health information.')">
            <form method="POST" action="{{ route('patient.profile.update') }}">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="medical_history" value="{{ __('Medical History & Notes') }}" />
                    <textarea
                        id="medical_history"
                        name="medical_history"
                        rows="6"
                        class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="{{ __('Describe your medical history, chronic conditions, allergies, medications, and any other relevant health information...') }}"
                    >{{ old('medical_history', $patient->medical_history) }}</textarea>
                    <x-input-error :messages="$errors->get('medical_history')" class="mt-2" />
                </div>

                <div class="mt-6 flex items-center justify-end gap-4">
                    <x-secondary-button type="reset">
                        {{ __('Reset') }}
                    </x-secondary-button>
                    <x-primary-button type="submit">
                        {{ __('Update Medical History') }}
                    </x-primary-button>
                </div>
            </form>
        </x-app-card>

        <!-- Quick Links -->
        <x-app-card :title="__('Quick Actions')" :description="__('Access your healthcare services and information.')">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-action-button :href="route('patient.doctors.index')" icon="plus" variant="secondary" class="w-full justify-center">
                    {{ __('Find Doctors') }}
                </x-action-button>

                <x-action-button :href="route('patient.appointments.create')" icon="plus" variant="secondary" class="w-full justify-center">
                    {{ __('Book Appointment') }}
                </x-action-button>

                <x-action-button :href="route('patient.bills.index')" icon="view" variant="secondary" class="w-full justify-center">
                    {{ __('View Bills') }}
                </x-action-button>

                <x-action-button :href="route('patient.medical-records.index')" icon="view" variant="secondary" class="w-full justify-center">
                    {{ __('Medical Records') }}
                </x-action-button>
            </div>
        </x-app-card>
    </div>
</x-app-layout>