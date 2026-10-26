@extends('layouts.app')

@php
    $editing = $regency->exists;
@endphp

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <a href="{{ route('admin.regencies.index') }}" class="text-sm font-semibold text-[#da251d] hover:underline">
                ← Kembali ke wilayah
            </a>

            <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">
                {{ $editing ? 'Edit wilayah' : 'Tambah wilayah' }}
            </h1>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Atur nama, koordinat, dan gambar peta wilayah.
            </p>
        </div>

        <form action="{{ $editing ? route('admin.regencies.update', $regency) : route('admin.regencies.store') }}"
            method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            @if ($editing)
                @method('PUT')
            @endif

            <x-common.component-card title="Informasi wilayah">
                <label for="regency-name" class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Nama wilayah
                </label>

                <input id="regency-name" name="name" type="text" value="{{ old('name', $regency->name) }}" required
                    maxlength="150"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 outline-none focus:border-[#da251d] dark:border-gray-700 dark:text-white">

                @error('name')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </x-common.component-card>

            <x-common.component-card title="Koordinat wilayah"
                desc="Tentukan titik pusat kabupaten/kota pada peta interaktif.">
                <div data-geo-picker data-regency-slug="{{ $regency->slug }}" class="space-y-4">

                    {{-- Peta persegi --}}



                    <div class="mx-auto w-full max-w-3xl space-y-3">

                        <!-- Pencarian lokasi -->
                        <div class="flex gap-2">
                            <input type="search" data-geo-search placeholder="Cari lokasi di Kalimantan Selatan..."
                                autocomplete="off"
                                class="h-11 min-w-0 flex-1 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 outline-none focus:border-red-500">

                            <button type="button" data-geo-search-button
                                class="rounded-lg bg-[#da251d] px-4 text-sm font-semibold text-white hover:bg-red-700">
                                Cari
                            </button>
                        </div>

                        <!-- Hasil pencarian -->
                        <select data-geo-search-results size="5" aria-label="Pilih hasil pencarian lokasi"
                            class="hidden w-full cursor-pointer rounded-lg border border-gray-300 bg-white p-2 text-sm text-gray-800 shadow-sm">
                        </select>

                        <p data-geo-search-message class="hidden text-sm text-gray-500">
                        </p>

                        <!-- Leaflet -->
                        <div data-geo-map
                            class="relative z-0 aspect-square w-full overflow-hidden rounded-lg border border-gray-300">
                        </div>

                    </div>




                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Klik peta atau geser marker untuk menentukan titik pusat wilayah.
                    </p>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label for="latitude">
                            <span class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Latitude
                            </span>

                            <input id="latitude" type="number" name="latitude"
                                value="{{ old('latitude', $regency->latitude) }}" step="any" min="-90"
                                max="90"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 dark:border-gray-700 dark:bg-gray-900 dark:text-white">

                            @error('latitude')
                                <span class="text-xs text-red-600">
                                    {{ $message }}
                                </span>
                            @enderror
                        </label>

                        <label for="longitude">
                            <span class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Longitude
                            </span>

                            <input id="longitude" type="number" name="longitude"
                                value="{{ old('longitude', $regency->longitude) }}" step="any" min="-180"
                                max="180"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 dark:border-gray-700 dark:bg-gray-900 dark:text-white">

                            @error('longitude')
                                <span class="text-xs text-red-600">
                                    {{ $message }}
                                </span>
                            @enderror
                        </label>
                    </div>
                </div>
            </x-common.component-card>

            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.regencies.index') }}"
                    class="inline-flex min-h-11 items-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700">
                    Batal
                </a>

                <button type="submit"
                    class="inline-flex min-h-11 items-center rounded-lg bg-[#da251d] px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700">
                    Simpan wilayah
                </button>
            </div>
        </form>
    </div>
@endsection
