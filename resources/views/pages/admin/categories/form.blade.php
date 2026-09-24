@extends('layouts.app')

@php
    $editing = $category->exists;
    $selectedIcon = old('category_icon', $category->heroiconName());
    $selectedColor = old('category_color', $category->category_color ?: '#da251d');
    $heroiconPath = base_path('vendor/blade-ui-kit/blade-heroicons/resources/svg');
    $selectedIconSvg = file_get_contents($heroiconPath.'/o-'.$selectedIcon.'.svg');
@endphp

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <a href="{{ route('admin.categories.index') }}" class="text-sm font-semibold text-[#da251d] hover:underline">← Kembali ke kategori</a>
            <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">{{ $editing ? 'Edit kategori' : 'Tambah kategori' }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Warna kategori digunakan pada pin peta, nomor lokasi, dan badge detail.</p>
        </div>

        @include('components.common.flash-message')

        <form action="{{ $editing ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="POST">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <x-common.component-card title="Informasi kategori" desc="Warna kategori digunakan pada marker, nomor lokasi, dan badge detail.">
                <div class="grid gap-5 md:grid-cols-2" x-data="{ icon: @js($selectedIcon), iconSvg: @js($selectedIconSvg), color: @js($selectedColor), pickerOpen: false, iconSearch: '' }">
                    <div class="md:col-span-2">
                        <x-form.form-elements.default-inputs label="Nama kategori" name="category_name" :value="$category->category_name" required maxlength="100" />
                    </div>

                    <label class="block">
                        <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Warna marker</span>
                        <div class="flex h-11 items-center gap-3 rounded-lg border border-gray-300 px-3 shadow-theme-xs dark:border-gray-700">
                            <input type="color" name="category_color" value="{{ $selectedColor }}" x-model="color" required class="size-7 cursor-pointer border-0 bg-transparent p-0">
                            <span class="text-xs text-gray-500">Pilih warna yang mudah dibedakan.</span>
                        </div>
                    </label>

                    <div class="relative" @click.outside="pickerOpen = false" @keydown.escape.window="pickerOpen = false">
                        <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Ikon kategori</span>
                        <input type="hidden" name="category_icon" :value="icon">
                        <button type="button" @click="pickerOpen = !pickerOpen" :aria-expanded="pickerOpen"
                            class="flex h-11 w-full items-center gap-3 rounded-lg border border-gray-300 bg-transparent px-3 text-left text-sm text-gray-700 shadow-theme-xs outline-none transition hover:bg-gray-50 focus:border-[#da251d] focus:ring-3 focus:ring-red-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
                            <span class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-gray-50 text-gray-500 dark:bg-white/[0.05] dark:text-gray-300 [&>svg]:size-5" :style="{ color: color }" x-html="iconSvg" aria-hidden="true"></span>
                            <span class="flex-1">Pilih ikon</span>
                            <x-heroicon-o-chevron-down class="size-4 text-gray-500 transition" x-bind:class="{ 'rotate-180': pickerOpen }" />
                        </button>

                        <div x-cloak x-show="pickerOpen" x-transition.origin.top.right
                            class="absolute right-0 z-30 mt-2 w-[min(24rem,calc(100vw-3rem))] rounded-xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-700 dark:bg-gray-900">
                            <label class="relative block">
                                <span class="sr-only">Cari ikon</span>
                                <x-heroicon-o-magnifying-glass class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-gray-400" />
                                <input type="search" x-model="iconSearch" placeholder="Cari ikon Heroicons..."
                                    class="h-10 w-full rounded-lg border border-gray-300 bg-transparent pl-9 pr-3 text-sm text-gray-800 outline-none focus:border-[#da251d] focus:ring-3 focus:ring-red-100 dark:border-gray-700 dark:text-white/90">
                            </label>
                            <div class="custom-scrollbar mt-3 grid max-h-72 grid-cols-6 gap-1.5 overflow-y-auto p-1">
                                @foreach ($iconOptions as $iconName => $iconLabel)
                                    <button type="button"
                                        x-show="iconSearch === '' || @js(strtolower($iconLabel)).includes(iconSearch.toLowerCase())"
                                        @click="icon = @js($iconName); iconSvg = $event.currentTarget.innerHTML; pickerOpen = false"
                                        :class="icon === @js($iconName) ? 'bg-red-50 text-[#da251d] ring-2 ring-[#da251d] dark:bg-red-500/10' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white'"
                                        class="flex aspect-square items-center justify-center rounded-lg transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#da251d] [&>svg]:size-5"
                                        title="{{ $iconLabel }}" aria-label="Pilih {{ $iconLabel }}">
                                        {!! file_get_contents($heroiconPath.'/o-'.$iconName.'.svg') !!}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        @error('category_icon')
                            <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <x-form.form-elements.text-area-inputs label="Deskripsi kategori" name="category_description" :value="$category->category_description" rows="4" required maxlength="1000" />
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end dark:border-gray-800">
                    <a href="{{ route('admin.categories.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Batal</a>
                    <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-[#da251d] px-5 py-2.5 text-sm font-semibold text-white shadow-theme-xs hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-100">
                        {{ $editing ? 'Simpan perubahan' : 'Tambah kategori' }}
                    </button>
                </div>
            </x-common.component-card>
        </form>
    </div>
@endsection
