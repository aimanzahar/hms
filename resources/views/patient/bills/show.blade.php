<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">{{ __('Bill Details') }}</p>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('Bill :id', ['id' => str_pad($bill->id, 5, '0', STR_PAD_LEFT)]) }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Complete billing information for your medical consultation.') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-action-button :href="route('patient.bills.index')" icon="arrow-right" variant="secondary">
                    {{ __('Back to Bills') }}
                </x-action-button>
                @if(in_array($bill->status, ['unpaid', 'partial']))
                    <x-action-button :href="route('patient.bills.show', $bill)" icon="credit-card" class="bg-green-600 hover:bg-green-700">
                        {{ __('Pay Now') }}
                    </x-action-button>
                @endif
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
            <!-- Bill Overview -->
            <x-app-card class="lg:col-span-2" :title="__('Bill Overview')" :description="__('Summary of charges and payment information.')">
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Bill ID') }}</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            {{ __('BILL-:id', ['id' => str_pad($bill->id, 5, '0', STR_PAD_LEFT)]) }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                        <dd class="mt-1">
                            <x-status-badge :status="$bill->status" size="lg" />
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Appointment Date') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ optional($bill->appointment->appointment_date)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Doctor') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ optional($bill->appointment->doctor?->user)->name ?? __('Unknown') }}
                        </dd>
                        <dd class="text-xs text-blue-600">
                            {{ $bill->appointment->doctor?->specialization ?? __('Not specified') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Due Date') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($bill->due_date)
                                {{ optional($bill->due_date)->format('d/m/Y') }}
                                @if($bill->due_date->isPast() && $bill->status !== 'paid')
                                    <span class="block text-xs text-red-600 font-medium">{{ __('Overdue') }}</span>
                                @endif
                            @else
                                {{ __('Not set') }}
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Total Amount') }}</dt>
                        <dd class="mt-1 text-2xl font-bold text-gray-900">
                            {{ __('RM :amount', ['amount' => number_format($bill->total_amount, 2)]) }}
                        </dd>
                    </div>
                </dl>
            </x-app-card>

            <!-- Payment Actions -->
            <x-app-card :title="__('Payment Actions')" :description="__('Manage your bill payment.')">
                <div class="space-y-4">
                    @if($bill->status === 'paid')
                        <div class="text-center py-4">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-green-900">{{ __('Payment Completed') }}</p>
                            <p class="text-xs text-green-700">{{ __('This bill has been fully paid.') }}</p>
                        </div>
                    @elseif($bill->status === 'partial')
                        <div class="text-center py-4">
                            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-yellow-900">{{ __('Partial Payment') }}</p>
                            <p class="text-xs text-yellow-700">{{ __('Outstanding balance remains.') }}</p>
                        </div>
                        <x-action-button :href="route('patient.bills.show', $bill)" icon="credit-card" class="w-full justify-center bg-green-600 hover:bg-green-700">
                            {{ __('Pay Remaining Balance') }}
                        </x-action-button>
                    @else
                        <x-action-button :href="route('patient.bills.show', $bill)" icon="credit-card" class="w-full justify-center bg-green-600 hover:bg-green-700">
                            {{ __('Pay Bill Now') }}
                        </x-action-button>
                    @endif

                    <div class="border-t border-gray-200 pt-4">
                        <p class="text-xs text-gray-500 text-center">
                            {{ __('Need help with payment?') }}<br>
                            <a href="mailto:support@clinic.com" class="text-blue-600 hover:text-blue-800">
                                {{ __('Contact billing support') }}
                            </a>
                        </p>
                    </div>
                </div>
            </x-app-card>
        </div>

        <!-- Bill Items -->
        <x-app-card :title="__('Bill Breakdown')" :description="__('Detailed list of all charges included in this bill.')">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Description') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Amount') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($bill->billItems as $item)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $item->description }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right font-medium">
                                    {{ __('RM :amount', ['amount' => number_format($item->amount, 2)]) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-sm text-gray-500 text-center">
                                    {{ __('No detailed breakdown available') }}
                                </td>
                            </tr>
                        @endforelse

                        <!-- Total Row -->
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                {{ __('Total Amount') }}
                            </td>
                            <td class="px-6 py-4 text-lg font-bold text-gray-900 text-right">
                                {{ __('RM :amount', ['amount' => number_format($bill->total_amount, 2)]) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-app-card>

        <!-- Payment History (if applicable) -->
        @if($bill->status !== 'unpaid')
            <x-app-card :title="__('Payment History')" :description="__('Record of payments made towards this bill.')">
                <div class="space-y-4">
                    @if($bill->status === 'paid')
                        <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-green-900">{{ __('Fully Paid') }}</p>
                                    <p class="text-xs text-green-700">{{ __('Payment completed successfully') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-green-900">
                                    {{ __('RM :amount', ['amount' => number_format($bill->total_amount, 2)]) }}
                                </p>
                                <p class="text-xs text-green-700">
                                    {{ optional($bill->updated_at)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                    @elseif($bill->status === 'partial')
                        <div class="flex items-center justify-between p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-yellow-900">{{ __('Partial Payment') }}</p>
                                    <p class="text-xs text-yellow-700">{{ __('Outstanding balance: RM :amount', ['amount' => number_format($bill->total_amount * 0.5, 2)]) }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-yellow-900">
                                    {{ __('RM :amount', ['amount' => number_format($bill->total_amount * 0.5, 2)]) }}
                                </p>
                                <p class="text-xs text-yellow-700">
                                    {{ __('Paid on :date', ['date' => now()->format('d/m/Y')]) }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </x-app-card>
        @endif

        <!-- Bill Information -->
        <x-app-card :title="__('Bill Information')" :description="__('Technical details about this bill.')">
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Created') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ optional($bill->created_at)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Last Updated') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ optional($bill->updated_at)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Bill ID') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">
                        BILL-{{ str_pad($bill->id, 5, '0', STR_PAD_LEFT) }}
                    </dd>
                </div>
            </dl>
        </x-app-card>

        <!-- Print/Download Actions -->
        <x-app-card :title="__('Additional Actions')" :description="__('Download or print your bill for records.')">
            <div class="flex flex-wrap gap-4">
                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    {{ __('Print Bill') }}
                </button>

                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    {{ __('Download PDF') }}
                </button>
            </div>
        </x-app-card>
    </div>
</x-app-layout>