@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div>
            <p class="text-sm font-medium text-[#da251d]">Pengaturan situs</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">Header situs</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Satu pengaturan ini dipakai bersama pada seluruh halaman publik.</p>
        </div>

        @include('components.common.flash-message')

        <form action="{{ route('admin.headers.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <x-common.component-card title="Pengaturan header" desc="Judul, logo, dan tulisan di samping logo akan sama di semua halaman. Warna latar mengikuti masing-masing halaman.">
                <div class="grid gap-5 md:grid-cols-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Judul header
                        <input type="text" name="header_title" value="{{ old('header_title', $header->header_title) }}" maxlength="255" required class="mt-1.5 min-h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-theme-xs focus:border-[#da251d] focus:outline-none focus:ring-2 focus:ring-[#da251d]/15 dark:border-gray-700 dark:text-white">
                        @error('header_title')<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror
                    </label>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tulisan di samping logo
                        <input type="text" name="header_logo_text" value="{{ old('header_logo_text', $header->header_logo_text) }}" maxlength="255" class="mt-1.5 min-h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-theme-xs focus:border-[#da251d] focus:outline-none focus:ring-2 focus:ring-[#da251d]/15 dark:border-gray-700 dark:text-white">
                        @error('header_logo_text')<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror
                    </label>
                    <div class="block text-sm font-medium text-gray-700 dark:text-gray-300 md:col-span-2">
                        Logo header
                        <div class="mt-1.5 flex flex-col gap-3 sm:flex-row sm:items-center">
                            <div class="flex h-16 w-28 shrink-0 items-center justify-center rounded-lg bg-gray-50 dark:bg-gray-800">
                                @if ($header->header_logo)
                                    <img src="{{ asset($header->header_logo) }}" alt="Logo header saat ini" class="max-h-10 max-w-full object-contain">
                                @else
                                    <span class="text-xs font-normal text-gray-500">Belum ada logo</span>
                                @endif
                            </div>
                            <input type="file" name="header_logo_file" accept="image/jpeg,image/png,image/webp,image/svg+xml,.svg" aria-label="Ganti logo header" class="block min-h-11 min-w-0 w-full flex-1 rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-700 file:mr-3 file:rounded-md file:border-0 file:bg-gray-100 file:px-3 file:py-1 file:font-medium dark:border-gray-700 dark:text-gray-300 dark:file:bg-gray-800">
                        </div>
                        <span class="mt-1 block text-xs font-normal text-gray-500">Kosongkan jika logo tidak berubah. Maksimal 2 MB.</span>
                        @error('header_logo_file')<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror
                    </div>
                </div>
            </x-common.component-card>

            <div class="flex justify-end border-t border-gray-100 pt-5 dark:border-gray-800">
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-[#da251d] px-5 py-2.5 text-sm font-semibold text-white shadow-theme-xs hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-100">Simpan perubahan</button>
            </div>
        </form>
    </div>
@endsection
