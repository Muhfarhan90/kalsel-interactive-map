@props([
    'videoId',
    'aspectRatio' => '16:9',
    'title' => 'Video YouTube',
])

@php
    $aspectRatios = [
        '16:9' => 'aspect-video',
        '4:3' => 'aspect-[4/3]',
        '21:9' => 'aspect-[21/9]',
        '1:1' => 'aspect-square',
    ];
@endphp

<div {{ $attributes->class(['overflow-hidden rounded-lg', $aspectRatios[$aspectRatio] ?? $aspectRatios['16:9']]) }}>
    <iframe
        src="https://www.youtube.com/embed/{{ rawurlencode($videoId) }}"
        title="{{ $title }}"
        loading="lazy"
        referrerpolicy="strict-origin-when-cross-origin"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
        allowfullscreen
        class="size-full"
    ></iframe>
</div>
