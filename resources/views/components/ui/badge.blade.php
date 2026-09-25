@props([
    'variant' => 'light',
    'size' => 'md',
    'color' => 'primary',
])

@php
    $sizes = [
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-2.5 py-1 text-sm',
    ];
    $colors = [
        'primary' => ['light' => 'bg-red-50 text-[#da251d] dark:bg-red-500/10 dark:text-red-400', 'solid' => 'bg-[#da251d] text-white'],
        'success' => ['light' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400', 'solid' => 'bg-success-600 text-white'],
        'error' => ['light' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400', 'solid' => 'bg-error-600 text-white'],
        'warning' => ['light' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400', 'solid' => 'bg-warning-600 text-white'],
        'info' => ['light' => 'bg-blue-light-50 text-blue-light-700 dark:bg-blue-light-500/10 dark:text-blue-light-400', 'solid' => 'bg-blue-light-600 text-white'],
        'gray' => ['light' => 'bg-gray-100 text-gray-700 dark:bg-white/[0.05] dark:text-gray-300', 'solid' => 'bg-gray-700 text-white'],
    ];
    $style = $colors[$color][$variant] ?? $colors['primary']['light'];
@endphp

<span {{ $attributes->class(['inline-flex items-center justify-center gap-1 rounded-full font-medium', $sizes[$size] ?? $sizes['md'], $style]) }}>
    @isset($startIcon)
        <span class="inline-flex shrink-0 items-center">{{ $startIcon }}</span>
    @endisset

    {{ $slot }}

    @isset($endIcon)
        <span class="inline-flex shrink-0 items-center">{{ $endIcon }}</span>
    @endisset
</span>
