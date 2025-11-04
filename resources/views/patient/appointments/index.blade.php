<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">{{ __('My Appointments') }}</p>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('Appointment Schedule') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('View and manage your upcoming and past appointments with healthcare providers.') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-action-button :href="route('patient.doctors.index')" icon="arrow-right" variant="secondary">
                    {{ __('Find Doctors') }}
                </x-action-button>
                <x-action-button :href="route('patient.appointments.create')" icon="plus">
                    {{ __('Book New') }}
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

        <!-- Tabs for filtering -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <a href="{{ route('patient.appointments.index', ['status' => 'upcoming']) }}"
                   class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') === 'upcoming' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    {{ __('Upcoming') }}
                </a>
                <a href="{{ route('patient.appointments.index', ['status' => 'completed']) }}"
                   class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') === 'completed' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    {{ __('Completed') }}
                </a>
                <a href="{{ route('patient.appointments.index') }}"
                   class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ !request('status') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    {{ __('All Appointments') }}
                </a>
            </nav>
        </div>

        <!-- Appointments List -->
        <x-app-card :title="__('Appointments')" :description="__('Your scheduled consultations and medical visits.')">
            <x-data-table
                :headers="[
                    __('Date & Time'),
                    __('Doctor'),
                    __('Specialization'),
                    __('Status'),
                    __('Notes'),
                    __('Actions'),
                ]"
                :paginate="$appointments instanceof \Illuminate\Contracts\Pagination\Paginator ? $appointments->onEachSide(1)->links() : null"
                :empty-message="__('No appointments found for the selected filter.')"
            >
                @foreach ($appointments as $appointment)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="flex flex-col">
                                <span class="font-medium">
                                    {{ optional($appointment->appointment_date)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y') }}
                                </span>
                                <span class="text-gray-500">
                                    {{ optional($appointment->appointment_date)->format('g:i A') }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    {{ optional($appointment->appointment_date)->diffForHumans() }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-900">
                                    {{ optional($appointment->doctor?->user)->name ?? __('Unknown Doctor') }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ __('MMC :license', ['license' => $appointment->doctor?->license_number ?? 'N/A']) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-blue-600">
                            {{ $appointment->doctor?->specialization ?? __('Not specified') }}
                        </td>
                        <td class="px-6 py-4">
                            <x-status-badge :status="$appointment->status" />
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ Str::limit($appointment->notes, 80) ?: __('No notes') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <x-action-button :href="route('patient.appointments.show', $appointment)" icon="view" variant="secondary">
                                    {{ __('View Details') }}
                                </x-action-button>
                                @if(in_array($appointment->status, ['pending', 'confirmed']))
                                    <form method="POST" action="{{ route('patient.appointments.destroy', $appointment) }}" onsubmit="return confirm('{{ __('Are you sure you want to cancel this appointment?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <x-action-button as="button" type="submit" icon="trash" variant="danger">
                                            {{ __('Cancel') }}
                                        </x-action-button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-data-table>
        </x-app-card>

        <!-- Quick Stats -->
        <div class="grid gap-4 md:grid-cols-3">
            <x-app-card class="text-center">
                <div class="text-2xl font-bold text-blue-600">
                    {{ $stats['upcoming'] ?? 0 }}
                </div>
                <div class="text-sm text-gray-500">{{ __('Upcoming') }}</div>
            </x-app-card>

            <x-app-card class="text-center">
                <div class="text-2xl font-bold text-green-600">
                    {{ $stats['completed'] ?? 0 }}
                </div>
                <div class="text-sm text-gray-500">{{ __('Completed') }}</div>
            </x-app-card>

            <x-app-card class="text-center">
                <div class="text-2xl font-bold text-red-600">
                    {{ $stats['cancelled'] ?? 0 }}
                </div>
                <div class="text-sm text-gray-500">{{ __('Cancelled') }}</div>
            </x-app-card>
        </div>
    </div>
</x-app-layout>