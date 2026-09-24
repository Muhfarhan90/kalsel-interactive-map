@props([
    'label',
    'name',
    'help' => null,
    'id' => null,
])

@php($inputId = $id ?: $name)

<label for="{{ $inputId }}" class="block">
    <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ $label }}</span>
    <input
        id="{{ $inputId }}"
        type="file"
        name="{{ $name }}"
        {{ $attributes->class([
            'h-11 w-full overflow-hidden rounded-lg border bg-transparent text-sm text-gray-500 shadow-theme-xs file:mr-5 file:h-full file:cursor-pointer file:border-0 file:border-r file:border-gray-200 file:bg-gray-50 file:px-3.5 file:text-sm file:text-gray-700 focus:border-brand-300 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:file:border-gray-800 dark:file:bg-white/[0.03] dark:file:text-gray-400',
            'border-error-300 dark:border-error-700' => $errors->has($name),
            'border-gray-300 dark:border-gray-700' => ! $errors->has($name),
        ]) }}
    >

    @error($name)
        <span class="mt-1 block text-xs text-error-600">{{ $message }}</span>
    @elseif ($help)
        <span class="mt-1 block text-xs text-gray-500 dark:text-gray-400">{{ $help }}</span>
    @enderror
</label>
