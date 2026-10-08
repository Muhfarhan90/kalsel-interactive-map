@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <p class="text-sm font-medium text-[#da251d]">Pengaturan situs</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                Homepage
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Atur konten dan gambar latar halaman utama.
            </p>
        </div>

        @include('components.common.flash-message')

        <form action="{{ route('admin.homepage.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <x-common.component-card title="Konten header" desc="Atur judul, logo, teks header, dan warna situs.">
                <div class="grid gap-5 md:grid-cols-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Judul header
                        <input type="text" name="header_title" value="{{ old('header_title', $homepage->header_title) }}"
                            maxlength="255"
                            class="mt-1.5 min-h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 dark:border-gray-700 dark:text-white">
                        @error('header_title')
                            <span class="mt-1 block text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Logo teks header
                        <input type="text" name="header_text" value="{{ old('header_text', $homepage->header_text) }}"
                            maxlength="255"
                            class="mt-1.5 min-h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 dark:border-gray-700 dark:text-white">
                        @error('header_text')
                            <span class="mt-1 block text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </label>

                    <div>
                        <x-form.form-elements.file-input-example label="Logo header" name="header_logo"
                            accept="image/jpeg,image/png,image/webp" help="Kosongkan jika logo tidak berubah." />

                        @if ($homepage->header_logo)
                            <img src="{{ asset($homepage->header_logo) }}" alt="Logo header saat ini"
                                class="mt-3 h-20 max-w-full object-contain">
                        @endif
                    </div>

                    <div>
                        <label for="homepage_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Warna situs
                        </label>

                        <div
                            class="mt-1.5 flex h-11 items-center gap-3 rounded-lg border border-gray-300 px-3 shadow-theme-xs dark:border-gray-700">
                            <input type="color" id="homepage_color" name="color"
                                value="{{ old('color', $homepage->color) ?: '#da251d' }}"
                                class="size-7 cursor-pointer border-0 bg-transparent p-0">

                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                Pilih warna situs.
                            </span>
                        </div>

                        @error('color')
                            <span class="mt-1 block text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </x-common.component-card>

            <x-common.component-card title="Konten homepage" desc="Teks yang tampil di halaman utama.">
                <div class="grid gap-5 md:grid-cols-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Label
                        <input type="text" name="label" value="{{ old('label', $homepage->label) }}" maxlength="255"
                            required
                            class="mt-1.5 min-h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 dark:border-gray-700 dark:text-white">
                        @error('label')
                            <span class="mt-1 block text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Judul
                        <input type="text" name="title" value="{{ old('title', $homepage->title) }}" maxlength="255"
                            required
                            class="mt-1.5 min-h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 dark:border-gray-700 dark:text-white">
                        @error('title')
                            <span class="mt-1 block text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 md:col-span-2">
                        Deskripsi
                        <textarea name="description" rows="4" maxlength="255"
                            class="mt-1.5 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 dark:border-gray-700 dark:text-white">{{ old('description', $homepage->description) }}</textarea>
                        @error('description')
                            <span class="mt-1 block text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </label>
                </div>
            </x-common.component-card>

            <x-common.component-card title="Gambar latar" desc="Format JPG, PNG, atau WebP; maksimal 5 MB.">
                @if ($homepage->image)
                    <img src="{{ asset($homepage->image) }}" alt="Banner homepage"
                        class="mb-4 h-auto w-full rounded-lg object-contain">
                @endif

                <x-form.form-elements.file-input-example label="Ganti gambar latar" name="image"
                    accept="image/jpeg,image/png,image/webp" help="Kosongkan jika gambar tidak berubah." />

                @error('image')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </x-common.component-card>

            <div class="flex justify-end">
                <button type="submit"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg bg-[#da251d] px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700">
                    Simpan perubahan
                </button>
            </div>
        </form>
    </div>
@endsection
