@extends('layouts.app')

@php
    $editing = $location->exists;
    $indexUrl = route('admin.locations.index', ['menu' => $menu->slug]);
    $coordinateX = (float) old('x_location', $location->x_location ?? 50);
    $coordinateY = (float) old('y_location', $location->y_location ?? 50);
@endphp

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div>
            <a href="{{ $indexUrl }}" class="text-sm font-semibold text-[#da251d] hover:underline">← Kembali ke lokasi</a>
            <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">
                {{ $editing ? 'Edit lokasi' : 'Tambah lokasi' }} {{ $menu->name }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Lengkapi informasi lokasi dan pilih posisi markernya pada peta.</p>
        </div>

        @include('components.common.flash-message')

        @if ($categories->isEmpty())
            <div class="rounded-xl border border-orange-200 bg-orange-50 p-4 text-sm text-orange-800 dark:border-orange-500/30 dark:bg-orange-500/10 dark:text-orange-300">
                Tambahkan kategori {{ $menu->name }} sebelum membuat lokasi.
                <a href="{{ route('admin.categories.create', ['menu' => $menu->slug]) }}" class="font-semibold underline">Tambah kategori</a>
            </div>
        @endif

        <form
            action="{{ $editing
                ? route('admin.locations.update', ['location' => $location, 'menu' => $menu->slug])
                : route('admin.locations.store', ['menu' => $menu->slug]) }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6"
        >
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(380px,0.8fr)]">
                <x-common.component-card title="Informasi lokasi" desc="Data ini akan ditampilkan pada peta dan panel detail.">
                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <x-form.form-elements.default-inputs label="Nama lokasi" name="name" :value="$location->name" required maxlength="150" />
                        </div>

                        <div class="md:col-span-2">
                            <x-form.form-elements.select-inputs label="Kategori" name="category_id" required :disabled="$categories->isEmpty()">
                                <option value="">Pilih kategori {{ $menu->name }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((string) old('category_id', $location->category_id) === (string) $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </x-form.form-elements.select-inputs>
                        </div>

                        <div class="md:col-span-2">
                            <x-form.form-elements.default-inputs label="Alamat" name="address" :value="$location->address" maxlength="255" />
                        </div>

                        <div class="md:col-span-2">
                            <x-form.form-elements.text-area-inputs label="Deskripsi" name="description" :value="$location->description" rows="5" maxlength="5000" />
                        </div>

                        <div class="md:col-span-2">
                            @if ($location->media)
                                <div class="mb-3 space-y-3">
                                    @if (str_ends_with(strtolower($location->media), '.mp4'))
                                        <video src="{{ asset($location->media) }}" controls class="aspect-video w-full max-w-md rounded-lg bg-black object-contain"></video>
                                    @else
                                        <img src="{{ asset($location->media) }}" alt="Media {{ $location->name }}" class="aspect-video w-full max-w-md rounded-lg object-cover">
                                    @endif
                                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" name="remove_media" value="1" @checked(old('remove_media')) class="size-4 rounded border-gray-300 text-[#da251d]">
                                        Hapus media saat ini
                                    </label>
                                </div>
                            @endif
                            <x-form.form-elements.file-input-example
                                label="Foto atau video lokasi"
                                name="media_file"
                                accept="image/jpeg,image/png,image/webp,video/mp4"
                                help="JPG, PNG, WebP, atau MP4; maksimal 70 MB. Kosongkan saat edit jika tidak ingin mengganti."
                            />
                        </div>

                        <div class="md:col-span-2">
                            <x-form.form-elements.default-inputs
                                label="Sumber media"
                                name="source_media"
                                :value="$location->source_media"
                                maxlength="255"
                                placeholder="Nama kreator atau sumber foto/video"
                            />
                        </div>

                        <div class="md:col-span-2">
                            <x-form.form-elements.checkbox-component
                                label="Tampilkan pada peta"
                                name="is_active"
                                :checked="$editing ? $location->is_active : true"
                                help="Lokasi nonaktif tetap tersimpan di admin."
                            />
                        </div>
                    </div>
                </x-common.component-card>

                <x-common.component-card title="Posisi marker" desc="Klik peta atau isi koordinat X dan Y (0–100).">
                    <div
                        x-data="{
                            x: @js($coordinateX),
                            y: @js($coordinateY),
                            pick(event) {
                                const bounds = event.currentTarget.getBoundingClientRect();
                                this.x = Number(((event.clientX - bounds.left) / bounds.width * 100).toFixed(2));
                                this.y = Number(((event.clientY - bounds.top) / bounds.height * 100).toFixed(2));
                            }
                        }"
                        class="space-y-5"
                    >
                        <button type="button" class="relative block aspect-square w-full cursor-crosshair overflow-hidden rounded-lg border border-gray-200 bg-gray-50 p-0 dark:border-gray-700" @click="pick($event)" aria-label="Pilih posisi marker pada peta">
                            <img src="{{ asset($map->map_image ?: 'images/maps/peta_provinsi_kalsel.png') }}" alt="Peta dasar Kalimantan Selatan" class="size-full object-fill">
                            <span class="pointer-events-none absolute z-10 grid size-12 -translate-x-1/2 -translate-y-full place-items-end justify-items-center text-[#da251d] drop-shadow-md" :style="{ left: x + '%', top: y + '%' }">
                                <svg class="h-10 w-8" viewBox="0 0 36 46" aria-hidden="true">
                                    <path d="M18 1.5C9.2 1.5 2 8.4 2 16.9C2 29.1 18 44.5 18 44.5S34 29.1 34 16.9C34 8.4 26.8 1.5 18 1.5Z" fill="currentColor" stroke="white" stroke-width="2.5" />
                                    <circle cx="18" cy="17" r="6" fill="white" />
                                </svg>
                            </span>
                        </button>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="block">
                                <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Koordinat X</span>
                                <input type="number" name="x_location" x-model.number="x" required min="0" max="100" step="0.01" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 outline-none focus:border-[#da251d] dark:border-gray-700 dark:text-white/90">
                                @error('x_location') <span class="mt-1 block text-xs text-error-600">{{ $message }}</span> @enderror
                            </label>
                            <label class="block">
                                <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Koordinat Y</span>
                                <input type="number" name="y_location" x-model.number="y" required min="0" max="100" step="0.01" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 outline-none focus:border-[#da251d] dark:border-gray-700 dark:text-white/90">
                                @error('y_location') <span class="mt-1 block text-xs text-error-600">{{ $message }}</span> @enderror
                            </label>
                        </div>
                    </div>
                </x-common.component-card>
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ $indexUrl }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Batal</a>
                <button type="submit" @disabled($categories->isEmpty()) class="inline-flex min-h-11 items-center justify-center rounded-lg bg-[#da251d] px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50">
                    {{ $editing ? 'Simpan perubahan' : 'Tambah lokasi' }}
                </button>
            </div>
        </form>
    </div>
@endsection
