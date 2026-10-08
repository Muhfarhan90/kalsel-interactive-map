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
    <x-public-page-header :settings="$homepage" background-color="{{ $homepage->color }}" />

    <main class="relative flex h-[calc(100svh-4rem)] items-center justify-center overflow-hidden px-4 py-5 sm:px-8 lg:px-12">
        <img src="{{ asset($homepage->image) }}" alt="" aria-hidden="true" class="absolute inset-0 size-full object-cover object-center">
        <div class="absolute inset-0 bg-[#102b29]/10" aria-hidden="true"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-[#102b29]/25 via-transparent to-[#102b29]/70" aria-hidden="true"></div>

        <section class="relative z-10 mx-auto flex w-full max-w-[90rem] flex-col items-center text-center">
            <p class="mb-2 flex items-center gap-3 text-xl font-semibold text-[#f4c76d] sm:mb-4 sm:text-2xl">
                <span class="h-px w-7 bg-[#f4c76d]" aria-hidden="true"></span>
                {{ $homepage->label }}
                <span class="h-px w-7 bg-[#f4c76d]" aria-hidden="true"></span>
            </p>
            <h1 class="max-w-4xl text-[2.25rem] font-semibold leading-[1.08] tracking-tight sm:text-[3.5rem] lg:text-[4rem] capitalize">{{ $homepage->title }}</h1>
            <p class="mt-3 max-w-2xl text-base leading-6 text-white/85 sm:mt-4 sm:text-xl sm:leading-8"> {{ $homepage->description }}</p>
            
            <nav aria-label="Menu utama" class="relative isolate mt-6 min-w-[90rem] max-w-[90rem] flex flex-wrap justify-center overflow-hidden rounded-[1.65rem] border-2 border-white/85 shadow-[0_22px_50px_rgba(12,38,30,0.4)] sm:mt-9 sm:rounded-[2.5rem] sm:border-[3px]">
                @foreach ($menus as $menu)
                    <a href="{{ route('menu', $menu->slug) }}" data-public-page-link class="relative z-10 flex min-h-[9rem] min-w-0 flex-col items-center gap-2.5 px-1.5 py-4 text-center text-white sm:min-h-[14.5rem] sm:gap-0 sm:px-6 sm:py-6 lg:p-8 basis-[16.666%] grow shrink-0">
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
            </nav>
        </section>
    </main>
</body>

</html>
