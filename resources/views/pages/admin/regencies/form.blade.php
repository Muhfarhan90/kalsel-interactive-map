@extends('layouts.app')

@php
    $editing = $regency->exists;
    $coordinateX = old('x_location', $regency->x_location);
    $coordinateY = old('y_location', $regency->y_location);
    $positionSet = $coordinateX !== null && $coordinateX !== '' && $coordinateY !== null && $coordinateY !== '';
@endphp

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <a href="{{ route('admin.regencies.index') }}" class="text-sm font-semibold text-[#da251d] hover:underline">← Kembali ke wilayah</a>
            <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">{{ $editing ? 'Edit wilayah' : 'Tambah wilayah' }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Atur nama, peta wilayah, dan posisi pin pada peta provinsi.</p>
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

            <x-common.component-card title="Posisi pada peta provinsi" desc="Klik peta atau geser bar koordinat untuk menempatkan pin wilayah.">
                <div x-data="{
                    x: @js(is_numeric($coordinateX) ? (float) $coordinateX : 50),
                    y: @js(is_numeric($coordinateY) ? (float) $coordinateY : 50),
                    positionSet: @js($positionSet),
                    pick(event) {
                        const bounds = event.currentTarget.getBoundingClientRect();
                        this.x = Number(((event.clientX - bounds.left) / bounds.width * 100).toFixed(2));
                        this.y = Number(((event.clientY - bounds.top) / bounds.height * 100).toFixed(2));
                        this.positionSet = true;
                    }
                }">
                    <div class="relative mx-auto aspect-square w-full max-w-3xl overflow-hidden rounded-lg bg-gray-50" @click="pick($event)">
                        <img src="{{ asset($map?->map_image ?: 'images/maps/peta_provinsi_kalsel.png') }}"
                            alt="Peta Provinsi Kalimantan Selatan" class="absolute inset-0 size-full cursor-crosshair object-fill">
                        <span x-show="positionSet"
                            :style="{ left: `${x}%`, top: `${y}%` }"
                            class="pointer-events-none absolute z-10 grid size-12 -translate-x-1/2 -translate-y-full place-items-end justify-items-center text-[#da251d]">
                            <svg class="h-10 w-8 drop-shadow" viewBox="0 0 36 46" aria-hidden="true">
                                <path d="M18 1.5C9.2 1.5 2 8.4 2 16.9C2 29.1 18 44.5 18 44.5S34 29.1 34 16.9C34 8.4 26.8 1.5 18 1.5Z" fill="currentColor" stroke="white" stroke-width="2.5" />
                                <circle cx="18" cy="17" r="6" fill="white" />
                            </svg>
                        </span>
                    </div>
                    <input type="hidden" name="x_location" :value="positionSet ? x : ''">
                    <input type="hidden" name="y_location" :value="positionSet ? y : ''">
                    <div class="mt-4 space-y-5">
                        @foreach (['x_location' => ['Koordinat X', 'x'], 'y_location' => ['Koordinat Y', 'y']] as $name => [$label, $model])
                            <label for="{{ $name }}" class="block">
                                <span class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ $label }}</span>
                                <input id="{{ $name }}" type="range" min="0" max="100" step="0.01"
                                    x-model.number="{{ $model }}" @input="positionSet = true"
                                    class="h-2 w-full cursor-pointer accent-[#da251d]">
                                @error($name) <span class="mt-1 block text-xs text-error-500">{{ $message }}</span> @enderror
                            </label>
                        @endforeach
                    </div>
                </div>
            </x-common.component-card>

            <x-common.component-card title="Gambar peta" desc="Opsional. Format JPG, PNG, atau WebP; maksimal 10 MB.">
                @if ($regency->map_image)
                    <img src="{{ asset($regency->map_image) }}?v={{ $regency->updated_at?->timestamp }}" alt="Peta {{ $regency->name }}"
                        class="mx-auto mb-4 aspect-square w-full max-w-3xl rounded-lg border border-gray-200 object-contain">
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
