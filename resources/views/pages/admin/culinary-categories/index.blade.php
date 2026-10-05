@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-[#da251d]">Konten kuliner</p>
                <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">Kategori kuliner</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Seret kategori ke posisi yang diinginkan, lalu simpan urutannya.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <form action="{{ route('admin.culinary-categories.reorder') }}" method="POST" data-category-sort-form hidden>
                    @csrf
                    <div data-order-inputs></div>
                    <button type="submit" disabled class="inline-flex min-h-11 items-center justify-center rounded-lg border border-[#da251d] px-4 py-2.5 text-sm font-semibold text-[#da251d] hover:bg-red-50">Simpan urutan</button>
                </form>
                <a href="{{ route('admin.culinary-categories.create') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-[#da251d] px-4 py-2.5 text-sm font-semibold text-white shadow-theme-xs transition hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-100">
                    <span class="text-lg leading-none">+</span>
                    Tambah kategori
                </a>
            </div>
        </div>

        @include('components.common.flash-message')

        <x-tables.table>
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    <th class="py-3.5 pl-5 pr-1"><span class="sr-only">Seret kategori</span></th>
                    <th class="px-5 py-3.5 text-left">Kategori</th>
                    <th class="px-5 py-3.5 text-left">Warna kategori</th>
                    <th class="px-5 py-3.5 text-left">Jumlah kuliner</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800" data-category-sort-list>
                @forelse ($categories as $category)
                    <tr class="text-sm text-gray-700 dark:text-gray-300" data-category-id="{{ $category->id }}">
                        <td class="py-4 pl-5 pr-1">
                            <button type="button" data-category-drag-handle aria-label="Seret {{ $category->category_name }} untuk mengubah urutan" title="Seret untuk mengurutkan" class="grid size-9 touch-none cursor-grab place-items-center rounded-lg border border-gray-300 text-gray-500 active:cursor-grabbing dark:border-gray-700 dark:text-gray-400">
                                <x-heroicon-o-bars-3 class="size-5" />
                            </button>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-start gap-3">
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-gray-50 text-gray-500 shadow-theme-xs dark:bg-white/[0.05] dark:text-gray-300" style="color: {{ $category->category_color ?: '#da251d' }}">
                                    <x-dynamic-component :component="'heroicon-o-'.$category->heroiconName()" class="size-5" />
                                </span>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $category->category_name }}</p>
                                    <p class="mt-1 max-w-xl text-xs leading-5 text-gray-500 dark:text-gray-400">{{ $category->category_description }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="inline-flex items-center gap-2 rounded-full bg-gray-50 px-3 py-1.5 dark:bg-gray-800">
                                <span class="size-3 rounded-full" style="background-color: {{ $category->category_color ?: '#da251d' }}"></span>
                                <span class="font-mono text-xs">{{ $category->category_color ?: '#da251d' }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full bg-blue-light-50 px-2.5 py-1 text-xs font-semibold text-blue-light-700 dark:bg-blue-light-500/10 dark:text-blue-light-400">
                                {{ $category->culinary_locations_count }} lokasi
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.culinary-categories.edit', $category) }}" class="inline-flex min-h-10 items-center rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Edit</a>
                                <form action="{{ route('admin.culinary-categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Hapus kategori kuliner ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex min-h-10 items-center rounded-lg border border-error-200 px-3 py-2 text-xs font-semibold text-error-600 transition hover:bg-error-50 dark:border-error-500/30 dark:hover:bg-error-500/10">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-14 text-center">
                            <p class="font-semibold text-gray-800 dark:text-white">Belum ada kategori kuliner</p>
                            <p class="mt-1 text-sm text-gray-500">Tambahkan kategori terlebih dahulu sebelum membuat data kuliner.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-tables.table>
    </div>
@endsection
