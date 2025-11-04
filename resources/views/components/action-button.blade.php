@props([
    'href' => '#',
    'variant' => 'primary',
    'icon' => null,
    'iconPosition' => 'left',
    'size' => 'md',
    'target' => null,
    'tooltip' => null,
])

@php
    $variant = strtolower((string) $variant);
    $iconPosition = strtolower((string) $iconPosition);

    $variants = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-500 focus-visible:ring-blue-600',
        'secondary' => 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50 focus-visible:ring-gray-400',
        'danger' => 'bg-rose-600 text-white hover:bg-rose-500 focus-visible:ring-rose-600',
        'ghost' => 'bg-transparent text-gray-600 hover:bg-gray-100 focus-visible:ring-gray-300',
        'success' => 'bg-emerald-600 text-white hover:bg-emerald-500 focus-visible:ring-emerald-600',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-3.5 py-2 text-sm',
        'lg' => 'px-4 py-2.5 text-sm font-semibold',
    ];

    $baseClasses = 'inline-flex items-center gap-2 rounded-lg transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2';
    $variantClasses = $variants[$variant] ?? $variants['primary'];
    $sizeClasses = $sizes[$size] ?? $sizes['md'];

    $icons = [
        'plus' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M12 5V19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M5 12H19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
        'arrow-right' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M5 12H19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M12 5L19 12L12 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        'edit' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M4 16.0002V19.0002H7L17.586 8.41421L14.586 5.41421L4 16.0002Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M13.0002 6.99976L17.0002 10.9998" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
        'trash' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M9 9V15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M15 9V15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M4 7H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M6 7L7 19C7 20.1046 7.89543 21 9 21H15C16.1046 21 17 20.1046 17 19L18 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M10 7V5C10 3.89543 10.8954 3 12 3C13.1046 3 14 3.89543 14 5V7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
        'view' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M2 12C3.8 7.6 7.6 5 12 5C16.4 5 20.2 7.6 22 12C20.2 16.4 16.4 19 12 19C7.6 19 3.8 16.4 2 12Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>',
    ];

    $iconSvg = $icon && isset($icons[$icon]) ? $icons[$icon] : null;
@endphp

<a
    href="{{ $href }}"
    {{ $attributes->class("$baseClasses $variantClasses $sizeClasses") }}
    @if($target) target="{{ $target }}" @endif
    @if($tooltip) title="{{ $tooltip }}" @endif
>
    @if($iconSvg && $iconPosition !== 'right')
        {!! $iconSvg !!}
    @endif

    <span>{{ $slot }}</span>

    @if($iconSvg && $iconPosition === 'right')
        {!! $iconSvg !!}
    @endif
</a>