@props([
    'label',
    'name',
    'checked' => false,
    'help' => null,
    'id' => null,
])

@php($inputId = $id ?: $name)

<label for="{{ $inputId }}" class="flex min-h-11 items-center gap-3 rounded-lg border border-gray-200 px-4 dark:border-gray-800">
    <input type="hidden" name="{{ $name }}" value="0">
    <input
        id="{{ $inputId }}"
        type="checkbox"
        name="{{ $name }}"
        value="1"
        @checked((bool) old($name, $checked))
        {{ $attributes->class('size-5 rounded border-gray-300 text-[#da251d] focus:ring-red-200') }}
    >
    <span>
        <span class="block text-sm font-semibold text-gray-800 dark:text-white">{{ $label }}</span>
        @if ($help)
            <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $help }}</span>
        @endif
    </span>
</label>

@error($name)
    <span class="mt-1 block text-xs text-error-600">{{ $message }}</span>
@enderror
