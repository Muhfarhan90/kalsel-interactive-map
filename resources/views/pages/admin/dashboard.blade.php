@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="rounded-2xl bg-gradient-to-r from-[#a91612] to-[#da251d] p-6 text-white shadow-theme-lg md:p-8">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-white/70">Kalimantan Selatan</p>
            <div class="mt-3 flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold md:text-4xl">Kelola menu dan konten peta</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-white/80">Atur menu, kategori, lokasi, dan tampilan
                        homepage untuk papan digital interaktif.</p>
                </div>
                <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer"
                    aria-label="Buka halaman publik di tab baru"
                    class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-[#a91612] shadow-theme-xs hover:bg-red-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                    Buka halaman publik ↗
                </a>
            </div>
        </div>

        @include('components.common.flash-message')

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([['label' => 'Total menu', 'value' => $menuCount, 'description' => number_format($activeMenuCount, 0, ',', '.') . ' menu aktif di homepage', 'tone' => 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400'], ['label' => 'Kategori', 'value' => $categoryCount, 'description' => 'Kategori di seluruh menu', 'tone' => 'bg-blue-light-50 text-blue-light-700 dark:bg-blue-light-500/10 dark:text-blue-light-400'], ['label' => 'Total lokasi', 'value' => $locationCount, 'description' => 'Lokasi di seluruh menu', 'tone' => 'bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400'], ['label' => 'Lokasi aktif', 'value' => $activeLocationCount, 'description' => 'Status lokasi di seluruh menu', 'tone' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400']] as $stat)
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                    <span
                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $stat['tone'] }}">{{ $stat['label'] }}</span>
                    <p class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($stat['value'], 0, ',', '.') }}</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $stat['description'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            @foreach ([['route' => 'admin.menus.index', 'title' => 'Kelola menu', 'description' => 'Tambah menu, atur urutan, dan tentukan menu yang tampil di homepage.'], ['route' => 'admin.homepage.edit', 'title' => 'Pengaturan homepage', 'description' => 'Perbarui teks, warna, logo, dan gambar pada halaman utama.'], ['route' => 'admin.maps.edit', 'title' => 'Peta dasar', 'description' => 'Atur gambar peta yang digunakan untuk menempatkan marker lokasi.']] as $shortcut)
                <a href="{{ route($shortcut['route']) }}"
                    class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-sm transition hover:border-[#da251d] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#da251d] dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-base font-bold text-gray-900 dark:text-white">{{ $shortcut['title'] }}</h2>
                            <p class="mt-2 text-sm leading-6 text-gray-500 dark:text-gray-400">
                                {{ $shortcut['description'] }}</p>
                        </div>
                        <span class="text-xl text-[#da251d]" aria-hidden="true">→</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection
