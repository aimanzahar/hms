<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">{{ __('Bill Details') }}</p>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('BILL-:id', ['id' => str_pad($bill->id, 5, '0', STR_PAD_LEFT)]) }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Review bill information, payment status, and related appointment details.') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-action-button :href="route('doctor.bills.index')" icon="arrow-right" variant="secondary">
                    {{ __('Back to Bills') }}
                </x-action-button>
                <x-action-button :href="route('doctor.bills.edit', $bill)" icon="edit">
                    {{ __('Edit Bill') }}
                </x-action-button>
                @if($bill->status === 'unpaid')
                    <form method="POST" action="{{ route('doctor.bills.update', $bill) }}" class="inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="paid">
                        <x-action-button as="button" type="submit" icon="check" variant="success">
                            {{ __('Mark as Paid') }}
                        </x-action-button>
                    </form>
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
            <x-app-card class="lg:col-span-2" :title="__('Bill Overview')" :description="__('Complete billing information and payment details.')">
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
                        <dt class="text-sm font-medium text-gray-500">{{ __('Total Amount') }}</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            {{ __('RM :amount', ['amount' => number_format($bill->total_amount, 2)]) }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Due Date') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ optional($bill->due_date)->format('d/m/Y') ?? __('Not set') }}
                        </dd>
                    </div>

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
                </dl>
            </x-app-card>

            <!-- Patient Information -->
            <x-app-card :title="__('Patient Information')" :description="__('Details of the billed patient.')">
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Name') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ optional($bill->patient?->user)->name ?? __('Unknown Patient') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Email') }}</dt>
                        <dd class="mt-1 text-sm text-blue-600">
                            {{ optional($bill->patient?->user)->email }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Phone') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $bill->patient?->phone ?? __('Not provided') }}
                        </dd>
                    </div>
                </dl>

                <div class="mt-4">
                    <x-action-button :href="route('doctor.patients.show', $bill->patient)" icon="view" variant="secondary" size="sm">
                        {{ __('View Patient Profile') }}
                    </x-action-button>
                </div>
            </x-app-card>
        </div>

        <!-- Bill Items -->
        <x-app-card :title="__('Bill Items')" :description="__('Detailed breakdown of charges and services.')">
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
                        @forelse($bill->items as $item)
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
                                <td colspan="2" class="px-6 py-8 text-center text-sm text-gray-500">
                                    {{ __('No bill items found.') }}
                                </td>
                            </tr>
                        @endforelse
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                {{ __('Total') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">
                                {{ __('RM :amount', ['amount' => number_format($bill->total_amount, 2)]) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-app-card>

        <!-- Related Appointment -->
        @if($bill->appointment)
            <x-app-card :title="__('Related Appointment')" :description="__('Appointment that generated this bill.')">
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Appointment Date') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ optional($bill->appointment->appointment_date)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                        <dd class="mt-1">
                            <x-status-badge :status="$bill->appointment->status" />
                        </dd>
                    </div>
                </dl>

                <div class="mt-4">
                    <x-action-button :href="route('doctor.appointments.show', $bill->appointment)" icon="view" variant="secondary" size="sm">
                        {{ __('View Appointment Details') }}
                    </x-action-button>
                </div>
            </x-app-card>
        @endif
    </div>
</x-app-layout>