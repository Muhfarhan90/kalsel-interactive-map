<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $menu->title ?: $menu->name }} - Kalimantan Selatan</title>
    <link rel="icon" type="image/png" href="{{ asset($homepage->header_logo) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="public-page m-0 h-[100dvh] overflow-hidden bg-gray-100 font-sans text-gray-800 max-[850px]:h-auto max-[850px]:min-h-screen max-[850px]:overflow-y-auto"
    style="--page-header-background: {{ $menu->color }}">
    <x-public-page-header :settings="$homepage" background-color="{{ $menu->color }}" />

    <main class="h-[calc(100vh-4rem)] w-full">
        <section
            class="grid h-full min-h-0 grid-cols-12 rounded-lg bg-white max-[850px]:h-auto max-[850px]:grid-cols-1">
            <div class="col-span-7 min-w-0 max-[850px]:col-span-1"
                style="background-color: color-mix(in srgb, var(--page-header-background) 14%, white)">
                <div class="p-3">
                    <div class="relative mx-auto aspect-square w-full max-w-[calc(100vh-6rem)] overflow-hidden rounded-lg border-[6px] max-[850px]:max-w-[35dvh]"
                        style="border-color: var(--page-header-background)" data-map-container>
                        <div class="absolute inset-0 cursor-grab touch-none select-none active:cursor-grabbing"
                            style="--map-scale: 1; --map-inverse-scale: 1; transform: translate3d(0, 0, 0) scale(1); transform-origin: center;"
                            data-map-layer>
                            <img class="block size-full rounded-sm pointer-events-none"
                                src="{{ asset($map?->map_image ?: 'images/maps/peta_provinsi_kalsel.png') }}"
                                alt="Peta Provinsi Kalimantan Selatan">
                            @foreach ($regencies as $regency)
                                @if ($regency->x_location !== null && $regency->y_location !== null)
                                    <button type="button"
                                        data-marker-url="{{ route('regencies.menu', ['regency' => $regency->slug, 'menu' => $menu->slug]) }}"
                                        class="absolute z-10 inline-flex h-fit w-fit cursor-pointer -translate-x-1/2 -translate-y-full border-0 bg-transparent p-0 text-[#da251d] drop-shadow-md focus-visible:outline-4 focus-visible:outline-offset-2 focus-visible:outline-red-300"
                                        style="left: {{ $regency->x_location }}%; right: auto; top: {{ $regency->y_location }}%"
                                        title="{{ $loop->iteration }}. {{ $regency->name }}"
                                        aria-label="Buka peta {{ $regency->name }}, nomor {{ $loop->iteration }}">
                                        <svg draggable="false"
                                            class="pointer-events-none h-auto w-8 overflow-visible lg:w-10 2xl:w-12"
                                            style="transform: scale(var(--map-inverse-scale)); transform-origin: center bottom"
                                            viewBox="0 0 36 46" aria-hidden="true">
                                            <path
                                                d="M18 1.5C9.2 1.5 2 8.4 2 16.9C2 29.1 18 44.5 18 44.5S34 29.1 34 16.9C34 8.4 26.8 1.5 18 1.5Z"
                                                fill="currentColor" stroke="white" stroke-width="2.5" />
                                            <circle cx="18" cy="17" r="8" fill="white" />
                                            <text x="18" y="20.5" text-anchor="middle" fill="currentColor"
                                                font-size="10" font-weight="700">{{ $loop->iteration }}</text>
                                        </svg>
                                        </a>
                                @endif
                            @endforeach
                        </div>
                        <div class="absolute right-2 top-2 z-20 flex flex gap-1" aria-label="Kontrol zoom peta">
                            <button type="button"
                                class="size-8 rounded bg-white/90 text-lg font-bold shadow hover:bg-white"
                                data-map-zoom="in" aria-label="Perbesar peta">+</button>
                            <button type="button"
                                class="size-8 rounded bg-white/90 text-lg font-bold shadow hover:bg-white"
                                data-map-zoom="out" aria-label="Perkecil peta">−</button>
                            <button type="button"
                                class="size-8 rounded bg-white/90 text-sm font-bold shadow hover:bg-white"
                                data-map-zoom="reset" aria-label="Atur ulang peta">↺</button>
                        </div>
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
                    <h2 class="mt-4 text-lg font-bold text-gray-800">Pilih Kabupaten/Kota</h2>
                </header>

                <nav aria-label="Daftar kabupaten dan kota"
                    class="min-h-0 flex-1 overflow-y-auto overscroll-contain pt-2 max-[850px]:overflow-visible">
                    <ul class="divide-y divide-gray-100">
                        @forelse ($regencies as $regency)
                            <li>
                                <a href="{{ route('regencies.menu', ['regency' => $regency->slug, 'menu' => $menu->slug]) }}"
                                    data-public-page-link
                                    class="flex min-h-12 items-center gap-3 rounded-lg px-3 py-2 text-md lg:text-lg 2xl:text-xl font-semibold text-gray-700 hover:bg-[color-mix(in_srgb,var(--page-header-background)_10%,white)] hover:text-[var(--page-header-background)] focus-visible:outline-2 focus-visible:outline-[var(--page-header-background)]">
                                    <span
                                        class="w-5 shrink-0 text-center font-bold text-[var(--page-header-background)]"
                                        aria-hidden="true">
                                        {{ $loop->iteration }}
                                    </span>
                                    <span class="min-w-0 flex-1 uppercase">{{ $regency->name }}</span>
                                    <i class="fas fa-chevron-right text-gray-400" aria-hidden="true"></i> </a>
                            </li>
                        @empty
                            <li class="p-4 text-sm text-gray-500">Belum ada kabupaten/kota yang tersedia.</li>
                        @endforelse
                    </ul>
                </nav>
            </aside>
        </section>
    </main>
    <script>
        (() => {
            const container = document.querySelector('[data-map-container]');
            const layer = container?.querySelector('[data-map-layer]');
            if (!container || !layer) return;
            let scale = 1,
                x = 0,
                y = 0,
                startX, startY,
                dragging = false,
                pinchStartDistance = 0,
                pinchStartScale = 1;
            const pointers = new Map();
            container.querySelectorAll('[data-marker-url]').forEach(marker => {
                marker.addEventListener('click', () => {
                    window.location.href = marker.dataset.markerUrl;
                });

                marker.addEventListener('dragstart', event => {
                    event.preventDefault();
                });
            });
            const clampScale = value => Math.max(1, Math.min(4, value));
            const pointerDistance = () => {
                const [first, second] = [...pointers.values()];
                return Math.hypot(second.clientX - first.clientX, second.clientY - first.clientY);
            };
            const limit = () => {
                const maxX = container.clientWidth * (scale - 1) / 2;
                const maxY = container.clientHeight * (scale - 1) / 2;
                x = Math.max(-maxX, Math.min(maxX, x));
                y = Math.max(-maxY, Math.min(maxY, y));
            };
            const render = () => {
                limit();
                layer.style.setProperty('--map-scale', scale);
                layer.style.setProperty('--map-inverse-scale', 1 / scale);
                layer.style.transform =
                    `translate3d(${x}px, ${y}px, 0) scale(${scale})`;
            };

            const zoom = (amount, clientX = null, clientY = null) => {
                const rect = container.getBoundingClientRect();
                const oldScale = scale;
                const nextScale = clampScale(scale + amount);

                const pointerX = clientX === null ?
                    rect.width / 2 :
                    clientX - rect.left;

                const pointerY = clientY === null ?
                    rect.height / 2 :
                    clientY - rect.top;

                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                const ratio = nextScale / oldScale;

                // Menjaga posisi di bawah pointer tetap sama.
                x = pointerX - centerX - (pointerX - centerX - x) * ratio;
                y = pointerY - centerY - (pointerY - centerY - y) * ratio;

                scale = nextScale;
                render();
            };

            container.querySelectorAll('[data-map-zoom]').forEach(button =>
                button.addEventListener('click', () => {
                    const action = button.dataset.mapZoom;

                    if (action === 'reset') {
                        scale = 1;
                        x = 0;
                        y = 0;
                        render();
                    } else {
                        zoom(action === 'in' ? .50 : -.50);
                    }
                })
            );

            container.addEventListener('wheel', event => {
                event.preventDefault();
                zoom(
                    event.deltaY < 0 ? .2 : -.2,
                    event.clientX,
                    event.clientY
                );
            }, {
                passive: false
            });
            container.addEventListener('pointerdown', event => {
                if (event.target.closest('button, a')) return;
                pointers.set(event.pointerId, event);
                container.setPointerCapture(event.pointerId);
                if (pointers.size === 1) {
                    dragging = true;
                    startX = event.clientX - x;
                    startY = event.clientY - y;
                } else if (pointers.size === 2) {
                    dragging = false;
                    pinchStartDistance = pointerDistance();
                    pinchStartScale = scale;
                }
            });
            container.addEventListener('pointermove', event => {
                if (!pointers.has(event.pointerId)) return;
                pointers.set(event.pointerId, event);
                if (pointers.size >= 2) {
                    const distance = pointerDistance();
                    if (pinchStartDistance > 0) {
                        scale = clampScale(pinchStartScale * distance / pinchStartDistance);
                        render();
                    }
                } else if (dragging) {
                    x = event.clientX - startX;
                    y = event.clientY - startY;
                    render();
                }
            });
            const endPointer = event => {
                pointers.delete(event.pointerId);
                if (pointers.size === 1) {
                    const remaining = [...pointers.values()][0];
                    dragging = true;
                    startX = remaining.clientX - x;
                    startY = remaining.clientY - y;
                } else {
                    dragging = false;
                    pinchStartDistance = 0;
                }
                if (container.hasPointerCapture(event.pointerId)) {
                    container.releasePointerCapture(event.pointerId);
                }
            };
            container.addEventListener('pointerup', endPointer);
            container.addEventListener('pointercancel', endPointer);
        })();
    </script>
</body>

</html>
