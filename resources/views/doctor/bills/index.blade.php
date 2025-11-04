<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">{{ __('Billing Management') }}</p>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('Patient Bills & Invoices') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Manage billing for your patients, track payments, and generate invoices for services rendered.') }}
                </p>
            </div>

            <x-action-button :href="route('doctor.bills.create')" icon="plus">
                {{ __('Create New Bill') }}
            </x-action-button>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <x-alert type="success" :title="__('Success')">
                {{ session('status') }}
            </x-alert>
        @endif

        <!-- Quick Stats -->
        <div class="grid gap-4 md:grid-cols-3">
            <x-app-card class="text-center">
                <div class="text-2xl font-bold text-red-600">
                    {{ __('RM :amount', ['amount' => number_format($stats['total_outstanding'] ?? 0, 2)]) }}
                </div>
                <div class="text-sm text-gray-500">{{ __('Total Outstanding') }}</div>
            </x-app-card>

            <x-app-card class="text-center">
                <div class="text-2xl font-bold text-green-600">
                    {{ __('RM :amount', ['amount' => number_format($stats['total_paid'] ?? 0, 2)]) }}
                </div>
                <div class="text-sm text-gray-500">{{ __('Total Paid') }}</div>
            </x-app-card>

            <x-app-card class="text-center">
                <div class="text-2xl font-bold text-blue-600">
                    {{ $stats['total_bills'] ?? 0 }}
                </div>
                <div class="text-sm text-gray-500">{{ __('Total Bills') }}</div>
            </x-app-card>
        </div>

        <!-- Bills List -->
        <x-app-card :title="__('All Bills')" :description="__('Search and filter bills by patient name or status to manage your billing efficiently.')">
            <div class="flex flex-col gap-4">
                <x-search-bar
                    :action="route('doctor.bills.index')"
                    :query="request('search')"
                    placeholder="{{ __('Search by patient name or bill ID') }}"
                >
                    <select name="status" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="unpaid" @selected(request('status') === 'unpaid')>{{ __('Unpaid') }}</option>
                        <option value="paid" @selected(request('status') === 'paid')>{{ __('Paid') }}</option>
                        <option value="partial" @selected(request('status') === 'partial')>{{ __('Partial') }}</option>
                    </select>
                </x-search-bar>

                <x-data-table
                    :headers="[
                        __('Bill ID'),
                        __('Patient'),
                        __('Amount'),
                        __('Status'),
                        __('Due Date'),
                        __('Actions'),
                    ]"
                    :paginate="$bills instanceof \Illuminate\Contracts\Pagination\Paginator ? $bills->onEachSide(1)->links() : null"
                    :empty-message="__('No bills found for the selected filters.')"
                >
                    @foreach ($bills as $bill)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ __('BILL-:id', ['id' => str_pad($bill->id, 5, '0', STR_PAD_LEFT)]) }}
                                <span class="block text-xs text-gray-500">
                                    {{ optional($bill->created_at)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div class="flex flex-col">
                                    <span class="font-medium text-gray-900">
                                        {{ optional($bill->patient?->user)->name ?? __('Unknown Patient') }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ optional($bill->patient?->user)->email }}
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
                                {{ optional($bill->due_date)->format('d/m/Y') ?? __('Not set') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <x-action-button :href="route('doctor.bills.show', $bill)" icon="view" variant="secondary">
                                        {{ __('View') }}
                                    </x-action-button>
                                    <x-action-button :href="route('doctor.bills.edit', $bill)" icon="edit">
                                        {{ __('Edit') }}
                                    </x-action-button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-data-table>
            </div>
        </x-app-card>
    </div>
</x-app-layout>