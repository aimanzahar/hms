@props([
    'items' => [],
    'columns' => 1,
    'border' => true,
])

@php
    $columns = max((int) $columns, 1);

    $gridClasses = match ($columns) {
        1 => 'grid grid-cols-1',
        2 => 'grid grid-cols-1 gap-x-8 gap-y-4 sm:grid-cols-2',
        3 => 'grid grid-cols-1 gap-x-8 gap-y-4 lg:grid-cols-3',
        default => 'grid grid-cols-1 gap-x-8 gap-y-4 sm:grid-cols-2',
    };

    $containerClasses = $border
        ? 'overflow-hidden rounded-xl border border-gray-200 bg-white'
        : '';
@endphp

<div {{ $attributes->class($containerClasses) }}>
    <dl class="{{ $gridClasses }}">
        @foreach($items as $label => $value)
            <div class="{{ $border ? 'px-6 py-4' : 'py-3' }} @if($border) border-b border-gray-100 last:border-b-0 @endif">
                <dt class="text-sm font-medium text-gray-500">
                    {{ $label }}
                </dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ $value instanceof \Illuminate\Support\HtmlString ? $value : e($value) }}
                </dd>
            </div>
        @endforeach

        {{ $slot }}
    </dl>
</div>