@props([
    'label',
    'name',
    'value' => null,
    'rows' => 5,
])

<div data-rich-text-editor>
    <x-form.form-elements.text-area-inputs :label="$label" :name="$name" :value="$value" :rows="$rows" {{ $attributes }} />
    <div data-rich-text-editor-surface class="hidden rounded-lg border border-gray-300 bg-white dark:border-gray-700 dark:bg-gray-900"></div>
</div>
