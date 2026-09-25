@props([
    'isOpen' => false,
    'showCloseButton' => true,
    'panelClass' => 'max-w-lg p-6',
])

<div
    x-data="{ open: @js($isOpen) }"
    x-modelable="open"
    x-show="open"
    x-cloak
    x-effect="document.body.style.overflow = open ? 'hidden' : ''"
    @keydown.escape.window="open = false"
    role="dialog"
    aria-modal="true"
    :aria-hidden="!open"
    {{ $attributes->class(['fixed inset-0 z-[99999] flex items-center justify-center overflow-y-auto p-5']) }}
>
    <div
        aria-hidden="true"
        @click="open = false"
        class="fixed inset-0 bg-gray-400/50 backdrop-blur-sm"
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    ></div>

    <div
        @click.stop
        class="relative w-full rounded-2xl bg-white shadow-xl dark:bg-gray-900 {{ $panelClass }}"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-y-2 opacity-0 sm:scale-95"
        x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-y-0 opacity-100 sm:scale-100"
        x-transition:leave-end="translate-y-2 opacity-0 sm:scale-95"
    >
        @if ($showCloseButton)
            <button type="button" @click="open = false" aria-label="Tutup dialog" class="absolute right-3 top-3 inline-flex size-9 items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 dark:text-gray-400 dark:hover:bg-gray-800">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12M18 6 6 18" />
                </svg>
            </button>
        @endif

        {{ $slot }}
    </div>
</div>
