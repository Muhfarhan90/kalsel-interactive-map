@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <p class="text-sm font-medium text-[#da251d]">Konten peta</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">Manajemen peta</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Atur identitas dan gambar utama yang digunakan pada peta wisata publik.</p>
        </div>

        @include('components.common.flash-message')

        <form action="{{ route('admin.map.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <x-common.component-card title="Header peta" desc="Atur tulisan pada header bagian atas peta publik.">
                <div class="grid gap-5 md:grid-cols-2">
                    <x-form.form-elements.default-inputs label="Judul header" name="header_title" :value="$map->header_title ?: 'Kalimantan Selatan'" required maxlength="255" />
                    <x-form.form-elements.default-inputs label="Subjudul header" name="header_sub_title" :value="$map->header_sub_title ?: 'Interactive Map Guidance'" required maxlength="255" />
                </div>
            </x-common.component-card>

            <x-common.component-card title="Informasi peta" desc="Judul, subjudul, dan deskripsi akan ditampilkan pada panel informasi publik.">
                <div class="grid gap-5 md:grid-cols-2">
                    <x-form.form-elements.default-inputs label="Judul peta" name="map_title" :value="$map->map_title" required maxlength="255" />
                    <x-form.form-elements.default-inputs label="Subjudul" name="map_sub_title" :value="$map->map_sub_title" maxlength="255" />
                    <div class="md:col-span-2">
                        <x-form.form-elements.text-area-inputs label="Deskripsi peta" name="map_description" :value="$map->map_description" rows="4" maxlength="2000" />
                    </div>
                </div>
            </x-common.component-card>

            <div class="grid gap-6 lg:grid-cols-2">
                <x-common.component-card title="Logo" desc="Format JPG, PNG, atau WebP. Maksimal 2 MB.">
                    @if ($map->map_logo)
                        <div class="relative mb-4 h-40 overflow-hidden rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                            <img src="{{ asset($map->map_logo) }}" alt="Logo peta saat ini" class="absolute inset-4 block" style="width:calc(100% - 2rem);height:calc(100% - 2rem);object-fit:contain">
                        </div>
                    @endif
                    <x-form.form-elements.file-input-example label="Ganti logo" name="map_logo_file" accept="image/jpeg,image/png,image/webp" help="Kosongkan jika logo tidak berubah." />
                </x-common.component-card>

                <x-common.component-card title="Gambar peta" desc="Format JPG, PNG, atau WebP. Maksimal 10 MB.">
                    @if ($map->map_image)
                        <div class="mb-4 grid aspect-square place-items-center overflow-hidden border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                            <img src="{{ asset($map->map_image) }}" alt="Gambar peta saat ini" class="size-full object-contain">
                        </div>
                    @endif
                    <x-form.form-elements.file-input-example label="Ganti gambar peta" name="map_image_file" accept="image/jpeg,image/png,image/webp" help="Gunakan gambar dengan batas wilayah yang sesuai dengan posisi marker." />
                </x-common.component-card>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-[#da251d] px-5 py-2.5 text-sm font-semibold text-white shadow-theme-xs hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-100">
                    Simpan perubahan
                </button>
            </div>
        </form>
    </div>
@endsection
