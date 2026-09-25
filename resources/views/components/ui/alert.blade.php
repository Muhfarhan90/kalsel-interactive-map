@props([
    'variant' => 'info',
    'title' => null,
    'message' => null,
    'showLink' => false,
    'linkHref' => '#',
    'linkText' => 'Selengkapnya',
])

@php
    $styles = [
        'success' => ['container' => 'border-success-200 bg-success-50 dark:border-success-500/30 dark:bg-success-500/10', 'icon' => 'text-success-600 dark:text-success-400', 'symbol' => '✓'],
        'error' => ['container' => 'border-error-200 bg-error-50 dark:border-error-500/30 dark:bg-error-500/10', 'icon' => 'text-error-600 dark:text-error-400', 'symbol' => '!'],
        'warning' => ['container' => 'border-warning-200 bg-warning-50 dark:border-warning-500/30 dark:bg-warning-500/10', 'icon' => 'text-warning-600 dark:text-warning-400', 'symbol' => '!'],
        'info' => ['container' => 'border-blue-light-200 bg-blue-light-50 dark:border-blue-light-500/30 dark:bg-blue-light-500/10', 'icon' => 'text-blue-light-600 dark:text-blue-light-400', 'symbol' => 'i'],
    ];
    $style = $styles[$variant] ?? $styles['info'];
    $role = $variant === 'error' ? 'alert' : 'status';
@endphp

<div {{ $attributes->class(['rounded-xl border p-4', $style['container']])->merge(['role' => $role, 'aria-live' => $role === 'alert' ? 'assertive' : 'polite']) }}>
    <div class="flex items-start gap-3">
        <span aria-hidden="true" class="grid size-6 shrink-0 place-items-center rounded-full font-bold {{ $style['icon'] }}">{{ $style['symbol'] }}</span>
        <div class="min-w-0 flex-1">
            @if ($title)
                <h4 class="mb-1 text-sm font-semibold text-gray-800 dark:text-white/90">{{ $title }}</h4>
            @endif

            @if ($message)
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $message }}</p>
            @endif

            {{ $slot }}

            @if ($showLink)
                <a href="{{ $linkHref }}" class="mt-3 inline-block text-sm font-medium underline">{{ $linkText }}</a>
            @endif
        </div>
    </div>
</div>
