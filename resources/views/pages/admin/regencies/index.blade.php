@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-[#da251d]">Pengaturan</p>
                <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">Wilayah</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola nama dan gambar peta kabupaten/kota.</p>
            </div>
            <a href="{{ route('admin.regencies.create') }}"
                class="inline-flex min-h-11 items-center justify-center rounded-lg bg-[#da251d] px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700">+ Tambah wilayah</a>
        </div>

        @include('components.common.flash-message')

        <x-tables.table>
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    <th class="px-5 py-3.5">Wilayah</th>
                    <th class="px-5 py-3.5">Slug</th>
                    <th class="px-5 py-3.5">Peta</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($regencies as $regency)
                    <tr class="text-sm text-gray-700 dark:text-gray-300">
                        <td class="px-5 py-4 font-semibold text-gray-900 dark:text-white">{{ $regency->name }}</td>
                        <td class="px-5 py-4 font-mono text-xs">{{ $regency->slug }}</td>
                        <td class="px-5 py-4">
                            @if ($regency->map_image)
                                <img src="{{ asset($regency->map_image) }}?v={{ $regency->updated_at?->timestamp }}" alt="Peta {{ $regency->name }}"
                                    class="size-14 rounded-lg border border-gray-200 bg-gray-50 object-contain">
                            @else
                                <span class="text-xs text-gray-500">Belum diunggah</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.regencies.edit', $regency) }}"
                                    class="inline-flex min-h-10 items-center rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Edit</a>
                                <form action="{{ route('admin.regencies.destroy', $regency) }}" method="POST" onsubmit="return confirm('Hapus wilayah ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex min-h-10 items-center rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-14 text-center text-sm text-gray-500">Belum ada wilayah. Tambahkan wilayah pertama.</td></tr>
                @endforelse
            </tbody>
        </x-tables.table>
    </div>
@endsection
