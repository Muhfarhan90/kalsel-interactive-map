<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#183832">
    <title>Jelajah Kalimantan Selatan</title>
    @vite('resources/css/app.css')
</head>

<body class="public-page h-[100svh] overflow-hidden bg-[#183832] font-outfit text-white antialiased">
    <x-public-page-header :settings="$pageHeader" background-color="#183832" />

    <main class="relative flex h-[calc(100svh-4rem)] items-center justify-center overflow-hidden px-4 py-5 sm:px-8 lg:px-12">
        <img src="{{ asset($homepage->image) }}" alt="" aria-hidden="true" class="absolute inset-0 size-full object-cover object-center">
        <div class="absolute inset-0 bg-[#102b29]/10" aria-hidden="true"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-[#102b29]/25 via-transparent to-[#102b29]/70" aria-hidden="true"></div>

        <section class="relative z-10 mx-auto flex w-full max-w-6xl flex-col items-center text-center">
            <p class="mb-2 flex items-center gap-3 text-xl font-semibold text-[#f4c76d] sm:mb-4 sm:text-2xl">
                <span class="h-px w-7 bg-[#f4c76d]" aria-hidden="true"></span>
                {{ $homepage->label }}
                <span class="h-px w-7 bg-[#f4c76d]" aria-hidden="true"></span>
            </p>
            <h1 class="max-w-4xl text-[2.25rem] font-semibold leading-[1.08] tracking-tight sm:text-[3.5rem] lg:text-[4rem] capitalize">{{ $homepage->title }}</h1>
            <p class="mt-3 max-w-2xl text-base leading-6 text-white/85 sm:mt-4 sm:text-xl sm:leading-8"> {{ $homepage->description }}</p>
            
            <nav aria-label="Menu utama" class="relative isolate mt-6 grid w-full max-w-7xl grid-cols-3 overflow-hidden rounded-[1.65rem] border-2 border-white/85 shadow-[0_22px_50px_rgba(12,38,30,0.4)] sm:mt-9 sm:rounded-[2.5rem] sm:border-[3px]">
                @foreach ($menus as $menu)
                    <a href="{{ route('menu', $menu->slug) }}" data-public-page-link class="relative z-10 flex min-h-[9rem] min-w-0 flex-col items-center justify-center gap-2.5 px-1.5 py-4 text-center text-white sm:min-h-[14.5rem] sm:gap-0 sm:px-6 sm:py-6 lg:p-8">
                        <div style="background-color: {{ $menu->color }}; opacity: 0.7;" class="absolute inset-0 z-0 transition-opacity duration-300 hover:opacity-100" aria-hidden="true">
                        </div>
                        <div style="background-color: black; opacity: 0.5;" class="absolute inset-0 z-0 transition-opacity duration-300 hover:opacity-100" aria-hidden="true">
                        </div>
                        <span class="z-1 flex size-9 shrink-0 items-center justify-center rounded-xl bg-white/15 sm:size-12 sm:rounded-2xl">
                            <i class="fas fa-{{ $menu->icon }}"></i>
                        </span>
                        <span class="z-1 block text-base font-semibold sm:mt-6 sm:min-h-12 sm:text-2xl uppercase">{{ $menu->name }}</span>
                        <span class="z-1 mt-2 hidden max-w-[18rem] text-base leading-5 text-white/85 sm:block">{{ $menu->description }}</span>
                    </a>
                @endforeach
                {{-- <svg aria-hidden="true" class="pointer-events-none absolute inset-0 z-0 size-full" viewBox="0 0 1000 1000" preserveAspectRatio="none">
                    @foreach ($menus as $menu)
                        <path d="" fill="#ff5733" fill-opacity=".82" />
                    @endforeach
                    <path d="M363 0 300 1000M701 0 637 1000" fill="none" stroke="white" stroke-opacity=".85" stroke-width="3" vector-effect="non-scaling-stroke" />
                </svg> --}}
                
                {{-- <a href="{{ route('tourism') }}" data-public-page-link class="relative z-10 flex min-h-[9rem] min-w-0 flex-col items-center justify-center gap-2.5 px-1.5 py-4 text-center text-white focus-visible:z-20 focus-visible:outline-4 focus-visible:outline-offset-[-5px] focus-visible:outline-white sm:min-h-[14.5rem] sm:gap-0 sm:px-6 sm:py-6 lg:p-8">
                    <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-white/15 sm:size-12 sm:rounded-2xl">
                        <svg aria-hidden="true" class="size-5 sm:size-7" viewBox="0 0 24 24" fill="none"><path d="M12 21s7-6.1 7-12a7 7 0 1 0-14 0c0 5.9 7 12 7 12Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="9" r="2.3" stroke="currentColor" stroke-width="1.7"/></svg>
                    </span>
                    <span class="block text-base font-semibold sm:mt-6 sm:min-h-12 sm:text-2xl uppercase">Wisata</span>
                    <span class="mt-2 hidden max-w-[18rem] text-base leading-5 text-white/85 sm:block">{{ $tourismMap?->map_background_text ?: 'Wisata' }}</span>
                </a>

                <a href="{{ route('culinary') }}" data-public-page-link class="relative z-10 flex min-h-[9rem] min-w-0 flex-col items-center justify-center gap-2.5 px-1.5 py-4 text-center text-white focus-visible:z-20 focus-visible:outline-4 focus-visible:outline-offset-[-5px] focus-visible:outline-white sm:min-h-[14.5rem] sm:gap-0 sm:px-6 sm:py-6 lg:p-8">
                    <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-white/15 sm:size-12 sm:rounded-2xl">
                        <svg aria-hidden="true" class="size-5 sm:size-7" viewBox="0 0 24 24" fill="none"><path d="M3 2v7c0 1.1.9 2 2 2h4c1.1 0 2-.9 2-2V2M7 2v20M21 15V2c-2 2-3 4-3 7v6h3Zm0 0v7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <span class="block text-base font-semibold sm:mt-6 sm:min-h-12 sm:text-2xl uppercase">Kuliner</span>
                    <span class="mt-2 hidden max-w-[18rem] text-base leading-5 text-white/85 sm:block">{{ $culinaryMap?->map_background_text ?: 'Kuliner' }}</span>
                </a>

                <a href="{{ route('transportation') }}" data-public-page-link class="relative z-10 flex min-h-[9rem] min-w-0 flex-col items-center justify-center gap-2.5 px-1.5 py-4 text-center text-white focus-visible:z-20 focus-visible:outline-4 focus-visible:outline-offset-[-5px] focus-visible:outline-white sm:min-h-[14.5rem] sm:gap-0 sm:px-6 sm:py-6 lg:p-8">
                    <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-white/15 sm:size-12 sm:rounded-2xl">
                        <svg aria-hidden="true" class="size-5 sm:size-7" viewBox="0 0 24 24" fill="none"><path d="M11 3v18M11 5H5L3 8l2 3h6m0-2h7l3 3-3 3h-7M7 21h9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <span class="block max-w-[13rem] text-sm font-semibold leading-5 sm:mt-6 sm:min-h-12 sm:text-2xl sm:leading-7 uppercase">Transportasi</span>
                    <span class="mt-2 hidden max-w-[18rem] text-base leading-5 text-white/85 sm:block">{{ $transportationMap?->map_background_text ?: 'Transportasi' }}</span>
                </a> --}}
            </nav>
        </section>
    </main>
</body>

</html>
