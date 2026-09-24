@props([
    'id' => 'datepicker-'.uniqid(),
    'mode' => 'single',
    'defaultDate' => null,
    'label' => null,
    'placeholder' => 'Pilih tanggal',
    'name' => null,
    'dateFormat' => 'Y-m-d',
])

@php($selectedDate = $name ? old($name, $defaultDate) : $defaultDate)

<div
    x-data="{
        picker: null,
        init() {
            this.picker = flatpickr(this.$refs.input, {
                mode: @js($mode),
                static: true,
                monthSelectorType: 'static',
                dateFormat: @js($dateFormat),
                defaultDate: @js($selectedDate),
                onChange: (selectedDates, dateStr, instance) => this.$dispatch('date-change', { selectedDates, dateStr, instance })
            });
        }
    }"
    x-init="init()"
    x-destroy="picker?.destroy()"
>
    @if ($label)
        <label for="{{ $id }}" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ $label }}</label>
    @endif

    <div class="relative custom-datepicker">
        <input
            x-ref="input"
            id="{{ $id }}"
            type="text"
            name="{{ $name }}"
            placeholder="{{ $placeholder }}"
            autocomplete="off"
            {{ $attributes->class([
                'h-11 w-full appearance-none rounded-lg border bg-transparent py-2.5 pr-11 pl-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/20 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800',
                'border-error-300 dark:border-error-700' => $name && $errors->has($name),
                'border-gray-300 dark:border-gray-700' => ! $name || ! $errors->has($name),
            ]) }}
        >
        <span class="pointer-events-none absolute top-1/2 right-3.5 -translate-y-1/2 text-gray-500 dark:text-gray-400">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 2C8.41421 2 8.75 2.33579 8.75 2.75V3.75H15.25V2.75C15.25 2.33579 15.5858 2 16 2C16.4142 2 16.75 2.33579 16.75 2.75V3.75H18.5C19.7426 3.75 20.75 4.75736 20.75 6V19C20.75 20.2426 19.7426 21.25 18.5 21.25H5.5C4.25736 21.25 3.25 20.2426 3.25 19V6C3.25 4.75736 4.25736 3.75 5.5 3.75H7.25V2.75C7.25 2.33579 7.58579 2 8 2ZM4.75 9.75V19C4.75 19.4142 5.08579 19.75 5.5 19.75H18.5C18.9142 19.75 19.25 19.4142 19.25 19V9.75H4.75ZM5.5 5.25C5.08579 5.25 4.75 5.58579 4.75 6V8.25H19.25V6C19.25 5.58579 18.9142 5.25 18.5 5.25H5.5Z" fill="currentColor" />
            </svg>
        </span>
    </div>

    @if ($name)
        @error($name)<span class="mt-1 block text-xs text-error-600">{{ $message }}</span>@enderror
    @endif
</div>
