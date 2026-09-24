@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="rounded-2xl bg-gradient-to-r from-[#a91612] to-[#da251d] p-6 text-white shadow-theme-lg md:p-8">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-white/70">Kalimantan Selatan</p>
            <div class="mt-3 flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold md:text-4xl">Kelola peta wisata</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-white/80">Perbarui kategori, lokasi, media, dan posisi marker yang ditampilkan pada papan digital interaktif.</p>
                </div>
                <a href="{{ route('tourism') }}" target="_blank" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-[#a91612] shadow-theme-xs hover:bg-red-50">Buka peta publik ↗</a>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            @foreach ([
                ['label' => 'Kategori', 'value' => $categoryCount, 'tone' => 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400'],
                ['label' => 'Total wisata', 'value' => $locationCount, 'tone' => 'bg-blue-light-50 text-blue-light-700 dark:bg-blue-light-500/10 dark:text-blue-light-400'],
                ['label' => 'Tampil di peta', 'value' => $activeLocationCount, 'tone' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400'],
            ] as $stat)
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $stat['tone'] }}">{{ $stat['label'] }}</span>
                    <p class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <a href="{{ route('admin.locations.index') }}" class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm transition hover:border-[#da251d] dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">Data wisata</p>
                        <p class="mt-2 text-sm leading-6 text-gray-500 dark:text-gray-400">Tambah lokasi, unggah gambar atau MP4, dan tentukan marker langsung pada peta.</p>
                    </div>
                    <span class="text-2xl text-[#da251d]">→</span>
                </div>
            </a>
            <a href="{{ route('admin.categories.index') }}" class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm transition hover:border-[#da251d] dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">Kategori wisata</p>
                        <p class="mt-2 text-sm leading-6 text-gray-500 dark:text-gray-400">Kelompokkan destinasi dan atur warna yang membedakan setiap marker.</p>
                    </div>
                    <span class="text-2xl text-[#da251d]">→</span>
                </div>
            </a>
        </div>
    </div>
@endsection
