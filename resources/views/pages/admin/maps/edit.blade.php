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

            <x-common.component-card title="Deskripsi peta" desc="Keterangan umum untuk peta dasar Kalimantan Selatan.">
                <x-form.form-elements.text-area-inputs label="Deskripsi" name="map_description" :value="$map->map_description" rows="4" maxlength="2000" />
            </x-common.component-card>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-[#da251d] px-5 py-2.5 text-sm font-semibold text-white shadow-theme-xs hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-100">Simpan perubahan</button>
            </div>
        </form>
    </div>
@endsection
