@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <p class="text-sm font-medium text-[#da251d]">Pengaturan peta</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">Peta Wisata</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Atur judul, subjudul, logo, dan gambar latar bawah. Gambar serta deskripsi peta diatur pada Peta Dasar.</p>
        </div>

        @include('components.common.flash-message')

        <form action="{{ route('admin.tourism-map.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <x-common.component-card title="Identitas peta wisata" desc="Identitas ini tampil pada panel informasi wisata.">
                <div class="grid gap-5 md:grid-cols-2">
                    <x-form.form-elements.default-inputs label="Judul peta" name="map_title" :value="$map->map_title" required maxlength="255" />
                    <x-form.form-elements.default-inputs label="Subjudul" name="map_sub_title" :value="$map->map_sub_title" maxlength="255" />
                </div>
            </x-common.component-card>

            <x-common.component-card title="Logo peta wisata" desc="Format JPG, PNG, atau WebP. Maksimal 2 MB.">
                @if ($map->map_logo)
                    <div class="relative mb-4 h-40 overflow-hidden rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <img src="{{ asset($map->map_logo) }}" alt="Logo peta wisata saat ini" class="absolute inset-4 block" style="width:calc(100% - 2rem);height:calc(100% - 2rem);object-fit:contain">
                    </div>
                @endif
                <x-form.form-elements.file-input-example label="Ganti logo" name="map_logo_file" accept="image/jpeg,image/png,image/webp" help="Kosongkan jika logo tidak berubah." />
                @error('map_logo_file') <p class="mt-2 text-xs text-error-500">{{ $message }}</p> @enderror
            </x-common.component-card>

            <x-common.component-card title="Gambar latar bawah" desc="Tampil di bawah daftar kategori pada halaman wisata.">
                @if ($map->map_background_image)
                    <div class="mb-4 flex flex-wrap items-center gap-4">
                        <img src="{{ asset($map->map_background_image) }}" alt="Gambar latar bawah saat ini" class="h-28 w-72 rounded-lg object-cover">
                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" name="remove_map_background_image" value="1" @checked(old('remove_map_background_image')) class="size-4 rounded border-gray-300 text-[#da251d]">
                            Hapus gambar saat ini
                        </label>
                    </div>
                @endif
                <x-form.form-elements.file-input-example label="Gambar latar bawah" name="map_background_image_file" accept="image/jpeg,image/png,image/webp" help="Gunakan gambar horizontal. JPG, PNG, atau WebP; maksimal 10 MB." />
                @error('map_background_image_file') <p class="mt-2 text-xs text-error-500">{{ $message }}</p> @enderror
                <div class="mt-4">
                    <x-form.form-elements.default-inputs label="Teks di atas gambar" name="map_background_text" :value="$map->map_background_text" maxlength="150" help="Tampil pada gambar latar dan menu halaman depan. Jika kosong, teks bawaan digunakan." />
                </div>
            </x-common.component-card>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-[#da251d] px-5 py-2.5 text-sm font-semibold text-white shadow-theme-xs hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-100">
                    Simpan perubahan
                </button>
            </div>
        </form>
    </div>
@endsection
