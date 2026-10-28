@extends('layouts.app')

@php
    $editing = $location->exists;
    $indexUrl = route('admin.locations.index', ['menu' => $menu->slug]);
    $coordinateX = (float) old('x_location', $location->x_location ?? 50);
    $coordinateY = (float) old('y_location', $location->y_location ?? 50);
@endphp

@section('content')
    <div class="mx-auto w-full space-y-6">
        <div>
            <a href="{{ $indexUrl }}" class="text-sm font-semibold text-[#da251d] hover:underline">← Kembali ke lokasi</a>
            <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">
                {{ $editing ? 'Edit lokasi' : 'Tambah lokasi' }} {{ $menu->name }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Lengkapi informasi lokasi dan pilih posisi markernya
                pada peta.</p>
        </div>

        @include('components.common.flash-message')

        @if ($categories->isEmpty())
            <div
                class="rounded-xl border border-orange-200 bg-orange-50 p-4 text-sm text-orange-800 dark:border-orange-500/30 dark:bg-orange-500/10 dark:text-orange-300">
                Tambahkan kategori {{ $menu->name }} sebelum membuat lokasi.
                <a href="{{ route('admin.categories.create', ['menu' => $menu->slug]) }}"
                    class="font-semibold underline">Tambah kategori</a>
            </div>
        @endif

        <form
            action="{{ $editing
                ? route('admin.locations.update', ['location' => $location, 'menu' => $menu->slug])
                : route('admin.locations.store', ['menu' => $menu->slug]) }}"
            method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{
                isSubmitting: false,
                uploading: false,
                progress: 0,
                uploadError: '',
                uploadErrors: [],
                submitForm(form) {
                    if (this.isSubmitting) return;
            
                    const files = Array.from(
                        form.querySelector('#gallery_files')?.files ?? []
                    );
                    this.uploadError = '';
                    this.uploadErrors = [];
            
                    if (files.some(file => file.size > 70 * 1024 * 1024)) {
                        this.uploadError = 'Setiap file maksimal 70 MB.';
                        return;
                    }
            
                    this.isSubmitting = true;
            
                    // Jika tidak ada file baru, submit seperti biasa.
                    if (files.length === 0) {
                        form.submit();
                        return;
                    }
            
                    this.uploading = true;
                    this.progress = 0;
            
                    const request = new XMLHttpRequest();
                    request.open(form.method, form.action);
                    request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    request.setRequestHeader('Accept', 'application/json');
                    request.upload.addEventListener('progress', event => {
                        if (event.lengthComputable) {
                            this.progress = Math.round(event.loaded / event.total * 100);
                        }
                    });
                    request.addEventListener('load', () => {
                        let response = {};
                        try { response = JSON.parse(request.responseText); } catch {}
            
                        if (request.status >= 200 && request.status < 300 && response.redirect) {
                            window.location.assign(response.redirect);
                            return;
                        }
            
                        this.isSubmitting = false;
                        this.uploading = false;
                        this.uploadErrors = Object.values(response.errors || {}).flat();
                        this.uploadError = this.uploadErrors.length ?
                            'Periksa kembali data berikut:' :
                            `Upload gagal (HTTP ${request.status}). Coba lagi.`;
                    });
                    request.addEventListener('error', () => {
                        this.isSubmitting = false;
                        this.uploading = false;
                        this.uploadErrors = [];
                        this.uploadError = 'Upload gagal. Periksa koneksi lalu coba lagi.';
                    });
                    request.send(new FormData(form));
                }
            }"
            x-on:submit.prevent="submitForm($el)">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(380px,0.8fr)]">
                <x-common.component-card title="Informasi lokasi"
                    desc="Data ini akan ditampilkan pada peta dan panel detail.">
                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <x-form.form-elements.default-inputs label="Nama lokasi" name="name" :value="$location->name"
                                required maxlength="150" />
                        </div>

                        <div class="md:col-span-2">
                            <x-form.form-elements.select-inputs label="Kategori" name="category_id" required
                                :disabled="$categories->isEmpty()">
                                <option value="">Pilih kategori {{ $menu->name }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((string) old('category_id', $location->category_id) === (string) $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </x-form.form-elements.select-inputs>
                        </div>

                        <div class="md:col-span-2">
                            <x-form.form-elements.default-inputs label="Alamat" name="address" :value="$location->address"
                                maxlength="255" />
                        </div>

                        <div class="md:col-span-2">
                            <x-form.form-elements.rich-text-editor label="Deskripsi" name="description" :value="$location->description" />
                        </div>

                        <div class="md:col-span-2">
                            <x-form.form-elements.default-inputs label="Sumber media" name="source_media" :value="$location->source_media"
                                maxlength="255" placeholder="Nama kreator atau sumber foto/video" />
                        </div>

                        <div class="md:col-span-2">
                            <x-form.form-elements.checkbox-component label="Tampilkan pada peta" name="is_active"
                                :checked="$editing ? $location->is_active : true" help="Lokasi nonaktif tetap tersimpan di admin." />
                        </div>

                        <div class="md:col-span-2 space-y-4">
                            <div>
                                <label for="gallery_files" class="mb-2 block text-sm font-semibold">
                                    Tambah Foto atau Video
                                </label>

                                <input id="gallery_files" type="file" name="gallery_files[]" multiple
                                    accept="image/jpeg,image/png,image/webp,video/mp4"
                                    class="w-full rounded-lg border border-gray-300 p-3 text-sm dark:border-gray-700">

                                <p class="mt-2 text-xs text-gray-500">
                                    JPG, PNG, WebP, atau MP4. Maksimal 70 MB per file.
                                </p>

                                @error('gallery_files')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                @error('gallery_remove')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            @php
                                $gallery = $location->media_gallery ?: ($location->media ? [$location->media] : []);
                            @endphp

                            @if ($editing && count($gallery))
                                <div class="mb-3 flex items-center justify-between gap-3">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Atur urutan foto dan video lokasi.
                                    </p>

                                    <button type="button" data-location-gallery-toggle
                                        data-location-gallery-save-url="{{ route('admin.locations.gallery.reorder', $location) }}"
                                        aria-pressed="false"
                                        class="inline-flex items-center gap-2 rounded-lg
                                            border border-gray-300 bg-white px-3 py-2
                                            text-sm font-medium text-gray-700
                                            hover:bg-gray-50 dark:border-gray-700
                                            dark:bg-gray-800 dark:text-gray-200">

                                        <i class="fas fa-arrow-down-up-across-line"></i>
                                        <span data-location-gallery-toggle-text>Ubah Urutan</span>
                                    </button>
                                </div>

                                <div data-location-gallery-status role="status" aria-live="polite"
                                    class="hidden rounded-lg border px-4 py-3 text-sm font-medium shadow-sm">
                                </div>

                                <div data-location-gallery-sort class="grid grid-cols-2 gap-3 sm:grid-cols-3">

                                    @foreach ($gallery as $index => $path)
                                        @php
                                            $url = \Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])
                                                ? $path
                                                : asset(ltrim($path, '/'));

                                            $isVideo = (bool) preg_match('/\.mp4(?:\?.*)?$/i', $path);
                                        @endphp

                                        <div data-location-gallery-item
                                            class="relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">

                                            {{-- Area drag, hanya aktif ketika mode urut dinyalakan --}}
                                            <div data-location-gallery-overlay
                                                class="absolute inset-0 z-30 hidden cursor-grab
                                                    touch-none bg-black/10 active:cursor-grabbing">

                                                <span
                                                    class="pointer-events-none absolute left-1/2 top-1/2
                                                        -translate-x-1/2 -translate-y-1/2
                                                        whitespace-nowrap rounded-lg bg-gray-900/85
                                                        px-3 py-2 text-sm font-medium text-white">

                                                    <i class="fas fa-arrows-up-down-left-right mr-1"></i>
                                                    Geser Media
                                                </span>
                                            </div>

                                            {{-- Media --}}
                                            <div class="relative aspect-video bg-gray-100">
                                                <span data-location-gallery-number
                                                    class="absolute left-2 top-2 z-10 rounded bg-black/70 px-2 py-1 text-xs text-white">
                                                    {{ $index + 1 }}
                                                </span>

                                                @if ($isVideo)
                                                    <video src="{{ $url }}" controls
                                                        class="size-full object-contain"></video>
                                                @else
                                                    <img src="{{ $url }}" alt="Media {{ $index + 1 }}"
                                                        class="size-full object-cover">
                                                @endif
                                            </div>

                                            {{-- Menyimpan path media sesuai urutan card --}}
                                            <input type="hidden" name="gallery_order[]" value="{{ $path }}">

                                            {{-- Checkbox untuk menghapus media --}}
                                            <label class="flex items-center gap-2 p-3 text-sm text-red-600">
                                                <input type="checkbox" name="gallery_remove[]" value="{{ $path }}"
                                                    @checked(in_array($path, old('gallery_remove', []), true))>
                                                Hapus media
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div x-cloak x-show="uploadError"
                            class="md:col-span-2 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700"
                            role="alert">
                            <p x-text="uploadError"></p>
                            <ul x-show="uploadErrors.length" class="mt-2 list-inside list-disc">
                                <template x-for="(error, index) in uploadErrors" :key="index">
                                    <li x-text="error"></li>
                                </template>
                            </ul>
                        </div>

                        <div x-cloak x-show="uploading" class="md:col-span-2" role="status" aria-live="polite">
                            <p class="mb-2 text-sm text-gray-600 dark:text-gray-300"
                                x-text="`Mengunggah media: ${progress}%`"></p>
                            <div class="h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                                <div class="h-full bg-[#da251d] transition-all" :style="{ width: progress + '%' }"></div>
                            </div>
                        </div>
                    </div>
                </x-common.component-card>


                <x-common.component-card title="Koordinat lokasi"
                    desc="Pilih kabupaten/kota, kemudian cari lokasi atau tentukan titik langsung pada peta.">

                    <div data-geo-picker data-geojson-url="{{ asset('geojson/regency_kalsel.geojson') }}"
                        class="space-y-4">
                        {{-- Pilih Kabupaten/Kota --}}
                        <div>
                            <label for="regency_id"
                                class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Kabupaten/Kota
                            </label>

                            <select id="regency_id" name="regency_id"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">

                                <option value="">Pilih wilayah</option>

                                @foreach ($regencies as $regency)
                                    <option value="{{ $regency->id }}" data-latitude="{{ $regency->latitude }}"
                                        data-longitude="{{ $regency->longitude }}" data-slug="{{ $regency->slug }}"
                                        @selected(old('regency_id', $location->regency_id) == $regency->id)>
                                        {{ $regency->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('regency_id')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Pencarian dan Peta --}}
                        <div class="mx-auto w-full max-w-3xl space-y-3">
                            <div class="flex flex-wrap gap-2">
                                <input type="search" data-geo-search placeholder="Cari lokasi..." autocomplete="off"
                                    class="h-11 min-w-0 flex-1 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 outline-none focus:border-[#da251d]">

                                <button type="button" data-geo-search-button
                                    class="h-11 rounded-lg bg-[#da251d] px-4 text-sm font-semibold text-white hover:bg-red-700">
                                    Cari
                                </button>

                                <x-geo-search-dialog />
                            </div>

                            {{-- Hasil Pencarian --}}
                            <select data-geo-search-results size="5" aria-label="Pilih hasil pencarian lokasi"
                                class="hidden w-full cursor-pointer rounded-lg border border-gray-300 bg-white p-2 text-sm text-gray-800 shadow-sm">
                            </select>

                            <p data-geo-search-message class="hidden text-sm text-gray-500 dark:text-gray-400">
                            </p>

                            <p data-geo-map-status role="status" class="hidden text-xs text-gray-500 dark:text-gray-400">
                            </p>

                            {{-- Peta Leaflet --}}
                            {{-- Peta Leaflet --}}
                            <div data-geo-map
                                class="relative z-0 aspect-square w-full overflow-hidden rounded-lg border border-gray-300 dark:border-gray-700">
                            </div>
                        </div>

                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Cari lokasi, klik pada peta, atau geser marker untuk
                            menentukan koordinat.
                        </p>

                        {{-- Koordinat --}}
                        <div class="grid gap-4 sm:grid-cols-2">
                            <label>
                                <span class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Latitude
                                </span>

                                <input type="number" name="latitude" value="{{ old('latitude', $location->latitude) }}"
                                    step="any" min="-90" max="90" placeholder="-3.1234567"
                                    class="h-11 w-full rounded-lg border border-gray-300 px-3 dark:border-gray-700 dark:bg-gray-900 dark:text-white">

                                @error('latitude')
                                    <span class="text-xs text-red-600">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </label>

                            <label>
                                <span class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Longitude
                                </span>

                                <input type="number" name="longitude"
                                    value="{{ old('longitude', $location->longitude) }}" step="any" min="-180"
                                    max="180" placeholder="114.1234567"
                                    class="h-11 w-full rounded-lg border border-gray-300 px-3 dark:border-gray-700 dark:bg-gray-900 dark:text-white">

                                @error('longitude')
                                    <span class="text-xs text-red-600">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </label>
                        </div>
                    </div>
                </x-common.component-card>

            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ $indexUrl }}"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Batal</a>
                <button type="submit" @disabled($categories->isEmpty())
                    :disabled="isSubmitting || @js($categories->isEmpty())"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg bg-[#da251d] px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50">
                    <span x-show="!isSubmitting">{{ $editing ? 'Simpan perubahan' : 'Tambah lokasi' }}</span>
                    <span x-cloak x-show="isSubmitting"
                        x-text="uploading ? `Mengunggah ${progress}%` : 'Menyimpan…'"></span>
                </button>
            </div>
        </form>
    </div>
@endsection
