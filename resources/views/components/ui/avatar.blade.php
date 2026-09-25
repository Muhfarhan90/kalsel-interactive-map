@props([
    'src' => '',
    'alt' => 'Avatar',
    'size' => 'md',
    'status' => 'none',
])

@php
    $sizes = [
        'xs' => 'size-6',
        'sm' => 'size-8',
        'md' => 'size-10',
        'lg' => 'size-12',
        'xl' => 'size-14',
        '2xl' => 'size-16',
    ];
    $statuses = [
        'online' => 'bg-success-500',
        'offline' => 'bg-gray-400',
        'busy' => 'bg-warning-500',
    ];
@endphp

<span {{ $attributes->class(['relative inline-flex shrink-0 rounded-full', $sizes[$size] ?? $sizes['md']]) }}>
    <img src="{{ $src }}" alt="{{ $alt }}" class="size-full rounded-full object-cover">

    @if (isset($statuses[$status]))
        <span aria-label="{{ $status }}" class="absolute bottom-0 right-0 size-3 rounded-full border-2 border-white dark:border-gray-900 {{ $statuses[$status] }}"></span>
    @endif
</span>
