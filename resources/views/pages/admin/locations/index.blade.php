@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-[#da251d]">Konten peta</p>
                <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">Data wisata</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola lokasi, media, koordinat marker, dan status tampil pada papan digital.</p>
            </div>
            <a href="{{ route('admin.locations.create') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-[#da251d] px-4 py-2.5 text-sm font-semibold text-white shadow-theme-xs transition hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-100">
                <span class="text-lg leading-none">+</span>
                Tambah wisata
            </a>
        </div>

        @include('components.common.flash-message')

        <x-tables.table
            :paginator="$locations"
            :searchable="true"
            search-label="Cari nama atau alamat wisata"
            search-placeholder="Cari nama atau alamat wisata..."
            :categories="$categories"
            all-categories-label="Semua kategori"
            :search-value="$search"
            :category-value="$categoryId"
            apply-label="Terapkan"
            reset-label="Reset"
        >
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            <th class="px-5 py-3.5 text-left">Wisata</th>
                            <th class="px-5 py-3.5 text-left">Kategori</th>
                            <th class="px-5 py-3.5 text-left">Koordinat</th>
                            <th class="px-5 py-3.5 text-left">Status</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($locations as $location)
                            <tr class="text-sm text-gray-700 dark:text-gray-300">
                                <td class="px-5 py-4">
                                    <div class="min-w-72">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $location->location_name }}</p>
                                        <p class="mt-1 max-w-sm truncate text-xs text-gray-500">{{ $location->location_address }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-2 rounded-full bg-gray-50 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:bg-white/[0.05] dark:text-gray-300" style="color: {{ $location->tourism_category->category_color }}">
                                        <x-dynamic-component :component="'heroicon-o-'.$location->tourism_category->heroiconName()" class="size-4" />
                                        {{ $location->tourism_category->category_name }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 font-mono text-xs text-gray-500">
                                    X {{ number_format($location->coordinate_x, 2) }}<br>
                                    Y {{ number_format($location->coordinate_y, 2) }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $location->is_active ? 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                                        {{ $location->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.locations.edit', $location) }}" class="inline-flex min-h-10 items-center rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Edit</a>
                                        <form action="{{ route('admin.locations.destroy', $location) }}" method="POST" onsubmit="return confirm('Hapus data wisata ini?')">
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
                                    <p class="font-semibold text-gray-800 dark:text-white">Belum ada data wisata</p>
                                    <p class="mt-1 text-sm text-gray-500">Tambahkan lokasi pertama untuk menampilkannya pada peta.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
        </x-tables.table>
    </div>
@endsection
