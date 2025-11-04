@props([
    'headers' => [],
    'emptyMessage' => 'No records found.',
    'striped' => true,
    'hover' => true,
    'paginate' => null,
])

@php
    $tableClasses = [
        'min-w-full divide-y divide-gray-200 text-left text-sm text-gray-700',
        $hover ? 'table-hover' : '',
    ];

    $tbodyClasses = [
        'divide-y divide-gray-100',
        $striped ? 'odd:bg-white even:bg-gray-50' : '',
    ];
@endphp

<div {{ $attributes->class('overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm') }}>
    <div class="overflow-x-auto">
        <table class="{{ implode(' ', array_filter($tableClasses)) }}">
            <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                <tr>
                    @foreach($headers as $header)
                        <th scope="col" class="px-6 py-3 font-semibold">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="{{ implode(' ', array_filter($tbodyClasses)) }}">
                @forelse($slot as $row)
                    {{ $row }}
                @empty
                    <tr>
                        <td colspan="{{ count($headers) }}" class="px-6 py-8 text-center text-sm text-gray-500">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="h-8 w-8 text-gray-300" viewBox="0 0 24 24" fill="none">
                                    <path d="M7 7H17M7 11H17M7 15H12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                    <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                </svg>
                                <span>{{ $emptyMessage }}</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($paginate)
        <div class="border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6">
            {{ $paginate }}
        </div>
    @endif
</div>