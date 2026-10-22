<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $regency->name }} - {{ $menu->title ?: $menu->name }}</title>
    <link rel="icon" type="image/png" href="{{ asset($homepage->header_logo) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body x-data="locationFullscreenModal()" @keydown.tab.window="trapFocus($event)"
    class="public-page m-0 h-[100dvh] overflow-hidden bg-gray-100 font-sans text-gray-800 max-[850px]:h-auto max-[850px]:min-h-screen max-[850px]:overflow-y-auto"
    style="--page-header-background: {{ $menu->color }}">
    <x-public-page-header :settings="$homepage" background-color="{{ $menu->color }}" />

    <main class="h-[calc(100vh-4rem)] w-full">
        <section
            class="grid h-full min-h-0 grid-cols-12 rounded-lg bg-white max-[850px]:h-auto max-[850px]:grid-cols-1">
            <div class="col-span-7 min-w-0 max-[850px]:col-span-1"
                style="background-color: color-mix(in srgb, var(--page-header-background) 14%, white)">
                <div class="p-3">
                    <div class="relative mx-auto aspect-square w-full max-w-[calc(100vh-6rem)] cursor-grab overflow-hidden rounded-lg border-[6px] touch-none select-none active:cursor-grabbing max-[850px]:max-w-[35dvh]"
                        style="border-color: var(--page-header-background)" id="mapFrame">
                        @if (!$regency->map_image)
                            <div
                                class="grid size-full place-items-center rounded-sm bg-gray-100 px-5 text-center text-sm text-gray-500">
                                Peta {{ $regency->name }} belum diunggah.</div>
                        @else
                            <div id="mapViewport" class="relative size-full overflow-hidden rounded-sm">
                                <img id="mapImage"
                                    class="absolute left-0 top-0 block size-full origin-top-left rounded-sm"
                                    src="{{ asset($regency->map_image) }}" alt="Peta {{ $regency->name }}">
                                <div id="markerLayer" class="pointer-events-none absolute inset-0"></div>
                                <div
                                    class="absolute right-2 top-2 z-30 flex overflow-hidden rounded-lg bg-white/90 shadow-md">
                                    <button type="button" id="zoomInButton"
                                        class="grid size-9 cursor-pointer place-items-center border-0 bg-transparent text-xl font-bold text-gray-700 hover:bg-gray-100"
                                        aria-label="Perbesar peta">+</button>
                                    <button type="button" id="zoomOutButton"
                                        class="grid size-9 cursor-pointer place-items-center border-0 border-t border-gray-200 bg-transparent text-xl font-bold text-gray-700 hover:bg-gray-100"
                                        aria-label="Perkecil peta">−</button>
                                    <button type="button" id="resetMapButton"
                                        class="grid size-9 cursor-pointer place-items-center border-0 border-t border-gray-200 bg-transparent text-lg font-bold text-gray-700 hover:bg-gray-100"
                                        aria-label="Reset peta">↺</button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <aside class="col-span-5 min-h-0 min-w-0 p-3 max-[850px]:col-span-1">
                <div id="listView" class="h-full min-h-0 overflow-hidden">
                    <div class="flex h-full min-h-0 flex-col">
                        <header class="border-b border-gray-200 pb-2 shrink-0">
                            <div class="flex flex-wrap items-center justify-between gap-3 max-[520px]:items-start">
                                <div class="flex min-w-0 items-center gap-2.5">
                                    <img class="h-auto w-16 md:w-28 shrink-0 object-contain max-[850px]:max-h-16"
                                        src="{{ asset($menu?->logo ?: 'images/logo/logo_kalsel.svg') }}"
                                        alt="Logo {{ $menu?->title ?: 'Kalimantan Selatan' }}">
                                    <div class="min-w-0">
                                        <h2 class="m-0 break-words text-2xl font-bold"
                                            style="color: {{ $menu->color }}">
                                            {{ $regency->name }}</h2>
                                        <p class="text-sm text-gray-500">{{ $menu->name }}</p>
                                        @if ($menu?->sub_title)
                                            <p class="text-sm text-gray-500">{{ $menu->sub_title }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex shrink-0 items-center gap-2">
                                    <div class="relative">
                                        <i
                                            class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>

                                        <input type="search" id="searchLocation" placeholder="Cari lokasi..."
                                            autocomplete="off" aria-label="Cari lokasi"
                                            class="h-10 w-52 rounded-lg border border-gray-400 bg-white pl-9 pr-3 text-sm outline-none focus:border-[var(--page-header-background)] focus:ring-2 focus:ring-[var(--page-header-background)]/20 max-[520px]:w-28">
                                    </div>

                                    <a href="{{ route('menu', $menu->slug) }}" data-public-page-link
                                        class="inline-flex h-10 shrink-0 items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-bold text-white shadow-sm transition-opacity hover:opacity-90 focus-visible:outline-4 focus-visible:outline-offset-2 focus-visible:outline-red-200 max-[520px]:px-2 max-[520px]:text-xs"
                                        style="background-color: var(--page-header-background)"
                                        aria-label="Kembali ke peta provinsi">
                                        Kembali
                                    </a>
                                </div>

                            </div>
                        </header>

                        <div id="categoryScroll" tabindex="0" role="region" aria-label="Kategori dan banner"
                            class="min-h-0 flex-1 overflow-x-hidden overflow-y-auto overscroll-contain pt-2.5 pr-1">
                            <div class="grid grid-cols-2 gap-2.5 max-[520px]:grid-cols-1" id="categoryGrid"></div>
                            @if ($menu?->banner)
                                <x-map-background-banner :image="$menu->banner" :text="$menu->description" />
                            @endif
                        </div>
                    </div>
                </div>

                <div id="detailView" class="h-full" hidden>
                    <article class="flex h-full min-w-0 flex-col overflow-hidden rounded-lg border border-gray-200">
                        <header class="shrink-0 border-b border-gray-200 p-4 pb-2">
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <div class="flex min-w-0 flex-1 items-center gap-2.5">
                                    <span
                                        class="grid size-8 shrink-0 place-items-center rounded-full text-sm font-bold text-white"
                                        id="detailNumber"></span>
                                    <h2 class="m-0 min-w-0 break-words text-2xl font-bold max-[520px]:text-xl"
                                        id="detailName"></h2>
                                </div>
                                <div class="ml-auto flex shrink-0 items-center gap-2">
                                    <button
                                        class="inline-flex min-h-10 cursor-pointer items-center gap-1.5 whitespace-nowrap rounded-lg border-0 px-3 py-2 text-sm font-bold text-white shadow-sm focus:outline-none focus-visible:ring-4 focus-visible:ring-gray-300 max-[520px]:px-2 max-[520px]:text-xs"
                                        style="background-color: var(--page-header-background)" id="fullscreenButton"
                                        type="button" @click="openModal()" aria-haspopup="dialog"
                                        aria-controls="detailModal" :aria-expanded="fullscreenOpen">
                                        <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" aria-hidden="true">
                                            <path d="M8 3H3v5m13-5h5v5M3 16v5h5m13-5v5h-5" />
                                        </svg>
                                        <span class="max-[520px]:hidden">
                                            Perluas
                                        </span>
                                    </button>
                                    <button
                                        class="inline-flex min-h-10 cursor-pointer items-center whitespace-nowrap rounded-lg border-0 px-3 py-2 text-sm font-bold text-white shadow-sm focus:outline-none focus-visible:ring-4 focus-visible:ring-gray-300 max-[520px]:px-2 max-[520px]:text-xs"
                                        style="background-color: var(--page-header-background)" id="backButton"
                                        type="button">
                                        Kembali ke daftar
                                    </button>
                                </div>
                            </div>
                            <div class="inline-flex items-center gap-1.5 rounded-full px-2.5 text-lg font-semibold"
                                id="detailCategory"></div>
                        </header>

                        <div class="min-h-0 flex-1 overflow-y-auto p-4" id="detailContent">
                            <div class="relative mb-4 grid aspect-video place-items-center overflow-hidden rounded-lg bg-black text-white"
                                id="detailMedia"></div>

                            <p class="mb-4 text-sm leading-6 text-gray-600">
                                <strong class="text-gray-800">Sumber:</strong>
                                <span id="detailSource"></span>
                            </p>

                            <div class="mb-4 flex items-start gap-2">
                                <svg class="mt-0.5 size-[18px] shrink-0 fill-[#da251d]" viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <path
                                        d="M12 2a8 8 0 0 0-8 8c0 5.5 8 12 8 12s8-6.5 8-12a8 8 0 0 0-8-8Zm0 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z" />
                                </svg>
                                <p class="m-0 text-base leading-6 font-bold" id="detailAddress"></p>
                            </div>

                            <div class="text-justify [&_h1]:mb-3 [&_h1]:mt-6 [&_h1]:text-3xl [&_h1]:font-bold [&_h2]:mb-3 [&_h2]:mt-5 [&_h2]:text-2xl [&_h2]:font-bold [&_h3]:mb-2 [&_h3]:mt-4 [&_h3]:text-xl [&_h3]:font-semibold [&_ol]:list-decimal [&_ul]:list-disc [&_li]:ml-5"
                                id="detailDescription"></div>
                        </div>
                    </article>
                </div>
            </aside>
        </section>
    </main>

    <x-ui.modal id="detailModal" x-model="fullscreenOpen" aria-labelledby="fullscreenDetailName"
        panelClass="h-[100dvh] max-h-[100dvh] w-screen max-w-none overflow-hidden p-0 mt-8">
        <article class="flex h-full max-h-full min-w-0 flex-col">
            <header class="shrink-0 border-b border-gray-200 p-4 pr-14 sm:p-6 sm:pb-2 sm:pr-16">
                <div class="flex min-w-0 items-start gap-3">
                    <span id="fullscreenDetailNumber"
                        class="grid size-10 shrink-0 place-items-center rounded-full text-lg font-bold text-white"></span>
                    <div class="min-w-0">
                        <h2 id="fullscreenDetailName" class="break-words text-2xl font-bold lg:text-3xl"></h2>
                    </div>
                </div>
            </header>

            <div id="fullscreenDetailContent" class="min-h-0 flex-1 overflow-y-auto p-0">
                <div id="fullscreenDetailMedia"
                    class="relative flex h-[calc(100dvh-6rem)] min-h-[60dvh] w-full shrink-0 items-center justify-center overflow-hidden bg-black text-white [&_img]:absolute [&_img]:inset-0 [&_img]:h-full [&_img]:w-full [&_img]:object-contain [&_video]:absolute [&_video]:inset-0 [&_video]:h-full [&_video]:w-full [&_video]:object-contain">
                </div>
                <div class="p-4 md:p-6">
                    <p class="mb-4 text-sm leading-6 text-gray-600">
                        <strong class="text-gray-800">Sumber:</strong>
                        <span id="fullscreenDetailSource"></span>
                    </p>
                    <div class="mb-4 flex items-start gap-2">
                        <svg class="mt-0.5 size-[18px] shrink-0 fill-[#da251d]" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path
                                d="M12 2a8 8 0 0 0-8 8c0 5.5 8 12 8 12s8-6.5 8-12a8 8 0 0 0-8-8Zm0 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z" />
                        </svg>
                        <p class="m-0 text-base leading-6 font-bold" id="fullscreenDetailAddress"></p>
                    </div>
                    <div id="fullscreenDetailDescription"
                        class="break-words text-base leading-8 sm:text-lg [&_h1]:mb-3 [&_h1]:mt-6 [&_h1]:text-3xl [&_h1]:font-bold [&_h2]:mb-3 [&_h2]:mt-5 [&_h2]:text-2xl [&_h2]:font-bold [&_h3]:mb-2 [&_h3]:mt-5 [&_h3]:text-xl [&_h3]:font-semibold [&_h3:first-child]:mt-0 [&_p]:mb-4 [&_ol]:my-3 [&_ol]:list-decimal [&_ul]:my-3 [&_ul]:list-disc [&_li]:ml-6 [&_blockquote]:border-l-4 [&_blockquote]:border-gray-200 [&_blockquote]:pl-4">
                    </div>
                </div>
            </div>
        </article>
    </x-ui.modal>

    <script>
        // Modal detail lokasi: fokus keyboard dan sinkronisasi pemutaran media.
        function locationFullscreenModal() {
            return {
                fullscreenOpen: false,
                videoMoved: false,
                resumeVideo: false,
                inertElements: [],

                init() {
                    this.$watch('fullscreenOpen', (open) => {
                        if (!open) this.stopPlayback();
                    });
                },

                openModal() {
                    if (this.fullscreenOpen) return;

                    ['Name', 'Number', 'Source', 'Address'].forEach((field) => {
                        document.getElementById('fullscreenDetail' + field).textContent =
                            document.getElementById('detail' + field).textContent;
                    });

                    document.getElementById('fullscreenDetailNumber').style.backgroundColor =
                        document.getElementById('detailNumber').style.backgroundColor;

                    document.getElementById('fullscreenDetailDescription').innerHTML =
                        document.getElementById('detailDescription').innerHTML;

                    const panelMedia = document.getElementById('detailMedia');
                    const modalMedia = document.getElementById('fullscreenDetailMedia');
                    const playerElement = panelMedia.querySelector('.plyr');

                    this.videoMoved = !!playerElement;
                    this.resumeVideo = !!(detailPlayer && !detailPlayer.paused);

                    if (playerElement) {
                        detailPlayer.pause();
                        modalMedia.replaceChildren(playerElement);
                    } else {
                        const media = panelMedia.cloneNode(true);
                        modalMedia.replaceChildren(...media.childNodes);
                    }

                    this.inertElements = [...document.body.children]
                        .filter((element) => ['HEADER', 'MAIN'].includes(element.tagName) && !element.inert);

                    this.inertElements.forEach((element) => element.inert = true);
                    this.fullscreenOpen = true;

                    this.$nextTick(() => {
                        if (!this.fullscreenOpen) return;

                        document.getElementById('detailModal')
                            .querySelector('button')?.focus();

                        document.getElementById('fullscreenDetailContent').scrollTop = 0;

                        if (this.videoMoved && this.resumeVideo && detailPlayer) {
                            Promise.resolve(detailPlayer.play()).catch(() => {});
                        }
                    });
                },
                stopPlayback() {
                    const panelMedia = document.getElementById('detailMedia');
                    const modalMedia = document.getElementById('fullscreenDetailMedia');

                    if (this.videoMoved) {
                        const playerElement = modalMedia.querySelector('.plyr');

                        if (playerElement) {
                            panelMedia.replaceChildren(playerElement);
                        }
                    }

                    modalMedia.replaceChildren();

                    this.videoMoved = false;
                    this.resumeVideo = false;

                    this.inertElements.forEach((element) => element.inert = false);
                    this.inertElements = [];

                    this.$nextTick(() => {
                        document.getElementById('fullscreenButton')?.focus();
                        if (wasPlaying && detailPlayer) {
                            Promise.resolve(detailPlayer.play()).catch(() => {});
                        }
                    });
                },

                trapFocus(event) {
                    if (!this.fullscreenOpen) return;
                    const modal = document.getElementById('detailModal');
                    const focusable = [...modal.querySelectorAll(
                        'button, video[controls], a[href], input, select, textarea, [tabindex]'
                    )].filter((element) => !element.disabled && element.tabIndex >= 0 && element.getClientRects()
                        .length);
                    const first = focusable[0];
                    const last = focusable.at(-1);
                    if (!first) return;

                    if (event.shiftKey && (document.activeElement === first || !modal.contains(document.activeElement))) {
                        event.preventDefault();
                        last.focus();
                    } else if (!event.shiftKey && (document.activeElement === last || !modal.contains(document
                            .activeElement))) {
                        event.preventDefault();
                        first.focus();
                    }
                },
            };
        }
    </script>


    <script>
        // Data halaman dan referensi elemen utama.
        const locations = @json($locations);
        const hasRegencyMap = @js((bool) $regency?->map_image);

        const mapFrame = document.getElementById('mapFrame');
        const mapViewport = document.getElementById('mapViewport');
        const mapImage = document.getElementById('mapImage');
        const markerLayer = document.getElementById('markerLayer');
        const categoryGrid = document.getElementById('categoryGrid');
        const listView = document.getElementById('listView');
        const detailView = document.getElementById('detailView');

        // Sanitasi konten deskripsi sebelum dimasukkan ke halaman.
        const escapeHtml = (value) => String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

        function safeDescription(html) {
            const template = document.createElement('template');
            template.innerHTML = html ?? '';
            template.content.querySelectorAll('script, style, iframe, object, svg, math').forEach((element) => element
                .remove());

            const allowedTags = new Set(['P', 'H1', 'H2', 'H3', 'BR', 'STRONG', 'B', 'EM', 'I', 'U', 'UL', 'OL', 'LI',
                'BLOCKQUOTE'
            ]);
            template.content.querySelectorAll('*').forEach((element) => {
                if (!allowedTags.has(element.tagName)) {
                    element.replaceWith(...element.childNodes);
                    return;
                }

                [...element.attributes].forEach((attribute) => element.removeAttribute(attribute.name));
            });

            return template.innerHTML;
        }

        // Pengelompokan lokasi dan penomoran marker per kategori.
        const groupedLocations = locations.reduce((groups, location) => {
            if (!groups[location.category]) {
                groups[location.category] = {
                    color: location.color,
                    icon: location.icon,
                    background: location.background,
                    locations: []
                };
            }

            groups[location.category].locations.push(location);
            return groups;
        }, {});

        const getCategoryNumber = (location) =>
            locations.filter((item) => item.category === location.category)
            .findIndex((item) => item.id === location.id) + 1;

        // State navigasi peta: zoom, posisi geser, dan status gesture marker.
        const mapState = {
            zoom: 1,
            x: 0,
            y: 0,
            dragging: false,
            startX: 0,
            startY: 0,
            originX: 0,
            originY: 0,
            moved: false,
            markerGesture: false,
            suppressMarkerClick: false
        };

        const MAP_MIN_ZOOM = 1;
        const MAP_MAX_ZOOM = 4;
        const MAP_ZOOM_STEP = 0.5;

        // Rendering peta: batasi posisi, terapkan transform, dan posisikan marker.
        function limitMapPosition() {
            if (!mapViewport) return;
            const maxX = mapViewport.clientWidth * (mapState.zoom - 1);
            const maxY = mapViewport.clientHeight * (mapState.zoom - 1);
            mapState.x = Math.min(0, Math.max(-maxX, mapState.x));
            mapState.y = Math.min(0, Math.max(-maxY, mapState.y));
        }

        function updateMap() {
            if (!mapViewport) return;
            limitMapPosition();
            mapImage.style.transform = `translate(${mapState.x}px, ${mapState.y}px) scale(${mapState.zoom})`;
            document.querySelectorAll('#markerLayer [data-location-id]').forEach((marker) => {
                const location = locations.find((item) => item.id === Number(marker.dataset.locationId));
                if (!location) return;
                // Markers stay at their geographic position while keeping their original pixel size.
                marker.style.left =
                    `${location.x_location * mapViewport.clientWidth / 100 * mapState.zoom + mapState.x}px`;
                marker.style.top =
                    `${location.y_location * mapViewport.clientHeight / 100 * mapState.zoom + mapState.y}px`;
                marker.style.right = 'auto';
            });
        }

        // Kontrol zoom dan reset dengan titik fokus tertentu.
        function setMapZoom(zoom, focusX = mapViewport?.clientWidth / 2, focusY = mapViewport?.clientHeight / 2) {
            if (!mapViewport) return;
            const nextZoom = Math.min(MAP_MAX_ZOOM, Math.max(MAP_MIN_ZOOM, zoom));
            const ratio = nextZoom / mapState.zoom;
            mapState.x = focusX - (focusX - mapState.x) * ratio;
            mapState.y = focusY - (focusY - mapState.y) * ratio;
            mapState.zoom = nextZoom;
            updateMap();
        }

        function resetMap() {
            mapState.zoom = MAP_MIN_ZOOM;
            mapState.x = 0;
            mapState.y = 0;
            updateMap();
        }

        // Pinch zoom pada perangkat sentuh.
        const mapPointers = new Map();
        let pinchStartDistance = 0;
        let pinchStartZoom = 1;
        let pinchFocusX = 0;
        let pinchFocusY = 0;

        function getPointerDistance() {
            const points = [...mapPointers.values()];
            if (points.length < 2) return 0;

            return Math.hypot(
                points[1].clientX - points[0].clientX,
                points[1].clientY - points[0].clientY
            );
        }

        function getPinchCenter() {
            const points = [...mapPointers.values()];

            return {
                x: (points[0].clientX + points[1].clientX) / 2 - mapViewport.getBoundingClientRect().left,
                y: (points[0].clientY + points[1].clientY) / 2 - mapViewport.getBoundingClientRect().top
            };
        }

        function updatePinchZoom() {
            if (mapPointers.size < 2 || !pinchStartDistance) return;

            const distance = getPointerDistance();
            const nextZoom = Math.min(
                MAP_MAX_ZOOM,
                Math.max(MAP_MIN_ZOOM, pinchStartZoom * distance / pinchStartDistance)
            );

            const ratio = nextZoom / mapState.zoom;

            mapState.x = pinchFocusX - (pinchFocusX - mapState.x) * ratio;
            mapState.y = pinchFocusY - (pinchFocusY - mapState.y) * ratio;
            mapState.zoom = nextZoom;

            updateMap();
        }

        mapFrame?.addEventListener('pointerdown', (event) => {
            if (event.target.closest('button, a')) return;

            mapPointers.set(event.pointerId, event);
            mapFrame.setPointerCapture(event.pointerId);

            if (mapPointers.size === 2) {
                const center = getPinchCenter();

                pinchStartDistance = getPointerDistance();
                pinchStartZoom = mapState.zoom;
                pinchFocusX = center.x;
                pinchFocusY = center.y;
                mapState.dragging = false;
            }
        });

        mapFrame?.addEventListener('pointermove', (event) => {
            if (!mapPointers.has(event.pointerId)) return;

            mapPointers.set(event.pointerId, event);

            if (mapPointers.size >= 2) {
                updatePinchZoom();
            }
        });

        function finishMapPointer(event) {
            mapPointers.delete(event.pointerId);

            if (mapPointers.size < 2) {
                pinchStartDistance = 0;
            }

            if (mapFrame.hasPointerCapture(event.pointerId)) {
                mapFrame.releasePointerCapture(event.pointerId);
            }
        }

        mapFrame?.addEventListener('pointerup', finishMapPointer);
        mapFrame?.addEventListener('pointercancel', finishMapPointer);

        // Marker lokasi pada peta dan interaksi menuju detail lokasi.
        function renderMarkers() {
            locations.forEach((location) => {
                const categoryNumber = getCategoryNumber(location);
                const marker = document.createElement('button');
                marker.type = 'button';
                marker.className =
                    'pointer-events-auto absolute z-10 inline-flex h-fit w-fit origin-bottom -translate-x-1/2 -translate-y-full border-0 bg-transparent p-0 drop-shadow-md focus:outline-none focus-visible:ring-4 focus-visible:ring-red-200';
                marker.dataset.locationId = location.id;
                marker.style.left = location.x_location + '%';
                marker.style.top = location.y_location + '%';
                marker.style.color = location.color;
                marker.innerHTML =
                    '<svg class="pointer-events-none h-auto w-8 lg:w-10 2xl:w-12 overflow-visible" style="cursor:pointer" viewBox="0 0 36 46" aria-hidden="true">' +
                    '<path d="M18 1.5C9.2 1.5 2 8.4 2 16.9C2 29.1 18 44.5 18 44.5S34 29.1 34 16.9C34 8.4 26.8 1.5 18 1.5Z" fill="currentColor" stroke="white" stroke-width="2.5" style="pointer-events:visiblePainted"/>' +
                    '<circle cx="18" cy="17" r="8" fill="white" style="pointer-events:visiblePainted"/>' +
                    '<text class="marker-number" x="18" y="20.5" text-anchor="middle" font-size="10" font-weight="700" fill="currentColor" style="pointer-events:visiblePainted">' +
                    categoryNumber + '</text>' +
                    '</svg>';
                marker.title = location.name;
                marker.setAttribute('aria-label', 'Lihat detail ' + location.name);
                marker.addEventListener('pointerdown', (event) => event.stopPropagation());
                marker.addEventListener('click', (event) => {
                    event.stopPropagation();
                    showDetail(location.id);
                });
                markerLayer.appendChild(marker);
            });
            updateMap();
        }

        // Daftar lokasi: render kategori dan filter berdasarkan pencarian.
        function renderList(search = '') {
            const keyword = search.toLowerCase().trim();

            if (!locations.length) {
                categoryGrid.innerHTML =
                    '<p class="col-span-2 rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-500">Belum ada lokasi untuk wilayah dan menu ini.</p>';
                return;
            }

            const filteredGroups = Object.entries(groupedLocations)
                .map(([category, group]) => [
                    category,
                    {
                        ...group,
                        locations: group.locations.filter((location) => [location.name, location.address, location
                                .category
                            ]
                            .some((value) =>
                                String(value ?? '').toLowerCase().includes(keyword)
                            )
                        )
                    }
                ])
                .filter(([, group]) => group.locations.length);

            categoryGrid.innerHTML = filteredGroups.map(([category, group]) =>
                '<section class="flex flex-col overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">' +
                '<h3 class="m-0 flex items-center gap-2 px-2.5 py-2.5 text-md font-bold" style="background-color: color-mix(in srgb, ' +
                escapeHtml(group.color) + ' 14%, white)">' +
                '<span class="grid size-8 shrink-0 place-items-center rounded-lg bg-gray-50 text-gray-500 dark:bg-gray-800 dark:text-gray-300" style="color: ' +
                escapeHtml(group.color) + '">' + '<i class="fas fa-' + escapeHtml(group.icon) + '"></i>' + '</span>' +
                '<span style="color: ' + escapeHtml(group.color) + '">' + escapeHtml(category) + '</span>' +
                '</h3>' +
                '<div class="relative flex-1 px-2.5 py-1">' +
                (group.background ?
                    '<img src="' + escapeHtml(group.background) +
                    '" alt="" aria-hidden="true" loading="lazy" class="pointer-events-none absolute inset-y-0 right-0 h-full w-full object-cover object-center">' +
                    '<div class="pointer-events-none absolute inset-y-0 right-0 w-full bg-gradient-to-r from-white via-white/85 to-transparent"></div>' :
                    '') +
                '<div class="relative">' +
                group.locations.map((location, index) =>
                    '<button class="flex w-full cursor-pointer items-center gap-2 border-0 bg-transparent py-2 text-left text-sm text-gray-800" type="button" data-location-id="' +
                    location.id + '">' +
                    '<span class="grid size-[22px] shrink-0 place-items-center rounded-full text-[10px] font-bold text-white" style="background-color: ' +
                    escapeHtml(location.color) + '">' +
                    getCategoryNumber(location) +
                    '</span>' +
                    '<span class="category-list-text">' + escapeHtml(location.name) + '</span>' +
                    '</button>'
                ).join('') +
                '</div></div></section>'
            ).join('');

            if (!filteredGroups.length) {
                categoryGrid.innerHTML =
                    '<p class="col-span-full py-10 text-center text-sm text-gray-500">' +
                    'Lokasi tidak ditemukan.' +
                    '</p>';
                return;
            }

            if (categoryGrid.children.length % 2) categoryGrid.lastElementChild.style.gridColumn = '1 / -1';

            categoryGrid.querySelectorAll('[data-location-id]').forEach((button) => {
                button.addEventListener('click', () => showDetail(Number(button.dataset.locationId)));
            });
        }

        // Sinkronkan hasil pencarian antara daftar lokasi dan marker peta.
        function filterLocations(search = '') {
            const keyword = search.toLowerCase().trim();

            renderList(keyword);

            mapFrame.querySelectorAll('[data-location-id]').forEach((marker) => {
                const location = locations.find(
                    (item) => item.id === Number(marker.dataset.locationId)
                );

                if (!location) return;

                const matches = [
                    location.name,
                    location.address,
                    location.category
                ].some((value) =>
                    String(value ?? '').toLowerCase().includes(keyword)
                );

                marker.style.display = matches ? '' : 'none';
            });
        }

        // Panel detail lokasi dan media pendukungnya.
        let detailPlayer = null;

        function showDetail(locationId) {
            const location = locations.find((item) => item.id === locationId);
            if (!location) return;

            const detailCategory = document.getElementById('detailCategory');
            detailCategory.innerHTML = '<i class="' + escapeHtml(location.icon) + '"></i>' + '<span>' + escapeHtml(location
                .category) + '</span>';
            detailCategory.style.color = location.color;

            const detailNumber = document.getElementById('detailNumber');
            detailNumber.textContent = getCategoryNumber(location);
            detailNumber.style.backgroundColor = location.color;

            document.getElementById('detailName').textContent = location.name;
            document.getElementById('detailAddress').textContent = location.address;
            document.getElementById('detailDescription').innerHTML = safeDescription(location.description);

            const detailMedia = document.getElementById('detailMedia');
            const mediaUrl = location.media;
            const isVideo = mediaUrl?.toLowerCase().endsWith('.mp4');

            detailPlayer?.destroy();
            detailPlayer = null;

            if (!mediaUrl) {
                detailMedia.innerHTML = '<span>Media belum tersedia</span>';
            } else if (isVideo) {
                detailMedia.innerHTML =
                    '<video class="block h-full w-full" controls playsinline preload="metadata" aria-label="Video ' +
                    escapeHtml(location.name) + '">' +
                    '<source src="' + escapeHtml(mediaUrl) + '" type="video/mp4">' +
                    '</video>';

                detailPlayer = new window.Plyr(detailMedia.querySelector('video'), {
                    controls: [
                        'play-large',
                        'restart',
                        'rewind',
                        'play',
                        'fast-forward',
                        'progress',
                        'current-time',
                        'duration',
                        'mute',
                        'volume',
                        'settings',
                        'pip',
                        'fullscreen'
                    ],
                    settings: ['speed'],
                    seekTime: 10,
                    fullscreen: {
                        enabled: true,
                        fallback: true,
                        iosNative: false
                    }
                });

                Promise.resolve(detailPlayer.play()).catch(() => {});
            } else {
                detailMedia.innerHTML =
                    '<img class="absolute inset-0 block" style="width:100%;height:100%;object-fit:contain" src="' +
                    escapeHtml(mediaUrl) + '" alt="Media ' +
                    escapeHtml(
                        location.name) + '">';
            }

            const source = document.getElementById('detailSource');
            source.textContent = location.source_media || 'Sumber belum tersedia';

            document.querySelectorAll('[data-location-id]').forEach((element) => {
                if (!element.closest('#mapFrame')) return;
                const markerLocation = locations.find((item) => item.id === Number(element.dataset
                    .locationId));
                if (!markerLocation) return;
                const active = markerLocation.id === locationId;
                element.style.color = active ? '#da251d' : markerLocation.color;
                element.querySelector('.marker-number')?.classList.toggle('hidden', active);
                element.classList.toggle('scale-y-110', active);
                element.classList.toggle('animate-stretch-up', active);
                element.classList.toggle('z-20', active);
            });

            listView.hidden = true;
            detailView.hidden = false;
            document.getElementById('detailContent').scrollTop = 0;
        }

        // Navigasi kembali dari detail ke daftar lokasi.
        document.getElementById('backButton').addEventListener('click', () => {
            detailPlayer?.pause();
            detailView.hidden = true;
            listView.hidden = false;

            document.querySelectorAll('#mapFrame [data-location-id]').forEach((marker) => {
                const location = locations.find((item) => item.id === Number(marker.dataset
                    .locationId));
                if (location) marker.style.color = location.color;
                marker.querySelector('.marker-number')?.classList.remove('hidden');
                marker.classList.remove('z-20', 'animate-stretch-up', 'scale-y-110');
            });
        });

        // Inisialisasi interaksi peta: wheel zoom, drag, tombol zoom, dan resize.
        if (hasRegencyMap) {
            renderMarkers();
            mapFrame.addEventListener('wheel', (event) => {
                event.preventDefault();
                const rect = mapViewport.getBoundingClientRect();
                setMapZoom(mapState.zoom + (event.deltaY < 0 ? MAP_ZOOM_STEP : -MAP_ZOOM_STEP), event.clientX - rect
                    .left, event
                    .clientY - rect.top);
            }, {
                passive: false
            });
            mapFrame.addEventListener('pointerdown', (event) => {
                if (event.target.closest('#zoomInButton, #zoomOutButton, #resetMapButton')) return;
                mapState.dragging = true;
                mapState.moved = false;
                mapState.markerGesture = Boolean(event.target.closest('[data-location-id]'));
                mapState.startX = event.clientX;
                mapState.startY = event.clientY;
                mapState.originX = mapState.x;
                mapState.originY = mapState.y;
                mapFrame.setPointerCapture(event.pointerId);
            });
            mapFrame.addEventListener('pointermove', (event) => {
                if (!mapState.dragging) return;
                const deltaX = event.clientX - mapState.startX;
                const deltaY = event.clientY - mapState.startY;
                if (Math.hypot(deltaX, deltaY) > 6) mapState.moved = true;
                event.preventDefault();
                mapState.x = mapState.originX + deltaX;
                mapState.y = mapState.originY + deltaY;
                updateMap();
            });
            ['pointerup', 'pointercancel'].forEach((type) => mapFrame.addEventListener(type, () => {
                if (mapState.markerGesture && mapState.moved) mapState.suppressMarkerClick = true;
                mapState.dragging = false;
                mapState.markerGesture = false;
            }));
            document.getElementById('zoomInButton').addEventListener('click', () => setMapZoom(mapState.zoom +
                MAP_ZOOM_STEP));
            document.getElementById('zoomOutButton').addEventListener('click', () => setMapZoom(mapState.zoom -
                MAP_ZOOM_STEP));
            document.getElementById('resetMapButton').addEventListener('click', resetMap);
            new ResizeObserver(updateMap).observe(mapViewport);
        }
        // Inisialisasi pencarian lokasi dan render awal.
        const searchLocation = document.getElementById('searchLocation');

        searchLocation.addEventListener('input', (event) => {
            filterLocations(event.target.value);
        });

        renderList();
    </script>
</body>

</html>
