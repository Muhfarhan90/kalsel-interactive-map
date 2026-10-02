<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#183832">
    <title>Jelajah Kalimantan Selatan</title>
    @vite('resources/css/app.css')
</head>

<body class="h-[100svh] overflow-hidden bg-[#183832] font-outfit text-white antialiased">
    <x-public-page-header :settings="$pageHeader" background-color="#183832" />

    <main class="relative flex h-[calc(100svh-4rem)] items-center justify-center overflow-hidden px-4 py-5 sm:px-8 lg:px-12">
        <img src="{{ asset('images/home/menara-pandang.jpeg') }}" alt="" aria-hidden="true" class="absolute inset-0 size-full object-cover object-center">
        <div class="absolute inset-0 bg-[#102b29]/45" aria-hidden="true"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-[#102b29]/25 via-transparent to-[#102b29]/70" aria-hidden="true"></div>

        <section class="relative z-10 mx-auto flex w-full max-w-6xl flex-col items-center text-center">
            <p class="mb-2 flex items-center gap-3 text-xl font-semibold text-[#f4c76d] sm:mb-4 sm:text-2xl">
                <span class="h-px w-7 bg-[#f4c76d]" aria-hidden="true"></span>
                Portal Banua
                <span class="h-px w-7 bg-[#f4c76d]" aria-hidden="true"></span>
            </p>
            <h1 class="max-w-4xl text-[2.25rem] font-semibold leading-[1.08] tracking-tight sm:text-[3.5rem] lg:text-[4rem] capitalize">Satu tempat untuk mengenal Kalimantan Selatan</h1>
            <p class="mt-3 max-w-2xl text-base leading-6 text-white/85 sm:mt-4 sm:text-xl sm:leading-8">Temukan alam, budaya, rasa, dan karya lokal. Pilih cara menjelajah yang paling menarik untukmu.</p>

            <nav aria-label="Menu utama" class="relative isolate mt-6 grid w-full max-w-5xl grid-cols-3 overflow-hidden rounded-[1.65rem] border-2 border-white/85 shadow-[0_22px_50px_rgba(12,38,30,0.4)] sm:mt-9 sm:rounded-[2.5rem] sm:border-[3px]">
                <svg aria-hidden="true" class="pointer-events-none absolute inset-0 z-0 size-full" viewBox="0 0 1000 1000" preserveAspectRatio="none">
                    <path d="M0 0H363L300 1000H0Z" fill="#da251d" fill-opacity=".82" />
                    <path d="M363 0H701L637 1000H300Z" fill="#9a6507" fill-opacity=".82" />
                    <path d="M701 0H1000V1000H637Z" fill="#1f5da8" fill-opacity=".82" />
                    <path d="M363 0 300 1000M701 0 637 1000" fill="none" stroke="white" stroke-opacity=".85" stroke-width="3" vector-effect="non-scaling-stroke" />
                </svg>
                <a href="{{ route('tourism') }}" class="relative z-10 flex min-h-[9rem] min-w-0 flex-col items-center justify-center gap-2.5 px-1.5 py-4 text-center text-white focus-visible:z-20 focus-visible:outline-4 focus-visible:outline-offset-[-5px] focus-visible:outline-white sm:min-h-[14.5rem] sm:gap-0 sm:px-6 sm:py-6 lg:p-8">
                    <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-white/15 sm:size-12 sm:rounded-2xl">
                        <svg aria-hidden="true" class="size-5 sm:size-7" viewBox="0 0 24 24" fill="none"><path d="M12 21s7-6.1 7-12a7 7 0 1 0-14 0c0 5.9 7 12 7 12Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="9" r="2.3" stroke="currentColor" stroke-width="1.7"/></svg>
                    </span>
                    <span class="block text-base font-semibold sm:mt-6 sm:min-h-12 sm:text-2xl uppercase">Wisata</span>
                    <span class="mt-2 hidden max-w-[18rem] text-base leading-5 text-white/85 sm:block">Temukan destinasi dan tempat menarik di seluruh Kalsel.</span>
                </a>

                <article class="relative z-10 flex min-h-[9rem] min-w-0 flex-col items-center justify-center gap-2.5 px-1.5 py-4 text-center text-white sm:min-h-[14.5rem] sm:gap-0 sm:px-6 sm:py-6 lg:p-8">
                    <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-white/15 sm:size-12 sm:rounded-2xl">
                        <svg aria-hidden="true" class="size-5 sm:size-7" viewBox="0 0 24 24" fill="none"><path d="M5 9h14l-1 11H6L5 9Zm3 0V6a4 4 0 0 1 8 0v3m-8 4v.01M16 13v.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <span class="block text-base font-semibold sm:mt-6 sm:min-h-12 sm:text-2xl uppercase">Kuliner</span>
                    <span class="mt-2 hidden max-w-[18rem] text-base leading-5 text-white/85 sm:block">Kenali hidangan khas dan cita rasa Banua.</span>
                </article>

                <article class="relative z-10 flex min-h-[9rem] min-w-0 flex-col items-center justify-center gap-2.5 px-1.5 py-4 text-center text-white sm:min-h-[14.5rem] sm:gap-0 sm:px-6 sm:py-6 lg:p-8">
                    <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-white/15 sm:size-12 sm:rounded-2xl">
                        <svg aria-hidden="true" class="size-5 sm:size-7" viewBox="0 0 24 24" fill="none"><path d="M4 20V9l8-5 8 5v11H4Zm5 0v-6h6v6M8 10h.01M12 10h.01M16 10h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <span class="block max-w-[13rem] text-sm font-semibold leading-5 sm:mt-6 sm:min-h-12 sm:text-2xl sm:leading-7 uppercase">Industri</span>
                    <span class="mt-2 hidden max-w-[18rem] text-base leading-5 text-white/85 sm:block">Jelajahi produk unggulan dan karya lokal.</span>
                </article>
            </nav>
        </section>
    </main>
</body>

</html>
