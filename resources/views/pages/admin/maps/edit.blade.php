@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <p class="text-sm font-medium text-[#da251d]">Pengaturan peta</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">Peta dasar</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gambar dan deskripsi peta dasar ini digunakan bersama oleh peta wisata, kuliner, dan transportasi.</p>
        </div>

        @include('components.common.flash-message')

        <form action="{{ route('admin.maps.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <x-common.component-card title="Gambar peta" desc="Format JPG, PNG, atau WebP. Maksimal 10 MB.">
                @if ($map->map_image)
                    <div class="mb-4 grid aspect-square max-h-full place-items-center overflow-hidden border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                        <img src="{{ asset($map->map_image) }}" alt="Gambar peta saat ini" class="size-full object-contain">
                    </div>
                @endif
                <x-form.form-elements.file-input-example label="Ganti gambar peta" name="map_image_file" accept="image/jpeg,image/png,image/webp" help="Gambar peta ini menjadi dasar posisi marker wisata dan kuliner." />
                @error('map_image_file') <p class="mt-2 text-xs text-error-500">{{ $message }}</p> @enderror
            </x-common.component-card>

            <x-common.component-card title="Deskripsi peta" desc="Keterangan umum untuk peta dasar Kalimantan Selatan.">
                <x-form.form-elements.text-area-inputs label="Deskripsi" name="map_description" :value="$map->map_description" rows="4" maxlength="2000" />
            </x-common.component-card>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-[#da251d] px-5 py-2.5 text-sm font-semibold text-white shadow-theme-xs hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-100">Simpan perubahan</button>
            </div>
        </form>
    </div>
@endsection
