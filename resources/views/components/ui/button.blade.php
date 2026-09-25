@props([
    'size' => 'md',
    'variant' => 'primary',
    'disabled' => false,
])

@php
    $sizes = [
        'sm' => 'min-h-9 px-3 py-2 text-xs',
        'md' => 'min-h-11 px-4 py-2.5 text-sm',
        'lg' => 'min-h-12 px-5 py-3 text-base',
    ];
    $variants = [
        'primary' => 'bg-[#da251d] text-white shadow-theme-xs hover:bg-red-700 focus-visible:outline-[#da251d]',
        'outline' => 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800',
        'danger' => 'border border-error-200 bg-white text-error-600 hover:bg-error-50 dark:border-error-500/30 dark:bg-gray-900 dark:text-error-400 dark:hover:bg-error-500/10',
        'ghost' => 'bg-transparent text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800',
    ];
@endphp

<button {{ $attributes->class([
    'inline-flex items-center justify-center gap-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
    $sizes[$size] ?? $sizes['md'],
    $variants[$variant] ?? $variants['primary'],
])->merge(['type' => 'button']) }} @disabled($disabled)>
    @isset($startIcon)
        <span class="inline-flex shrink-0 items-center">{{ $startIcon }}</span>
    @endisset

    <span>{{ $slot }}</span>

    @isset($endIcon)
        <span class="inline-flex shrink-0 items-center">{{ $endIcon }}</span>
    @endisset
</button>
