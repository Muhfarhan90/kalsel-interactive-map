@extends('layouts.app')

@php
    $editing = $location->exists;
    $coordinateX = (float) old('coordinate_x', $location->coordinate_x ?? 50);
    $coordinateY = (float) old('coordinate_y', $location->coordinate_y ?? 50);
@endphp

@section('content')
    <div class="space-y-6">
        <div>
            <a href="{{ route('admin.locations.index') }}" class="text-sm font-semibold text-[#da251d] hover:underline">← Kembali ke data wisata</a>
            <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">{{ $editing ? 'Edit data wisata' : 'Tambah data wisata' }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Isi informasi wisata dan sentuh peta untuk menentukan posisi pin.</p>
        </div>

        @include('components.common.flash-message')

        @if ($categories->isEmpty())
            <div class="rounded-xl border border-warning-200 bg-warning-50 px-4 py-3 text-sm text-warning-700">
                Tambahkan kategori terlebih dahulu sebelum membuat data wisata.
                <a href="{{ route('admin.categories.create') }}" class="font-semibold underline">Tambah kategori</a>
            </div>
        @endif

        @if ($editing && $location->location_media_url)
            <form id="delete-location-media-form" action="{{ route('admin.locations.media.destroy', $location) }}" method="POST" class="hidden" onsubmit="return confirm('Hapus media wisata ini?')">
                @csrf
                @method('DELETE')
            </form>
        @endif

        <form
            action="{{ $editing ? route('admin.locations.update', $location) : route('admin.locations.store') }}"
            method="POST"
            enctype="multipart/form-data"
            x-data="{
                isSubmitting: false,
                hasCategories: @js($categories->isNotEmpty()),
                maxMediaSize: 70 * 1024 * 1024,
                fileTooLarge: false,
                uploading: false,
                progress: 0,
                uploadError: '',
                uploadErrors: [],
                checkMedia(file) {
                    this.fileTooLarge = Boolean(file && file.size > this.maxMediaSize);
                    this.uploadError = this.fileTooLarge
                        ? `Ukuran file ${(file.size / 1024 / 1024).toFixed(1)} MB. Maksimal 70 MB.`
                        : '';
                    this.uploadErrors = [];
                    return !this.fileTooLarge;
                },
                upload(form) {
                    this.uploading = true;
                    this.uploadError = '';
                    this.uploadErrors = [];
                    const request = new XMLHttpRequest();
                    request.open(form.method, form.action);
                    request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    request.setRequestHeader('Accept', 'application/json');
                    request.upload.addEventListener('progress', event => {
                        if (event.lengthComputable) this.progress = Math.round(event.loaded / event.total * 100);
                    });
                    request.addEventListener('load', () => {
                        let response = {};
                        try { response = JSON.parse(request.responseText); } catch {}

                        if (request.status >= 200 && request.status < 300 && response.redirect) {
                            window.location.assign(response.redirect);
                            return;
                        }
                        this.uploading = false;
                        this.isSubmitting = false;
                        this.uploadErrors = Object.values(response.errors || {}).flat();
                        this.uploadError = this.uploadErrors.length
                            ? 'Periksa kembali data berikut:'
                            : `Upload gagal (HTTP ${request.status}). Periksa batas ukuran file dan coba lagi.`;
                    });
                    request.addEventListener('error', () => {
                        this.uploading = false;
                        this.isSubmitting = false;
                        this.uploadErrors = [];
                        this.uploadError = 'Upload gagal. Periksa koneksi lalu coba lagi.';
                    });
                    request.send(new FormData(form));
                }
            }"
            @submit="const file = $el.querySelector('input[name=media]')?.files[0]; if (!checkMedia(file)) { $event.preventDefault(); return; } isSubmitting = true; if (file) { $event.preventDefault(); upload($el) }"
            class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_minmax(420px,0.8fr)]"
        >
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <x-common.component-card title="Informasi wisata" desc="Data ini akan ditampilkan pada panel detail peta interaktif.">
                <div class="grid gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <x-form.form-elements.default-inputs label="Nama wisata" name="location_name" :value="$location->location_name" required maxlength="150" />
                    </div>

                    <div class="md:col-span-2">
                        <x-form.form-elements.select-inputs label="Kategori" name="category_id" required>
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) old('category_id', $location->category_id) === (string) $category->id)>{{ $category->category_name }}</option>
                            @endforeach
                        </x-form.form-elements.select-inputs>
                    </div>

                    <div class="md:col-span-2">
                        <x-form.form-elements.default-inputs label="Alamat" name="location_address" :value="$location->location_address" required maxlength="255" />
                    </div>

                    <div class="md:col-span-2">
                        <x-form.form-elements.rich-text-editor label="Deskripsi" name="location_description" :value="$location->location_description" required/>
                    </div>

                    <x-form.form-elements.file-input-example label="Media gambar atau MP4" name="media" accept="image/jpeg,image/png,image/webp,video/mp4" help="Maksimal 70 MB. Kosongkan saat edit jika media tidak berubah." x-on:change="checkMedia($event.target.files[0])" />

                    <div x-cloak x-show="uploadError" class="md:col-span-2 rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-700 dark:border-error-500/30 dark:bg-error-500/10 dark:text-error-400" role="alert">
                        <p x-text="uploadError"></p>
                        <ul x-show="uploadErrors.length" class="mt-2 list-disc space-y-1 pl-5">
                            <template x-for="(error, index) in uploadErrors" :key="index">
                                <li x-text="error"></li>
                            </template>
                        </ul>
                    </div>

                    <x-form.form-elements.default-inputs label="Sumber media" name="location_source_media" :value="$location->location_source_media" maxlength="255" placeholder="Nama pembuat atau URL sumber" />

                    <div class="md:col-span-2">
                        <x-form.form-elements.checkbox-component label="Tampilkan pada peta" name="is_active" :checked="$location->exists ? $location->is_active : true" help="Data nonaktif tetap tersimpan tetapi tidak muncul pada peta." />
                    </div>
                </div>
            </x-common.component-card>

            <div x-data="{ x: {{ $coordinateX }}, y: {{ $coordinateY }} }" class="space-y-5">
                <x-common.component-card title="Posisi marker" desc="Sentuh lokasi pada peta. Nilai X dan Y akan terisi otomatis.">
                    <button type="button" class="relative block aspect-square w-full overflow-hidden border border-gray-200 bg-gray-50 p-0 dark:border-gray-700" @click="const rect = $el.getBoundingClientRect(); x = Math.max(0, Math.min(100, (($event.clientX - rect.left) / rect.width) * 100)).toFixed(2); y = Math.max(0, Math.min(100, (($event.clientY - rect.top) / rect.height) * 100)).toFixed(2)">
                        <img src="{{ asset($map->map_image ?: 'images/maps/peta_provinsi_kalsel.png') }}" alt="Pilih posisi marker pada peta" class="size-full object-fill">
                        <span class="pointer-events-none absolute z-10 grid size-10 -translate-x-1/2 -translate-y-full place-items-end text-[#da251d] drop-shadow-md" :style="{ left: x + '%', top: y + '%' }">
                            <svg class="h-10 w-8" viewBox="0 0 36 46" aria-hidden="true">
                                <path d="M18 1.5C9.2 1.5 2 8.4 2 16.9C2 29.1 18 44.5 18 44.5S34 29.1 34 16.9C34 8.4 26.8 1.5 18 1.5Z" fill="currentColor" stroke="white" stroke-width="2.5"/>
                                <circle cx="18" cy="17" r="6" fill="white"/>
                            </svg>
                        </span>
                    </button>

                    <div class="mt-4 space-y-5">
                        @foreach (['coordinate_x' => ['Koordinat X', 'x'], 'coordinate_y' => ['Koordinat Y', 'y']] as $name => [$label, $model])
                            <label for="{{ $name }}" class="block">
                                <span class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ $label }}</span>
                                <input id="{{ $name }}" name="{{ $name }}" type="range" min="0" max="100" step="0.01" value="{{ $name === 'coordinate_x' ? $coordinateX : $coordinateY }}" x-model.number="{{ $model }}" required aria-label="{{ $label }}" class="h-2 w-full cursor-pointer accent-[#da251d]">
                                @error($name)
                                    <span class="mt-1 block text-xs text-error-600">{{ $message }}</span>
                                @enderror
                            </label>
                        @endforeach
                    </div>
                </x-common.component-card>

                @if ($location->location_media_url)
                    <x-common.component-card title="Media saat ini">
                        @if (str_ends_with(strtolower($location->location_media_url), '.mp4'))
                            <video src="{{ asset($location->location_media_url) }}" controls class="aspect-video w-full rounded-xl bg-black object-contain"></video>
                        @else
                            <img src="{{ asset($location->location_media_url) }}" alt="Media {{ $location->location_name }}" class="aspect-video w-full rounded-xl object-cover">
                        @endif
                        <button type="submit" form="delete-location-media-form" class="mt-4 inline-flex min-h-10 items-center justify-center rounded-lg border border-error-200 px-4 py-2 text-sm font-semibold text-error-600 hover:bg-error-50 dark:border-error-500/30 dark:hover:bg-error-500/10">
                            Hapus media
                        </button>
                    </x-common.component-card>
                @endif

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.locations.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">Batal</a>
                    <button type="submit" :disabled="isSubmitting || !hasCategories || fileTooLarge" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-[#da251d] px-5 py-2.5 text-sm font-semibold text-white shadow-theme-xs hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-100 disabled:cursor-not-allowed disabled:opacity-50">
                        <svg x-cloak x-show="isSubmitting" class="size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                        <span x-show="!isSubmitting">{{ $editing ? 'Simpan perubahan' : 'Tambah wisata' }}</span>
                        <span x-cloak x-show="isSubmitting" x-text="uploading ? `Menyimpan ${progress}%` : 'Menyimpan…'"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
