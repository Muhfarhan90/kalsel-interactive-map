@extends('layouts.app')

@php
    $editing = $location->exists;
    $coordinateX = (float) old('coordinate_x', $location->coordinate_x ?? 50);
    $coordinateY = (float) old('coordinate_y', $location->coordinate_y ?? 50);
@endphp

@section('content')
    <div class="space-y-6">
        <div>
            <a href="{{ route('admin.culinary-locations.index') }}" class="text-sm font-semibold text-[#da251d] hover:underline">← Kembali ke data kuliner</a>
            <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">{{ $editing ? 'Edit data kuliner' : 'Tambah data kuliner' }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Isi informasi kuliner lalu pilih posisi markernya pada peta.</p>
        </div>

        @include('components.common.flash-message')

        @if ($categories->isEmpty())
            <div class="rounded-xl border border-orange-200 bg-orange-50 p-4 text-sm text-orange-800 dark:border-orange-500/30 dark:bg-orange-500/10 dark:text-orange-300">
                Tambahkan kategori terlebih dahulu.
                <a href="{{ route('admin.culinary-categories.create') }}" class="font-semibold underline">Buat kategori kuliner</a>
            </div>
        @endif

        <form action="{{ $editing ? route('admin.culinary-locations.update', $location) : route('admin.culinary-locations.store') }}" method="POST" enctype="multipart/form-data"
            x-data="{
                isSubmitting: false, hasCategories: @js($categories->isNotEmpty()), uploading: false, progress: 0, fileTooLarge: false,
                uploadError: '', uploadErrors: [],
                checkMedia(file) {
                    this.fileTooLarge = Boolean(file && file.size > 70 * 1024 * 1024);
                    this.uploadError = this.fileTooLarge ? 'Ukuran file melebihi batas 70 MB.' : '';
                    this.uploadErrors = [];
                    return !this.fileTooLarge;
                },
                upload(form, hasFile) {
                    this.uploading = hasFile;
                    this.progress = 0;
                    this.uploadError = '';
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
                            : `Upload gagal (HTTP ${request.status}). Coba lagi.`;
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
            x-on:submit.prevent="const file = $el.querySelector('input[name=media]')?.files[0]; if (!checkMedia(file) || isSubmitting) return; isSubmitting = true; upload($el, Boolean(file))">
            @csrf
            @if ($editing) @method('PUT') @endif
            <div class="grid gap-6 lg:grid-cols-2">
                <x-common.component-card title="Informasi kuliner" desc="Informasi ini akan digunakan pada panel detail kuliner.">
                    <div class="space-y-5">
                        <label class="block">
                            <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nama kuliner</span>
                            <input type="text" name="location_name" value="{{ old('location_name', $location->location_name) }}" required maxlength="150" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 outline-none focus:border-[#da251d] focus:ring-3 focus:ring-red-100 dark:border-gray-700 dark:text-white/90">
                            @error('location_name') <span class="mt-1 block text-xs text-error-500">{{ $message }}</span> @enderror
                        </label>
                        <label class="block">
                            <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kategori</span>
                            <select name="category_id" required @disabled($categories->isEmpty()) class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 outline-none focus:border-[#da251d] focus:ring-3 focus:ring-red-100 disabled:opacity-60 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                <option value="">Pilih kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((string) old('category_id', $location->category_id) === (string) $category->id)>{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="mt-1 block text-xs text-error-500">{{ $message }}</span> @enderror
                        </label>
                        <label class="block">
                            <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Alamat/lokasi <span class="font-normal text-gray-400">(opsional)</span></span>
                            <input type="text" name="location_address" value="{{ old('location_address', $location->location_address) }}" maxlength="255" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 outline-none focus:border-[#da251d] focus:ring-3 focus:ring-red-100 dark:border-gray-700 dark:text-white/90">
                            @error('location_address') <span class="mt-1 block text-xs text-error-500">{{ $message }}</span> @enderror
                        </label>
                        <x-form.form-elements.rich-text-editor label="Deskripsi (opsional)" name="location_description" :value="$location->location_description" maxlength="5000" />
                        <label class="block">
                            <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Foto/video <span class="font-normal text-gray-400">(opsional, maks. 70 MB)</span></span>
                            <input type="file" name="media" accept="image/jpeg,image/png,image/webp,video/mp4" x-on:change="checkMedia($event.target.files[0])" class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm dark:border-gray-700">
                            @error('media') <span class="mt-1 block text-xs text-error-500">{{ $message }}</span> @enderror
                        </label>
                        <div x-cloak x-show="uploadError" class="rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-700 dark:border-error-500/30 dark:bg-error-500/10 dark:text-error-400" role="alert">
                            <p x-text="uploadError"></p>
                            <ul x-show="uploadErrors.length" class="mt-2 list-disc space-y-1 pl-5">
                                <template x-for="(error, index) in uploadErrors" :key="index"><li x-text="error"></li></template>
                            </ul>
                        </div>
                        <label class="block">
                            <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Sumber media <span class="font-normal text-gray-400">(opsional)</span></span>
                            <input type="text" name="location_source_media" value="{{ old('location_source_media', $location->location_source_media) }}" maxlength="255" placeholder="TikTok, Instagram, YouTube, atau nama kreator" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 outline-none focus:border-[#da251d] focus:ring-3 focus:ring-red-100 dark:border-gray-700 dark:text-white/90">
                            @error('location_source_media') <span class="mt-1 block text-xs text-error-500">{{ $message }}</span> @enderror
                        </label>
                        <label class="inline-flex items-center gap-3">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $location->exists ? $location->is_active : true)) class="size-4 rounded border-gray-300 text-[#da251d] focus:ring-[#da251d]">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Tampilkan sebagai data aktif</span>
                        </label>
                    </div>
                </x-common.component-card>

                <x-common.component-card title="Posisi pada peta" desc="Klik pada peta atau geser bar koordinat untuk menentukan titik lokasi kuliner.">
                    <div x-data="{
                        x: @js($coordinateX), y: @js($coordinateY),
                        pick(event) {
                            const bounds = event.currentTarget.getBoundingClientRect();
                            this.x = Number(((event.clientX - bounds.left) / bounds.width * 100).toFixed(2));
                            this.y = Number(((event.clientY - bounds.top) / bounds.height * 100).toFixed(2));
                        }
                    }">
                        <div class="relative mx-auto aspect-square w-full max-w-lg overflow-hidden rounded-lg bg-gray-50" @click="pick($event)">
                            <img src="{{ asset($map->map_image) }}" alt="Peta kuliner Kalimantan Selatan" class="absolute inset-0 size-full cursor-crosshair object-fill">
                            <span class="pointer-events-none absolute z-10 grid size-12 -translate-x-1/2 -translate-y-full place-items-end justify-items-center text-[#da251d]" :style="{ left: `${x}%`, top: `${y}%` }">
                                <svg class="h-10 w-8 drop-shadow" viewBox="0 0 36 46" aria-hidden="true"><path d="M18 1.5C9.2 1.5 2 8.4 2 16.9C2 29.1 18 44.5 18 44.5S34 29.1 34 16.9C34 8.4 26.8 1.5 18 1.5Z" fill="currentColor" stroke="white" stroke-width="2.5"/><circle cx="18" cy="17" r="6" fill="white"/></svg>
                            </span>
                        </div>
                        <div class="mt-4 space-y-5">
                            @foreach (['coordinate_x' => ['Koordinat X', 'x'], 'coordinate_y' => ['Koordinat Y', 'y']] as $name => [$label, $model])
                                <label for="{{ $name }}" class="block">
                                    <span class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ $label }}</span>
                                    <input id="{{ $name }}" name="{{ $name }}" type="range" min="0" max="100" step="0.01" value="{{ $name === 'coordinate_x' ? $coordinateX : $coordinateY }}" x-model.number="{{ $model }}" required aria-label="{{ $label }}" class="h-2 w-full cursor-pointer accent-[#da251d]">
                                    @error($name) <span class="mt-1 block text-xs text-error-500">{{ $message }}</span> @enderror
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @if ($location->location_media_url)
                        <div class="mt-5 border-t border-gray-100 pt-5 dark:border-gray-800">
                            <p class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Media saat ini</p>
                            @if (str_ends_with(strtolower($location->location_media_url), '.mp4'))
                                <video src="{{ asset($location->location_media_url) }}" controls class="aspect-video w-full rounded-xl bg-black object-contain"></video>
                            @else
                                <img src="{{ asset($location->location_media_url) }}" alt="Media {{ $location->location_name }}" class="aspect-video w-full rounded-xl object-cover">
                            @endif
                        </div>
                    @endif
                </x-common.component-card>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.culinary-locations.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">Batal</a>
                <button type="submit" :disabled="isSubmitting || !hasCategories || fileTooLarge" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-[#da251d] px-5 py-2.5 text-sm font-semibold text-white shadow-theme-xs hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50">
                    <svg x-cloak x-show="isSubmitting" class="size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" /></svg>
                    <span x-show="!isSubmitting">{{ $editing ? 'Simpan perubahan' : 'Tambah kuliner' }}</span>
                    <span x-cloak x-show="isSubmitting" x-text="uploading ? `Menyimpan ${progress}%` : 'Menyimpan…'"></span>
                </button>
            </div>
        </form>
    </div>
@endsection
