@props([
    'label',
    'name',
    'help' => null,
    'id' => null,
])

@php($inputId = $id ?: $name)

<label for="{{ $inputId }}" class="block">
    <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ $label }}</span>
    <div class="relative z-20 bg-transparent">
        <select
            id="{{ $inputId }}"
            name="{{ $name }}"
            {{ $attributes->class([
                'dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border bg-transparent bg-none py-2.5 pr-11 pl-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800',
                'border-error-300 focus:border-error-300 dark:border-error-700' => $errors->has($name),
                'border-gray-300 dark:border-gray-700' => ! $errors->has($name),
            ]) }}
        >
            {{ $slot }}
        </select>
        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </span>
    </div>

    @error($name)
        <span class="mt-1 block text-xs text-error-600">{{ $message }}</span>
    @elseif ($help)
        <span class="mt-1 block text-xs text-gray-500 dark:text-gray-400">{{ $help }}</span>
    @enderror
</label>
