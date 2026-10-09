@extends('layouts.app')

@php
    $editing = $menu->exists;
    $selectedIcon = old('icon', $menu->icon ?: 'location-dot');
    $selectedColor = old('color', $menu->color ?: '#da251d');
    if (!array_key_exists($selectedIcon, $iconOptions)) {
        $selectedIcon = 'location-dot';
    }
@endphp

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <a href="{{ route('admin.menus.index') }}" class="text-sm font-semibold text-[#da251d] hover:underline">← Kembali
                ke menu</a>
            <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">{{ $editing ? 'Edit menu' : 'Tambah menu' }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Atur informasi menu yang ditampilkan pada aplikasi.</p>
        </div>

        @include('components.common.flash-message')

        <form action="{{ $editing ? route('admin.menus.update', $menu) : route('admin.menus.store') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <x-common.component-card title="Informasi menu" desc="Atur nama, warna, ikon, dan deskripsi menu.">
                <div class="grid gap-5 md:grid-cols-2" x-data="{ icon: @js($selectedIcon), color: @js($selectedColor), pickerOpen: false, iconSearch: '' }">
                    <div class="md:col-span-2">
                        <x-form.form-elements.default-inputs label="Nama menu" name="name" :value="old('name', $menu->name)" required
                            maxlength="100" />
                    </div>

                    <div>
                        <x-form.form-elements.default-inputs label="Title" name="title" :value="old('title', $menu->title)" required
                            maxlength="100" />
                    </div>

                    <div>
                        <x-form.form-elements.default-inputs label="Subtitle" name="sub_title" :value="old('sub_title', $menu->sub_title)" required
                            maxlength="100" />
                    </div>

                    <label class="block">
                        <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Warna menu</span>
                        <div
                            class="flex h-11 items-center gap-3 rounded-lg border border-gray-300 px-3 shadow-theme-xs dark:border-gray-700">
                            <input type="color" name="color" value="{{ $selectedColor }}" x-model="color" required
                                class="size-7 cursor-pointer border-0 bg-transparent p-0">
                            <span class="text-xs text-gray-500">Pilih warna menu.</span>
                        </div>
                    </label>

                    <div class="relative" @click.outside="pickerOpen = false" @keydown.escape.window="pickerOpen = false">
                        <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Ikon menu
                        </span>

                        <input type="hidden" name="icon" :value="icon">

                        <button type="button" @click="pickerOpen = !pickerOpen" :aria-expanded="pickerOpen"
                            class="flex h-11 w-full items-center gap-3 rounded-lg border border-gray-300 bg-transparent px-3 text-left text-sm text-gray-700 shadow-theme-xs outline-none transition hover:bg-gray-50 focus:border-[#da251d] focus:ring-3 focus:ring-red-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">

                            <span
                                class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-gray-50 text-gray-500 dark:bg-white/[0.05]">
                                <i class="fa-solid text-xl" :class="'fa-' + icon" aria-hidden="true"></i>
                            </span>

                            <span class="flex-1">Pilih ikon</span>

                            <i class="fa-solid fa-chevron-down text-sm text-gray-500 transition"
                                :class="{ 'rotate-180': pickerOpen }" aria-hidden="true"></i>
                        </button>

                        <div x-cloak x-show="pickerOpen" x-transition.origin.top.right
                            class="absolute right-0 z-30 mt-2 w-[min(24rem,calc(100vw-3rem))] rounded-xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-700 dark:bg-gray-900">

                            <label class="relative block">
                                <span class="sr-only">Cari ikon</span>

                                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
                                    aria-hidden="true"></i>

                                <input type="search" x-model="iconSearch" placeholder="Cari ikon Font Awesome..."
                                    class="h-10 w-full rounded-lg border border-gray-300 bg-transparent pl-9 pr-3 text-sm text-gray-800 outline-none focus:border-[#da251d] focus:ring-3 focus:ring-red-100 dark:border-gray-700 dark:text-white/90">
                            </label>

                            <div class="custom-scrollbar mt-3 grid max-h-72 grid-cols-6 gap-1.5 overflow-y-auto p-1">
                                @foreach ($iconOptions as $iconName => $iconLabel)
                                    <button type="button"
                                        x-show="iconSearch === '' || @js(strtolower($iconLabel)).includes(iconSearch.toLowerCase())"
                                        @click="icon = @js($iconName); pickerOpen = false"
                                        :class="icon === @js($iconName) ?
                                            'bg-red-50 text-[#da251d] ring-2 ring-[#da251d] dark:bg-red-500/10' :
                                            'text-gray-500 hover:bg-gray-100 hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white'"
                                        class="flex aspect-square items-center justify-center rounded-lg transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#da251d]"
                                        title="{{ $iconLabel }}" aria-label="Pilih {{ $iconLabel }}">
                                        <i class="fa-solid fa-{{ $iconName }} text-lg" aria-hidden="true"></i>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        @error('icon')
                            <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <x-form.form-elements.text-area-inputs label="Deskripsi menu" name="description" :value="$menu->description"
                            rows="4" />
                    </div>

                    <div class="border-t border-gray-100 pt-5 md:col-span-2 dark:border-gray-800">
                        <h3 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">Gambar menu</h3>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div class="min-w-0 rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                                <p class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">Logo menu</p>

                                @if ($menu->logo)
                                    <img src="{{ asset($menu->logo) }}" alt="Logo menu saat ini"
                                        class="mb-3 size-24 rounded-lg object-contain">

                                    <label class="mb-3 flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" name="remove_logo" value="1"
                                            @checked(old('remove_logo')) class="size-4 rounded border-gray-300">
                                        Hapus logo saat ini
                                    </label>
                                @endif

                                <x-form.form-elements.file-input-example label="Upload logo menu" name="logo"
                                    accept="image/jpeg,image/png,image/webp" help="JPG, PNG, atau WebP; maksimal 5 MB." />
                            </div>

                            <div class="min-w-0 rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                                <p class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">Banner menu</p>

                                @if ($menu->banner)
                                    <img src="{{ asset($menu->banner) }}" alt="Banner menu saat ini"
                                        class="mb-3 h-24 w-40 rounded-lg object-cover">

                                    <label class="mb-3 flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" name="remove_banner" value="1"
                                            @checked(old('remove_banner')) class="size-4 rounded border-gray-300">
                                        Hapus banner saat ini
                                    </label>
                                @endif

                                <x-form.form-elements.file-input-example label="Upload banner menu" name="banner"
                                    accept="image/jpeg,image/png,image/webp" help="JPG, PNG, atau WebP; maksimal 5 MB." />
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <x-form.form-elements.checkbox-component label="Tampilkan pada homepage" name="is_active"
                            :checked="$editing ? $menu->is_active : true" />
                    </div>
                </div>

                <div
                    class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end dark:border-gray-800">
                    <a href="{{ route('admin.menus.index') }}"
                        class="inline-flex min-h-11 items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Batal</a>
                    <button type="submit"
                        class="inline-flex min-h-11 items-center justify-center rounded-lg bg-[#da251d] px-5 py-2.5 text-sm font-semibold text-white shadow-theme-xs hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-100">
                        {{ $editing ? 'Simpan perubahan' : 'Tambah menu' }}
                    </button>
                </div>
            </x-common.component-card>
        </form>
    </div>
@endsection
