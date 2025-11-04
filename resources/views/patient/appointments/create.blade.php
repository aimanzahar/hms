<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">{{ __('Book Appointment') }}</p>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('Schedule Consultation') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Book an appointment with a healthcare professional for your medical needs.') }}
                </p>
            </div>

            <x-action-button :href="route('patient.appointments.index')" icon="arrow-right" variant="secondary">
                {{ __('Back to Appointments') }}
            </x-action-button>
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
                {{ __('Some fields require your attention before we can book your appointment.') }}
            </x-alert>
        @endif

        <form method="POST" action="{{ route('patient.appointments.store') }}" class="space-y-6">
            @csrf

            <!-- Doctor Selection -->
            <x-app-card :title="__('Select Doctor')" :description="__('Choose the healthcare professional you want to consult with.')">
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <x-input-label for="doctor_id" value="{{ __('Healthcare Professional') }}" />
                        <select id="doctor_id" name="doctor_id" class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                            <option value="">{{ __('Select a doctor...') }}</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" @selected(request('doctor') == $doctor->id)>
                                    {{ optional($doctor->user)->name ?? __('Dr. Unknown') }}
                                    - {{ $doctor->specialization ?? __('Specialization not specified') }}
                                    (RM {{ number_format($doctor->consultation_fee ?? 0, 2) }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('doctor_id')" class="mt-2" />
                    </div>
                </div>
            </x-app-card>

            <!-- Appointment Details -->
            <x-app-card :title="__('Appointment Details')" :description="__('Set the date, time, and provide any additional information.')">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <x-input-label for="appointment_date" value="{{ __('Preferred Date') }}" />
                        <x-text-input
                            id="appointment_date"
                            name="appointment_date"
                            type="date"
                            class="mt-1 block w-full"
                            :value="old('appointment_date')"
                            min="{{ now()->format('Y-m-d') }}"
                            required
                        />
                        <x-input-error :messages="$errors->get('appointment_date')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="appointment_time" value="{{ __('Preferred Time') }}" />
                        <select id="appointment_time" name="appointment_time" class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                            <option value="">{{ __('Select time...') }}</option>
                            @for($hour = 8; $hour <= 17; $hour++)
                                @for($minute = 0; $minute < 60; $minute += 30)
                                    <option value="{{ sprintf('%02d:%02d', $hour, $minute) }}" @selected(old('appointment_time') === sprintf('%02d:%02d', $hour, $minute))>
                                        {{ sprintf('%02d:%02d', $hour, $minute) }}
                                    </option>
                                @endfor
                            @endfor
                        </select>
                        <x-input-error :messages="$errors->get('appointment_time')" class="mt-2" />
                        <p class="mt-1 text-xs text-gray-500">{{ __('Clinic hours: 8:00 AM - 5:00 PM') }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    <x-input-label for="notes" value="{{ __('Additional Notes (Optional)') }}" />
                    <textarea
                        id="notes"
                        name="notes"
                        rows="4"
                        class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="{{ __('Describe your symptoms, reason for visit, or any special requirements...') }}"
                    >{{ old('notes') }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>
            </x-app-card>

            <!-- Appointment Summary -->
            <x-app-card :title="__('Appointment Summary')" :description="__('Review your booking details before confirming.')">
                <div class="bg-gray-50 rounded-lg p-4">
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Consultation Fee') }}</dt>
                            <dd id="fee-display" class="text-sm font-semibold text-gray-900">RM 0.00</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Estimated Duration') }}</dt>
                            <dd class="text-sm text-gray-900">30 minutes</dd>
                        </div>
                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-900">{{ __('Total') }}</dt>
                                <dd id="total-display" class="text-lg font-bold text-gray-900">RM 0.00</dd>
                            </div>
                        </div>
                    </dl>
                </div>

                <div class="mt-4 text-sm text-gray-600">
                    <p class="font-medium">{{ __('Important Notes:') }}</p>
                    <ul class="mt-2 space-y-1 list-disc list-inside">
                        <li>{{ __('Appointments are subject to doctor availability and confirmation.') }}</li>
                        <li>{{ __('Please arrive 15 minutes before your scheduled time.') }}</li>
                        <li>{{ __('Bring any relevant medical records or test results.') }}</li>
                        <li>{{ __('Cancellation policy: 24 hours notice required.') }}</li>
                    </ul>
                </div>
            </x-app-card>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-4">
                <x-secondary-button type="reset">
                    {{ __('Reset Form') }}
                </x-secondary-button>
                <x-primary-button type="submit">
                    {{ __('Book Appointment') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
        // Update fee display when doctor is selected
        document.getElementById('doctor_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const feeText = selectedOption.text.match(/\(RM ([\d.]+)\)/);
            const fee = feeText ? parseFloat(feeText[1]) : 0;

            document.getElementById('fee-display').textContent = 'RM ' + fee.toFixed(2);
            document.getElementById('total-display').textContent = 'RM ' + fee.toFixed(2);
        });

        // Set initial fee if doctor is pre-selected
        document.addEventListener('DOMContentLoaded', function() {
            const doctorSelect = document.getElementById('doctor_id');
            if (doctorSelect.value) {
                doctorSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
</x-app-layout>