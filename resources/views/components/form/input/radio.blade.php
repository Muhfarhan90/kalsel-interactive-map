@props([
    'id',
    'name',
    'value',
    'checked' => false,
    'label',
    'disabled' => false,
])

<label for="{{ $id }}" @class([
    'relative flex select-none items-center gap-3 text-sm font-medium',
    'cursor-not-allowed text-gray-300 dark:text-gray-600' => $disabled,
    'cursor-pointer text-gray-700 dark:text-gray-400' => ! $disabled,
])>
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="radio"
        value="{{ $value }}"
        @checked((string) old($name, $checked ? $value : null) === (string) $value)
        @disabled($disabled)
        {{ $attributes->class('peer sr-only') }}
    >
    <span class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px] border-gray-300 bg-transparent after:hidden after:h-2 after:w-2 after:rounded-full after:bg-white after:content-[''] peer-checked:border-brand-500 peer-checked:bg-brand-500 peer-checked:after:block peer-disabled:border-gray-200 peer-disabled:bg-gray-100 dark:border-gray-700 dark:peer-disabled:border-gray-700 dark:peer-disabled:bg-gray-700"></span>
    {{ $label }}
</label>
