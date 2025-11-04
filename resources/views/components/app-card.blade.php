@props([
    'title' => null,
    'description' => null,
    'actions' => null,
    'padding' => 'p-6',
])

@php
    $containerClasses = 'bg-white shadow-sm border border-gray-100 rounded-xl';
    $contentPadding = trim($padding) !== '' ? $padding : 'p-6';
@endphp

<div {{ $attributes->class($containerClasses) }}>
    @if($title || $description || $actions)
        <div class="flex flex-col gap-3 border-b border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                @if($title)
                    <h3 class="text-base font-semibold leading-6 text-gray-900">
                        {{ $title }}
                    </h3>
                @endif

                @if($description)
                    <p class="text-sm text-gray-500">
                        {{ $description }}
                    </p>
                @endif
            </div>

            @if($actions)
                <div class="flex flex-wrap items-center gap-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    <div class="{{ $contentPadding }}">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="border-t border-gray-100 px-6 py-4">
            {{ $footer }}
        </div>
    @endisset
</div>