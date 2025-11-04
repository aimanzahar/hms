@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => false,
    'icon' => null,
    'onDismiss' => null,
])

@php
    $normalized = strtolower((string) $type);

    $variants = [
        'success' => [
            'container' => 'bg-emerald-50 text-emerald-800 border border-emerald-200',
            'icon' => $icon ?? 'success',
        ],
        'warning' => [
            'container' => 'bg-amber-50 text-amber-800 border border-amber-200',
            'icon' => $icon ?? 'warning',
        ],
        'error' => [
            'container' => 'bg-rose-50 text-rose-800 border border-rose-200',
            'icon' => $icon ?? 'error',
        ],
        'info' => [
            'container' => 'bg-blue-50 text-blue-800 border border-blue-200',
            'icon' => $icon ?? 'info',
        ],
        'neutral' => [
            'container' => 'bg-slate-50 text-slate-800 border border-slate-200',
            'icon' => $icon ?? 'neutral',
        ],
    ];

    $icons = [
        'success' => fn () => '<svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none"><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="1.5"/><path d="M16 9L10.5 14.5L8 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        'warning' => fn () => '<svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none"><path d="M12 9V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M12 17.01L12.01 16.9989" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M10.292 3.86001L2.822 17.01C2.30304 17.9097 3.13133 19 4.21001 19H19.79C20.8687 19 21.697 17.9097 21.178 17.01L13.708 3.86001C13.1539 2.90233 11.8461 2.90233 11.292 3.86001Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>',
        'error' => fn () => '<svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none"><path d="M17 7L7 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M7 7L17 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22Z" stroke="currentColor" stroke-width="1.5"/></svg>',
        'info' => fn () => '<svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none"><path d="M12 8H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M12 12V16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="1.5"/></svg>',
        'neutral' => fn () => '<svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none"><path d="M4 7H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M4 17H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M4 12H14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
    ];

    $variant = $variants[$normalized] ?? $variants['info'];
    $iconKey = strtolower((string) $variant['icon']);
    $iconSvg = isset($icons[$iconKey]) ? $icons[$iconKey]() : null;
@endphp

<div {{ $attributes->class([
    'relative overflow-hidden rounded-xl px-4 py-4 sm:px-5 sm:py-5 transition-all duration-200',
    $variant['container'],
]) }}>
    <div class="flex items-start gap-3 sm:gap-4">
        @if($iconSvg)
            <div class="mt-0.5 text-current">
                {!! $iconSvg !!}
            </div>
        @endif

        <div class="min-w-0 space-y-2">
            @if($title)
                <h3 class="text-sm font-semibold leading-6 sm:text-base">
                    {{ $title }}
                </h3>
            @endif

            <div class="text-sm leading-6 sm:text-base">
                {{ $slot }}
            </div>
        </div>

        @if($dismissible)
            <button
                type="button"
                x-data
                @click="{{ $onDismiss ?? 'window.dispatchEvent(new CustomEvent(\'alert-dismissed\'))' }}"
                class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-current/70 transition hover:bg-black/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-current/40"
            >
                <span class="sr-only">Dismiss</span>
                <svg class="h-4 w-4 sm:h-5 sm:w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 8.586l4.95-4.95a1 1 0 111.414 1.414L11.414 10l4.95 4.95a1 1 0 01-1.414 1.414L10 11.414l-4.95 4.95a1 1 0 01-1.414-1.414L8.586 10l-4.95-4.95A1 1 0 115.05 3.636L10 8.586z" clip-rule="evenodd" />
                </svg>
            </button>
        @endif
    </div>
</div>