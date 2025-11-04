<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">{{ __('Appointments') }}</p>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('Manage Your Appointment Schedule') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Confirm upcoming visits, complete consultations, or review past sessions with patients.') }}
                </p>
            </div>

            <x-action-button :href="route('doctor.appointments.create')" icon="plus">
                {{ __('New Appointment') }}
            </x-action-button>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-app-card :title="__('Appointment List')" :description="__('Filter by status or patient name to focus on what matters today.')">
            <div class="flex flex-col gap-4">
                <x-search-bar
                    :action="route('doctor.appointments.index')"
                    :query="request('search')"
                    placeholder="{{ __('Search by patient name, notes, or doctor remark') }}"
                >
                    <select name="status" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">{{ __('All Statuses') }}</option>
                        @foreach(['pending', 'confirmed', 'completed', 'cancelled'] as $statusOption)
                            <option value="{{ $statusOption }}" @selected(request('status') === $statusOption)>
                                {{ ucfirst($statusOption) }}
                            </option>
                        @endforeach
                    </select>
                </x-search-bar>

                <x-data-table
                    :headers="[
                        __('Date & Time'),
                        __('Patient'),
                        __('Status'),
                        __('Notes'),
                        __('Actions'),
                    ]"
                    :paginate="$appointments instanceof \Illuminate\Contracts\Pagination\Paginator ? $appointments->onEachSide(1)->links() : null"
                    :empty-message="__('No appointments found for the selected filters.')"
                >
                    @foreach ($appointments as $appointment)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ optional($appointment->appointment_date)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y g:i A') }}
                                <span class="mt-1 block text-xs text-gray-500">
                                    {{ $appointment->appointment_date?->diffForHumans() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div class="flex flex-col">
                                    <span class="font-medium text-gray-900">
                                        {{ optional($appointment->patient?->user)->name ?? __('Unknown Patient') }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ optional($appointment->patient?->user)->email }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <x-status-badge :status="$appointment->status" />
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ Str::limit($appointment->notes, 120) ?: __('No notes recorded') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <x-action-button :href="route('doctor.appointments.show', $appointment)" icon="view" variant="secondary">
                                        {{ __('View') }}
                                    </x-action-button>
                                    <x-action-button :href="route('doctor.appointments.edit', $appointment)" icon="edit">
                                        {{ __('Update Status') }}
                                    </x-action-button>
                                    <form method="POST" action="{{ route('doctor.appointments.destroy', $appointment) }}" onsubmit="return confirm('{{ __('Are you sure you want to cancel this appointment?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <x-action-button as="button" type="submit" icon="trash" variant="danger">
                                            {{ __('Cancel') }}
                                        </x-action-button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-data-table>
            </div>
        </x-app-card>
    </div>
</x-app-layout>