@props([
    'label',
    'name',
    'options' => [],
    'selected' => [],
    'placeholder' => 'Pilih data',
])

{{--
    Contoh data TailAdmin:
    options: [
        { value: 1, label: 'Option 1' },
        { value: 2, label: 'Option 2' },
        { value: 3, label: 'Option 3' }
    ]
--}}

<div>
    <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ $label }}</span>
    <div
        x-data="{
            open: false,
            options: @js(array_values($options)),
            selected: @js(array_values(old($name, $selected))),
            toggle(value) {
                this.selected = this.selected.includes(value)
                    ? this.selected.filter(item => item !== value)
                    : [...this.selected, value];
            }
        }"
        class="relative"
        @click.outside="open = false"
    >
        <template x-for="value in selected" :key="value">
            <input type="hidden" name="{{ $name }}[]" :value="value">
        </template>

        <button type="button" @click="open = !open" {{ $attributes->class('flex min-h-11 w-full gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-left shadow-theme-xs dark:border-gray-700 dark:bg-gray-900') }}>
            <span class="flex flex-1 flex-wrap items-center gap-2">
                <template x-for="value in selected" :key="value">
                    <span class="flex items-center rounded-full bg-gray-100 py-1 pr-2 pl-2.5 text-sm text-gray-800 dark:bg-gray-800 dark:text-white/90">
                        <span x-text="options.find(option => option.value === value)?.label ?? value"></span>
                        <span role="button" tabindex="0" class="ml-1 text-gray-500" @click.stop="toggle(value)" @keydown.enter.prevent.stop="toggle(value)">&times;</span>
                    </span>
                </template>
                <span x-show="selected.length === 0" class="text-sm text-gray-500 dark:text-gray-400">{{ $placeholder }}</span>
            </span>
            <svg class="mt-1.5 h-5 w-5 shrink-0 text-gray-500 dark:text-gray-400" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
            </svg>
        </button>

        <div x-cloak x-show="open" class="absolute z-50 mt-1 max-h-64 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
            <template x-for="option in options" :key="option.value">
                <button type="button" class="block w-full border-b border-gray-200 px-4 py-3 text-left text-sm text-gray-800 last:border-b-0 dark:border-gray-800 dark:text-white/90" @click="toggle(option.value)" x-text="option.label"></button>
            </template>
        </div>
    </div>

    @error($name)<span class="mt-1 block text-xs text-error-600">{{ $message }}</span>@enderror
</div>
