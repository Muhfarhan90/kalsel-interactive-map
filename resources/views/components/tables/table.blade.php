@props([
    'paginator' => null,
    'searchable' => false,
    'searchLabel' => 'Cari data',
    'searchPlaceholder' => 'Cari data...',
    'categories' => [],
    'categoryFilterLabel' => '',
    'allCategoriesLabel' => 'Semua kategori',
    'searchValue' => '',
    'categoryValue' => '',
    'applyLabel' => 'Terapkan',
    'resetLabel' => 'Reset',
])

<div {{ $attributes->class('overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]') }}>
    @if ($searchable || count($categories))
        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
            <form method="GET" action="{{ url()->current() }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                @if ($searchable)
                    <label class="min-w-0 flex-1">
                        <span class="sr-only">{{ $searchLabel }}</span>
                        <input type="search" name="search" value="{{ $searchValue }}" placeholder="{{ $searchPlaceholder }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                    </label>
                @endif

                @if (count($categories))
                    <x-form.form-elements.select-inputs :label="$categoryFilterLabel" name="category_id" class="min-w-48">
                        <option value="">{{ $allCategoriesLabel }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) $categoryValue === (string) $category->id)>{{ $category->category_name }}</option>
                        @endforeach
                    </x-form.form-elements.select-inputs>
                @endif

                <button type="submit" class="inline-flex h-11 items-center justify-center rounded-lg bg-[#da251d] px-4 text-sm font-semibold text-white transition hover:bg-red-700">{{ $applyLabel }}</button>

                @if ($searchValue !== '' || $categoryValue !== '')
                    <a href="{{ url()->current() }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-gray-300 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">{{ $resetLabel }}</a>
                @endif
            </form>
        </div>
    @endif

    <div class="max-w-full overflow-x-auto custom-scrollbar">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            {{ $slot }}
        </table>
    </div>

    @if ($paginator?->hasPages())
        <div class="border-t border-gray-200 px-5 py-4 dark:border-gray-800">
            {{ $paginator->links() }}
        </div>
    @endif
</div>
