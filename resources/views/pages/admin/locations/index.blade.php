@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-[#da251d]">Konten peta</p>
                <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">Lokasi {{ $menu->name }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola lokasi, kategori, media, dan posisi marker untuk menu ini.</p>
            </div>
            <a href="{{ route('admin.locations.create', ['menu' => $menu->slug]) }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-[#da251d] px-4 py-2.5 text-sm font-semibold text-white shadow-theme-xs transition hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-100">
                <span class="text-lg leading-none">+</span>
                Tambah lokasi
            </a>
        </div>

        @include('components.common.flash-message')

        <form action="{{ route('admin.locations.index') }}" method="GET" class="flex flex-col gap-3 rounded-xl border border-gray-200 bg-white p-4 sm:flex-row sm:items-end dark:border-gray-800 dark:bg-white/[0.03]">
            <input type="hidden" name="menu" value="{{ $menu->slug }}">
            <label class="min-w-0 flex-1">
                <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Cari lokasi</span>
                <input type="search" name="search" value="{{ $search }}" placeholder="Nama atau alamat lokasi..." class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 outline-none focus:border-[#da251d] dark:border-gray-700 dark:text-white/90">
            </label>
            <div class="min-w-48">
                <x-form.form-elements.select-inputs label="Kategori" name="category_id">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) $categoryId === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </x-form.form-elements.select-inputs>
            </div>
            <button type="submit" class="inline-flex h-11 items-center justify-center rounded-lg bg-[#da251d] px-4 text-sm font-semibold text-white hover:bg-red-700">Terapkan</button>
            @if ($search !== '' || $categoryId !== null)
                <a href="{{ route('admin.locations.index', ['menu' => $menu->slug]) }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-gray-300 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Reset</a>
            @endif
        </form>

        <x-tables.table :paginator="$locations">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    <th class="px-5 py-3.5">Lokasi</th>
                    <th class="px-5 py-3.5">Kategori</th>
                    <th class="px-5 py-3.5">Status</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($locations as $location)
                    <tr class="text-sm text-gray-700 dark:text-gray-300">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $location->name }}</p>
                            <p class="mt-1 max-w-sm truncate text-xs text-gray-500">{{ $location->address }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-2 rounded-full bg-gray-50 px-2.5 py-1 text-xs font-semibold dark:bg-white/[0.05]" style="color: {{ $location->category->color }}">
                                <i class="{{ $location->category->fontAwesomeClass() }}" aria-hidden="true"></i>
                                {{ $location->category->name }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $location->is_active ? 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                                {{ $location->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.locations.edit', ['location' => $location, 'menu' => $menu->slug]) }}" class="inline-flex min-h-10 items-center rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Edit</a>
                                <form action="{{ route('admin.locations.destroy', ['location' => $location, 'menu' => $menu->slug]) }}" method="POST" onsubmit="return confirm('Hapus lokasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex min-h-10 items-center rounded-lg border border-error-200 px-3 py-2 text-xs font-semibold text-error-600 hover:bg-error-50 dark:border-error-500/30">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-14 text-center">
                            <p class="font-semibold text-gray-800 dark:text-white">Belum ada lokasi</p>
                            <p class="mt-1 text-sm text-gray-500">Tambahkan lokasi pertama untuk menu {{ $menu->name }}.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-tables.table>
    </div>
@endsection
