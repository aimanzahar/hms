@props([
    'action' => '#',
    'method' => 'GET',
    'placeholder' => 'Search…',
    'query' => request()->query('search', ''),
    'name' => 'search',
    'advanced' => false,
])

@php
    $method = strtoupper($method);
@endphp

<form action="{{ $action }}" method="{{ $method === 'GET' ? 'GET' : 'POST' }}" class="w-full">
    @csrf
    @if(! in_array($method, ['GET', 'POST'], true))
        @method($method)
    @endif

    <div class="flex flex-wrap items-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm transition focus-within:border-blue-400 focus-within:ring-2 focus-within:ring-blue-200">
        <div class="flex min-w-0 flex-1 items-center gap-2">
            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none">
                <path d="M21 21L17 17M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>

            <input
                type="text"
                name="{{ $name }}"
                value="{{ old($name, $query) }}"
                placeholder="{{ $placeholder }}"
                class="block w-full min-w-0 border-0 p-0 text-sm text-gray-900 placeholder:text-gray-400 focus:border-0 focus:ring-0"
            />
        </div>

        <div class="flex items-center gap-2">
            {{ $advanced }}

            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-blue-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                    <path d="M4 7H20M4 12H20M4 17H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Search
            </button>
        </div>
    </div>
</form>