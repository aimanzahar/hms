<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">{{ __('Create New Bill') }}</p>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('Generate Invoice') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Create a new bill for patient services and consultations.') }}
                </p>
            </div>

            <x-action-button :href="route('doctor.bills.index')" icon="arrow-right" variant="secondary">
                {{ __('Back to Bills') }}
            </x-action-button>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <x-alert type="success" :title="__('Success')">
                {{ session('status') }}
            </x-alert>
        @endif

        <form method="POST" action="{{ route('doctor.bills.store') }}" class="space-y-6">
            @csrf

            <!-- Appointment Selection -->
            <x-app-card :title="__('Appointment Details')" :description="__('Select the appointment for which this bill is being created.')">
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <x-input-label for="appointment_id" value="{{ __('Select Appointment') }}" />
                        <select id="appointment_id" name="appointment_id" class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                            <option value="">{{ __('Choose an appointment...') }}</option>
                            @foreach($availableAppointments as $appointment)
                                <option value="{{ $appointment->id }}" @selected(request('appointment') == $appointment->id)>
                                    {{ optional($appointment->appointment_date)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') }}
                                    - {{ optional($appointment->patient?->user)->name ?? __('Unknown Patient') }}
                                    ({{ ucfirst($appointment->status) }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('appointment_id')" class="mt-2" />
                    </div>
                </div>
            </x-app-card>

            <!-- Bill Items -->
            <x-app-card :title="__('Bill Items')" :description="__('Add services, consultations, and procedures with their respective amounts.')">
                <div id="bill-items" class="space-y-4">
                    <!-- Template for bill items -->
                    <div class="bill-item grid grid-cols-1 gap-4 sm:grid-cols-5">
                        <div class="sm:col-span-3">
                            <x-input-label value="{{ __('Description') }}" />
                            <x-text-input
                                type="text"
                                name="items[0][description]"
                                class="mt-1 block w-full"
                                placeholder="{{ __('e.g., Consultation fee, Blood test, X-ray') }}"
                                required
                            />
                        </div>
                        <div>
                            <x-input-label value="{{ __('Amount (RM)') }}" />
                            <x-text-input
                                type="number"
                                name="items[0][amount]"
                                class="mt-1 block w-full"
                                step="0.01"
                                min="0"
                                placeholder="0.00"
                                required
                            />
                        </div>
                        <div class="flex items-end">
                            <x-danger-button type="button" onclick="removeBillItem(this)" class="w-full">
                                {{ __('Remove') }}
                            </x-danger-button>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <x-secondary-button type="button" onclick="addBillItem()">
                        {{ __('Add Another Item') }}
                    </x-secondary-button>
                </div>

                <!-- Total Calculation -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-900">{{ __('Total Amount') }}</span>
                        <span id="total-amount" class="text-lg font-bold text-gray-900">RM 0.00</span>
                    </div>
                </div>
            </x-app-card>

            <!-- Bill Settings -->
            <x-app-card :title="__('Bill Settings')" :description="__('Configure payment terms and status.')">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <x-input-label for="due_date" value="{{ __('Due Date') }}" />
                        <x-text-input
                            id="due_date"
                            name="due_date"
                            type="date"
                            class="mt-1 block w-full"
                            :value="old('due_date', now()->addDays(30)->format('Y-m-d'))"
                            required
                        />
                        <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="status" value="{{ __('Initial Status') }}" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                            <option value="unpaid" @selected(old('status', 'unpaid') === 'unpaid')>{{ __('Unpaid') }}</option>
                            <option value="paid" @selected(old('status') === 'paid')>{{ __('Paid') }}</option>
                            <option value="partial" @selected(old('status') === 'partial')>{{ __('Partial') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>
                </div>
            </x-app-card>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-4">
                <x-secondary-button type="reset">
                    {{ __('Reset') }}
                </x-secondary-button>
                <x-primary-button type="submit">
                    {{ __('Create Bill') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
        let itemCount = 1;

        function addBillItem() {
            const container = document.getElementById('bill-items');
            const template = `
                <div class="bill-item grid grid-cols-1 gap-4 sm:grid-cols-5">
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <input type="text" name="items[${itemCount}][description]" class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="e.g., Consultation fee, Blood test, X-ray" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Amount (RM)</label>
                        <input type="number" name="items[${itemCount}][amount]" class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400" step="0.01" min="0" placeholder="0.00" required onchange="calculateTotal()">
                    </div>
                    <div class="flex items-end">
                        <button type="button" onclick="removeBillItem(this)" class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 disabled:opacity-25 transition">
                            Remove
                        </button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', template);
            itemCount++;
            calculateTotal();
        }

        function removeBillItem(button) {
            button.closest('.bill-item').remove();
            calculateTotal();
        }

        function calculateTotal() {
            const amounts = document.querySelectorAll('input[name*="[amount]"]');
            let total = 0;
            amounts.forEach(input => {
                const value = parseFloat(input.value) || 0;
                total += value;
            });
            document.getElementById('total-amount').textContent = 'RM ' + total.toFixed(2);
        }

        // Calculate total on page load
        document.addEventListener('DOMContentLoaded', calculateTotal);
    </script>
</x-app-layout>