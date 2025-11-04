<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">{{ __('Edit Bill') }}</p>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('Update BILL-:id', ['id' => str_pad($bill->id, 5, '0', STR_PAD_LEFT)]) }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Modify bill items, amounts, and payment status.') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-action-button :href="route('doctor.bills.show', $bill)" icon="view" variant="secondary">
                    {{ __('View Bill') }}
                </x-action-button>
                <x-action-button :href="route('doctor.bills.index')" icon="arrow-right">
                    {{ __('Back to Bills') }}
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

        <form method="POST" action="{{ route('doctor.bills.update', $bill) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Bill Overview (Read-only) -->
            <x-app-card :title="__('Bill Overview')" :description="__('Basic bill information (read-only).')">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Bill ID') }}</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            {{ __('BILL-:id', ['id' => str_pad($bill->id, 5, '0', STR_PAD_LEFT)]) }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Patient') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ optional($bill->patient?->user)->name ?? __('Unknown Patient') }}
                        </dd>
                    </div>

                    @if($bill->appointment)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Appointment Date') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ optional($bill->appointment->appointment_date)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') }}
                            </dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Created') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ optional($bill->created_at)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') }}
                        </dd>
                    </div>
                </div>
            </x-app-card>

            <!-- Bill Items -->
            <x-app-card :title="__('Bill Items')" :description="__('Edit services, consultations, and procedures with their respective amounts.')">
                <div id="bill-items" class="space-y-4">
                    @foreach($bill->items as $index => $item)
                        <div class="bill-item grid grid-cols-1 gap-4 sm:grid-cols-5">
                            <div class="sm:col-span-3">
                                <x-input-label value="{{ __('Description') }}" />
                                <x-text-input
                                    type="text"
                                    name="items[{{ $index }}][description]"
                                    class="mt-1 block w-full"
                                    :value="old('items.' . $index . '.description', $item->description)"
                                    required
                                />
                                <x-input-error :messages="$errors->get('items.' . $index . '.description')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label value="{{ __('Amount (RM)') }}" />
                                <x-text-input
                                    type="number"
                                    name="items[{{ $index }}][amount]"
                                    class="mt-1 block w-full"
                                    step="0.01"
                                    min="0"
                                    :value="old('items.' . $index . '.amount', $item->amount)"
                                    required
                                />
                                <x-input-error :messages="$errors->get('items.' . $index . '.amount')" class="mt-2" />
                            </div>
                            <div class="flex items-end">
                                <x-danger-button type="button" onclick="removeBillItem(this)" class="w-full">
                                    {{ __('Remove') }}
                                </x-danger-button>
                            </div>
                        </div>
                    @endforeach
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
                        <span id="total-amount" class="text-lg font-bold text-gray-900">RM {{ number_format($bill->total_amount, 2) }}</span>
                    </div>
                </div>
            </x-app-card>

            <!-- Bill Settings -->
            <x-app-card :title="__('Bill Settings')" :description="__('Update payment terms and status.')">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <x-input-label for="due_date" value="{{ __('Due Date') }}" />
                        <x-text-input
                            id="due_date"
                            name="due_date"
                            type="date"
                            class="mt-1 block w-full"
                            :value="old('due_date', optional($bill->due_date)->format('Y-m-d'))"
                            required
                        />
                        <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="status" value="{{ __('Payment Status') }}" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                            <option value="unpaid" @selected(old('status', $bill->status) === 'unpaid')>{{ __('Unpaid') }}</option>
                            <option value="paid" @selected(old('status', $bill->status) === 'paid')>{{ __('Paid') }}</option>
                            <option value="partial" @selected(old('status', $bill->status) === 'partial')>{{ __('Partial') }}</option>
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
                    {{ __('Update Bill') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
        let itemCount = {{ count($bill->items) }};

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