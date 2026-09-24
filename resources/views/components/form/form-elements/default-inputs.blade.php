@props([
    'label',
    'name',
    'type' => 'text',
    'value' => null,
    'help' => null,
    'id' => null,
])

@php($inputId = $id ?: $name)

<label for="{{ $inputId }}" class="block">
    <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ $label }}</span>
    <input
        id="{{ $inputId }}"
        type="{{ $type }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->class([
            'dark:bg-dark-900 h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800',
            'border-error-300 focus:border-error-300 dark:border-error-700' => $errors->has($name),
            'border-gray-300 dark:border-gray-700' => ! $errors->has($name),
        ]) }}
    >

    @error($name)
        <span class="mt-1 block text-xs text-error-600">{{ $message }}</span>
    @elseif ($help)
        <span class="mt-1 block text-xs text-gray-500 dark:text-gray-400">{{ $help }}</span>
    @enderror
</label>
