@extends('layouts.app')

@php $editing = $regency->exists; @endphp

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <a href="{{ route('admin.regencies.index') }}" class="text-sm font-semibold text-[#da251d] hover:underline">← Kembali ke wilayah</a>
            <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">{{ $editing ? 'Edit wilayah' : 'Tambah wilayah' }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Atur nama dan gambar peta wilayah.</p>
        </div>

        <form action="{{ $editing ? route('admin.regencies.update', $regency) : route('admin.regencies.store') }}"
            method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if ($editing) @method('PUT') @endif

            <x-common.component-card title="Informasi wilayah">
                <label for="regency-name" class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Nama wilayah</label>
                <input id="regency-name" name="name" type="text" value="{{ old('name', $regency->name) }}" required maxlength="150"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 outline-none focus:border-[#da251d] dark:border-gray-700 dark:text-white">
                @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                <p class="mt-2 text-xs text-gray-500">Slug dibuat otomatis dan tidak berubah saat nama diedit.</p>
                @if ($editing) <p class="mt-1 font-mono text-xs text-gray-500">{{ $regency->slug }}</p> @endif
            </x-common.component-card>

            <x-common.component-card title="Gambar peta" desc="Opsional. Format JPG, PNG, atau WebP; maksimal 10 MB.">
                @if ($regency->map_image)
                    <img src="{{ asset($regency->map_image) }}?v={{ $regency->updated_at?->timestamp }}" alt="Peta {{ $regency->name }}"
                        class="mx-auto mb-4 aspect-square w-full max-w-sm rounded-lg border border-gray-200 object-contain">
                @endif
                <label for="regency-map-image" class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $editing ? 'Ganti gambar' : 'Unggah gambar' }}</label>
                <input id="regency-map-image" name="map_image_file" type="file" accept="image/jpeg,image/png,image/webp"
                    class="block w-full rounded-lg border border-gray-300 p-2 text-sm dark:border-gray-700 dark:text-white">
                @error('map_image_file') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                <p class="mt-2 text-xs text-gray-500">Nama file mengikuti slug wilayah, misalnya kabupaten-kotabaru.png.</p>
            </x-common.component-card>

            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.regencies.index') }}" class="inline-flex min-h-11 items-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700">Batal</a>
                <button type="submit" class="inline-flex min-h-11 items-center rounded-lg bg-[#da251d] px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Simpan wilayah</button>
            </div>
        </form>
    </div>
@endsection
