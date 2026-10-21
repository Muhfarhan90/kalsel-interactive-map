<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $menu->title ?: $menu->name }} - Kalimantan Selatan</title>
    <link rel="icon" type="image/png" href="{{ asset($homepage->header_logo) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="public-page m-0 h-[100dvh] overflow-hidden bg-gray-100 font-sans text-gray-800 max-[850px]:h-auto max-[850px]:min-h-screen max-[850px]:overflow-y-auto"
    style="--page-header-background: {{ $menu->color }}">
    <x-public-page-header :settings="$homepage" background-color="{{ $menu->color }}" />

    <main class="h-[calc(100vh-4rem)] w-full">
        <section class="grid h-full min-h-0 grid-cols-12 rounded-lg bg-white max-[850px]:h-auto max-[850px]:grid-cols-1">
            <div class="col-span-7 min-w-0 max-[850px]:col-span-1"
                style="background-color: color-mix(in srgb, var(--page-header-background) 14%, white)">
                <div class="p-3">
                    <div class="relative mx-auto aspect-square w-full max-w-[calc(100vh-6rem)] rounded-lg border-[6px] max-[850px]:max-w-[35dvh]"
                        style="border-color: var(--page-header-background)">
                        <img class="block size-full rounded-sm"
                            src="{{ asset($map?->map_image ?: 'images/maps/peta_provinsi_kalsel.png') }}"
                            alt="Peta Provinsi Kalimantan Selatan">
                        @foreach ($regencies as $regency)
                            @if ($regency->x_location !== null && $regency->y_location !== null)
                                <a href="{{ route('regencies.menu', ['regency' => $regency->slug, 'menu' => $menu->slug]) }}"
                                    data-public-page-link
                                    class="absolute z-10 grid size-12 -translate-x-1/2 -translate-y-full place-items-end justify-items-center text-[#da251d] drop-shadow-md focus-visible:outline-4 focus-visible:outline-offset-2 focus-visible:outline-red-300"
                                    style="left: {{ $regency->x_location }}%; top: {{ $regency->y_location }}%"
                                    title="{{ $loop->iteration }}. {{ $regency->name }}" aria-label="Buka peta {{ $regency->name }}, nomor {{ $loop->iteration }}">
                                    <svg class="h-10 w-8 overflow-visible" viewBox="0 0 36 46" aria-hidden="true">
                                        <path d="M18 1.5C9.2 1.5 2 8.4 2 16.9C2 29.1 18 44.5 18 44.5S34 29.1 34 16.9C34 8.4 26.8 1.5 18 1.5Z" fill="currentColor" stroke="white" stroke-width="2.5" />
                                        <circle cx="18" cy="17" r="8" fill="white" />
                                        <text x="18" y="20.5" text-anchor="middle" fill="currentColor" font-size="10" font-weight="700">{{ $loop->iteration }}</text>
                                    </svg>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <aside class="col-span-5 flex min-h-0 min-w-0 flex-col p-3 max-[850px]:col-span-1">
                <header class="shrink-0 border-b border-gray-200 pb-3">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex min-w-0 items-center gap-2.5">
                            <img class="h-auto w-16 shrink-0 object-contain md:w-28 max-[850px]:max-h-16"
                                src="{{ asset($menu->logo ?: 'images/logo/logo_kalsel.svg') }}"
                                alt="Logo {{ $menu->name }}">
                            <div class="min-w-0">
                                <h1 class="m-0 break-words text-2xl font-bold" style="color: {{ $menu->color }}">
                                    {{ $menu->title ?: $menu->name }}
                                </h1>
                                @if ($menu->sub_title)
                                    <p class="text-sm text-gray-500">{{ $menu->sub_title }}</p>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('home') }}" data-public-page-link
                            class="inline-flex min-h-10 shrink-0 items-center rounded-lg px-3 py-2 text-sm font-bold text-white shadow-sm hover:opacity-90 focus-visible:outline-4 focus-visible:outline-offset-2 focus-visible:outline-red-200"
                            style="background-color: var(--page-header-background)">Kembali</a>
                    </div>
                    <h2 class="mt-4 text-lg font-bold text-gray-800">Pilih kabupaten/kota</h2>
                </header>

                <nav aria-label="Daftar kabupaten dan kota"
                    class="min-h-0 flex-1 overflow-y-auto overscroll-contain pt-2 max-[850px]:overflow-visible">
                    <ul class="divide-y divide-gray-100">
                        @forelse ($regencies as $regency)
                            <li>
                                <a href="{{ route('regencies.menu', ['regency' => $regency->slug, 'menu' => $menu->slug]) }}"
                                    data-public-page-link
                                    class="flex min-h-12 items-center gap-3 rounded-lg px-3 py-2 text-lg font-semibold text-gray-700 hover:bg-red-50 hover:text-[#da251d] focus-visible:outline-2 focus-visible:outline-red-500">
                                    <span class="w-5 shrink-0 text-center font-bold text-[#da251d]" aria-hidden="true">{{ $loop->iteration }}</span>
                                    <span class="min-w-0 flex-1">{{ $regency->name }}</span>
                                    <span aria-hidden="true" class="text-gray-400">›</span>
                                </a>
                            </li>
                        @empty
                            <li class="p-4 text-sm text-gray-500">Belum ada kabupaten/kota yang tersedia.</li>
                        @endforelse
                    </ul>
                </nav>
            </aside>
        </section>
    </main>
</body>

</html>
