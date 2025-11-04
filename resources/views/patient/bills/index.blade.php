<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">{{ __('My Bills') }}</p>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('Billing History') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('View and manage your medical bills and payment status.') }}
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

        <!-- Filter Tabs -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <a href="{{ route('patient.bills.index') }}"
                   class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ !request('status') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    {{ __('All Bills') }}
                </a>
                <a href="{{ route('patient.bills.index', ['status' => 'unpaid']) }}"
                   class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') === 'unpaid' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    {{ __('Unpaid') }}
                </a>
                <a href="{{ route('patient.bills.index', ['status' => 'paid']) }}"
                   class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') === 'paid' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    {{ __('Paid') }}
                </a>
                <a href="{{ route('patient.bills.index', ['status' => 'partial']) }}"
                   class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') === 'partial' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    {{ __('Partial') }}
                </a>
            </nav>
        </div>

        <!-- Bills List -->
        <x-app-card :title="__('Billing Records')" :description="__('Your medical consultation invoices and payment history.')">
            <x-data-table
                :headers="[
                    __('Bill ID'),
                    __('Date'),
                    __('Doctor'),
                    __('Amount'),
                    __('Status'),
                    __('Due Date'),
                    __('Actions'),
                ]"
                :paginate="$bills instanceof \Illuminate\Contracts\Pagination\Paginator ? $bills->onEachSide(1)->links() : null"
                :empty-message="__('No bills found for the selected filter.')"
            >
                @foreach ($bills as $bill)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ __('BILL-:id', ['id' => str_pad($bill->id, 5, '0', STR_PAD_LEFT)]) }}
                            <span class="block text-xs text-gray-500">
                                {{ optional($bill->created_at)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ optional($bill->appointment->appointment_date)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-900">
                                    {{ optional($bill->appointment->doctor?->user)->name ?? __('Unknown Doctor') }}
                                </span>
                                <span class="text-xs text-blue-600">
                                    {{ $bill->appointment->doctor?->specialization ?? __('Not specified') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                            {{ __('RM :amount', ['amount' => number_format($bill->total_amount, 2)]) }}
                        </td>
                        <td class="px-6 py-4">
                            <x-status-badge :status="$bill->status" />
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($bill->due_date)
                                {{ optional($bill->due_date)->format('d/m/Y') }}
                                @if($bill->due_date->isPast() && $bill->status !== 'paid')
                                    <span class="block text-xs text-red-600 font-medium">{{ __('Overdue') }}</span>
                                @endif
                            @else
                                {{ __('Not set') }}
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <x-action-button :href="route('patient.bills.show', $bill)" icon="view" variant="secondary">
                                    {{ __('View Details') }}
                                </x-action-button>
                                @if(in_array($bill->status, ['unpaid', 'partial']))
                                    <x-action-button :href="route('patient.bills.show', $bill)" icon="credit-card" class="bg-green-600 hover:bg-green-700">
                                        {{ __('Pay Now') }}
                                    </x-action-button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-data-table>
        </x-app-card>

        <!-- Billing Summary -->
        <div class="grid gap-4 md:grid-cols-4">
            <x-app-card class="text-center">
                <div class="text-2xl font-bold text-blue-600">
                    {{ __('RM :amount', ['amount' => number_format($stats['total_outstanding'] ?? 0, 2)]) }}
                </div>
                <div class="text-sm text-gray-500">{{ __('Total Outstanding') }}</div>
            </x-app-card>

            <x-app-card class="text-center">
                <div class="text-2xl font-bold text-red-600">
                    {{ $stats['unpaid_count'] ?? 0 }}
                </div>
                <div class="text-sm text-gray-500">{{ __('Unpaid Bills') }}</div>
            </x-app-card>

            <x-app-card class="text-center">
                <div class="text-2xl font-bold text-green-600">
                    {{ __('RM :amount', ['amount' => number_format($stats['total_paid'] ?? 0, 2)]) }}
                </div>
                <div class="text-sm text-gray-500">{{ __('Total Paid') }}</div>
            </x-app-card>

            <x-app-card class="text-center">
                <div class="text-2xl font-bold text-purple-600">
                    {{ $stats['overdue_count'] ?? 0 }}
                </div>
                <div class="text-sm text-gray-500">{{ __('Overdue') }}</div>
            </x-app-card>
        </div>

        <!-- Payment Information -->
        <x-app-card :title="__('Payment Information')" :description="__('How to pay your medical bills.')">
            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">{{ __('Payment Methods') }}</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Online payment (credit/debit cards)') }}
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Bank transfer') }}
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Cash payment at clinic') }}
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">{{ __('Important Notes') }}</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ __('Payments are processed securely') }}
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ __('Receipts available after payment') }}
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ __('Contact clinic for payment issues') }}
                        </li>
                    </ul>
                </div>
            </div>
        </x-app-card>
    </div>
</x-app-layout>