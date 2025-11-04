@props([
    'status' => 'pending',
    'size' => 'md',
])

@php
    $normalized = strtolower((string) $status);

    $variants = [
        'pending' => [
            'container' => 'bg-amber-50 text-amber-700 border border-amber-200',
            'dot' => 'bg-amber-500',
        ],
        'confirmed' => [
            'container' => 'bg-blue-50 text-blue-700 border border-blue-200',
            'dot' => 'bg-blue-500',
        ],
        'completed' => [
            'container' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            'dot' => 'bg-emerald-500',
        ],
        'cancelled' => [
            'container' => 'bg-rose-50 text-rose-700 border border-rose-200',
            'dot' => 'bg-rose-500',
        ],
        'paid' => [
            'container' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            'dot' => 'bg-emerald-500',
        ],
        'unpaid' => [
            'container' => 'bg-rose-50 text-rose-700 border border-rose-200',
            'dot' => 'bg-rose-500',
        ],
        'partial' => [
            'container' => 'bg-amber-50 text-amber-700 border border-amber-200',
            'dot' => 'bg-amber-500',
        ],
        'overdue' => [
            'container' => 'bg-rose-50 text-rose-700 border border-rose-200',
            'dot' => 'bg-rose-500',
        ],
        'draft' => [
            'container' => 'bg-slate-50 text-slate-700 border border-slate-200',
            'dot' => 'bg-slate-400',
        ],
    ];

    $sizes = [
        'sm' => 'px-2.5 py-0.5 text-xs',
        'md' => 'px-3 py-1 text-sm',
        'lg' => 'px-3.5 py-1.5 text-sm font-medium',
    ];

    $variant = $variants[$normalized] ?? [
        'container' => 'bg-slate-50 text-slate-700 border border-slate-200',
        'dot' => 'bg-slate-400',
    ];

    $sizeClasses = $sizes[$size] ?? $sizes['md'];

    $label = ucwords(str_replace(['-', '_'], ' ', (string) $status));
@endphp

<span {{ $attributes->class([
    'inline-flex items-center gap-2 rounded-full font-medium leading-tight',
    $variant['container'],
    $sizeClasses,
]) }}>
    <span class="h-2 w-2 rounded-full {{ $variant['dot'] }}"></span>
    <span>{{ $label }}</span>
</span>