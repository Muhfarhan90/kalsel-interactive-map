@props(['image', 'text' => null])

<div class="relative mt-3 overflow-hidden rounded-lg border border-gray-200 shadow-sm" style="min-height:16rem">
    <img src="{{ asset($image) }}" alt="" aria-hidden="true" loading="lazy" class="absolute inset-0 size-full object-cover object-center">
    @if (filled($text))
        <div class="relative flex items-center px-4 py-3" style="min-height:16rem;background:linear-gradient(to right,rgba(0,0,0,.62),rgba(0,0,0,.18) 72%,transparent)">
            <p class="m-0 break-words text-lg font-bold leading-snug text-white" style="max-width:72%;text-shadow:0 2px 5px #000">{{ $text }}</p>
        </div>
    @endif
</div>
