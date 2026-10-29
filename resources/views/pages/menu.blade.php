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

        .kalsel-location-marker {
            background: transparent;
        }

        .kalsel-location-marker svg {
            display: block;
            transform-origin: center bottom;
        }

        .kalsel-location-marker.is-active svg {
            scale: 1.12;
            filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.25));
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
                    <div id="mapFrame"
                        class="relative mx-auto aspect-square w-full max-w-[calc(100vh-6rem)] overflow-hidden rounded-lg border-[6px] max-[850px]:max-w-[35dvh]"
                        style="border-color: var(--page-header-background)">
                        <div id="leafletMap" class="absolute inset-0 z-0 size-full rounded-sm"></div>
                        <!-- Kontrol zoom custom -->
                        <div
                            class="absolute right-2 top-2 z-[1000] flex overflow-hidden rounded-lg bg-white/90 shadow-md">
                            <button type="button" id="zoomInButton"
                                class="grid size-9 cursor-pointer place-items-center border-0 bg-transparent text-xl font-bold text-gray-700 hover:bg-gray-100"
                                aria-label="Perbesar peta">+</button>
                            <button type="button" id="zoomOutButton"
                                class="grid size-9 cursor-pointer place-items-center border-0 border-l border-gray-200 bg-transparent text-xl font-bold text-gray-700 hover:bg-gray-100"
                                aria-label="Perkecil peta">−</button>
                            <button type="button" id="resetMapButton"
                                class="grid size-9 cursor-pointer place-items-center border-0 border-l border-gray-200 bg-transparent text-lg font-bold text-gray-700 hover:bg-gray-100"
                                aria-label="Reset peta">↺</button>
                        </div>
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

                    this.videoMoved = true;
                    this.resumeVideo = Boolean(detailPlayer && !detailPlayer.paused);

                    detailPlayer?.pause?.();

                    // Pindahkan seluruh galeri ke fullscreen.
                    modalMedia.replaceChildren(...panelMedia.childNodes);

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

                    const wasPlaying = Boolean(
                        detailPlayer && !detailPlayer.paused
                    );

                    detailPlayer?.pause?.();

                    if (this.videoMoved) {
                        panelMedia.replaceChildren(...modalMedia.childNodes);
                    }

                    modalMedia.replaceChildren();

                    this.videoMoved = false;
                    this.resumeVideo = false;

                    this.inertElements.forEach((element) => {
                        element.inert = false;
                    });

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

        const regencyCoordinates = @js([$regency->latitude, $regency->longitude]);
        const regencySlug = @js($regency->slug);
        const geoJsonUrl = @js(asset('geojson/regency_kalsel.geojson'));
        const categoryGrid = document.getElementById('categoryGrid');
        const listView = document.getElementById('listView');
        const detailView = document.getElementById('detailView');
        let leafletMap = null;
        const locationMarkers = new Map();

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

        function getLocationCoordinates(location) {
            if (
                location.latitude == null ||
                location.longitude == null ||
                location.latitude === '' ||
                location.longitude === ''
            ) {
                return null;
            }

            const lat = Number(location.latitude);
            const lng = Number(location.longitude);

            if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                return null;
            }

            if (Math.abs(lat) > 90 || Math.abs(lng) > 180) {
                return null;
            }

            return [lat, lng];
        }

        function markerColor(location) {
            const color = String(location.color ?? '');
            return /^#[0-9a-f]{3}(?:[0-9a-f]{3})?$/i.test(color) ?
                color :
                '#da251d';
        }

        // Membuat marker dengan desain yang sama seperti sebelumnya.

        function createMarkerIcon(location) {
            const number = getCategoryNumber(location);
            return L.divIcon({
                className: 'kalsel-location-marker',
                iconSize: [40, 52],
                iconAnchor: [20, 52],
                html: `
            <svg viewBox="0 0 36 46"
                 width="40" height="52"
                 aria-hidden="true">
                <path
                    d="M18 1.5C9.2 1.5 2 8.4 2 16.9C2 29.1 18 44.5 18 44.5S34 29.1 34 16.9C34 8.4 26.8 1.5 18 1.5Z"
                    fill="currentColor"
                    stroke="white"
                    stroke-width="2.5"
                />
                <circle cx="18" cy="17" r="8" fill="white"/>
                <text class="marker-number"
                      x="18" y="20.5"
                      text-anchor="middle"
                      fill="currentColor"
                      font-size="10"
                      font-weight="700">
                    ${number}
                </text>
            </svg>
        `
            });
        }

        function renderMarkers() {
            console.table(locations.map(location => ({
                id: location.id,
                name: location.name,
                latitude: location.latitude,
                longitude: location.longitude
            })));

            locations.forEach((location) => {
                const coordinates = getLocationCoordinates(location);
                if (!coordinates) {
                    console.warn(
                        'Marker dilewati karena koordinat tidak valid:',
                        location.name,
                        location.latitude,
                        location.longitude
                    );
                    return;
                }

                const marker = L.marker(coordinates, {
                    icon: createMarkerIcon(location),
                    title: location.name,
                    keyboard: true,
                    riseOnHover: true
                }).addTo(leafletMap);
                const element = marker.getElement();
                if (element) {
                    element.style.color = markerColor(location);
                }

                marker.on('click', () => {
                    showDetail(Number(location.id));
                });

                locationMarkers.set(Number(location.id), marker);

            });

            console.log('Jumlah marker Leaflet:', locationMarkers.size);

        }

        // Highlight marker saat detail ditampilkan.

        function setActiveMarker(locationId = null) {
            locationMarkers.forEach((marker, id) => {
                const location = locations.find(
                    item => Number(item.id) === id
                );

                if (!location) return;
                const active = id === Number(locationId);
                const element = marker.getElement();
                if (element) {
                    element.style.color = active ?
                        '#da251d' :
                        markerColor(location);
                    element.classList.toggle('is-active', active);
                    element.querySelector('.marker-number')
                        ?.classList.toggle('hidden', active);
                }
                marker.setZIndexOffset(active ? 1000 : 0);
            });

        }

        // Nama pada GeoJSON menggunakan properties.shapeName.
        // Samakan dengan slug database tanpa mengubah file GeoJSON asli.
        function normalizeRegencyName(value) {
            const name = String(value ?? '')
                .toLowerCase()
                .replace(/[_-]+/g, ' ')
                .replace(/\s+/g, ' ')
                .trim();

            // Nama pada GeoJSON tidak selalu mengikuti penulisan database.
            if (name === 'kota baru' || name === 'kabupaten kota baru') return 'kotabaru';
            if (name === 'kota banjar baru') return 'banjarbaru';

            return name
                .replace(/^(kabupaten|kota)\s+/, '')
                .replace(/[^a-z0-9]/g, '');
        }

        async function initializeLeafletMap() {
            const mapElement = document.getElementById('leafletMap');
            const noticeElement = document.getElementById('menuMapNotice');

            if (!mapElement || !window.L) {
                console.error('Leaflet belum tersedia atau #leafletMap tidak ditemukan.');
                return;
            }

            const hasRegencyCenter =
                regencyCoordinates[0] != null &&
                regencyCoordinates[1] != null &&
                Number.isFinite(Number(regencyCoordinates[0])) &&
                Number.isFinite(Number(regencyCoordinates[1]));

            const center = hasRegencyCenter ?
                regencyCoordinates.map(Number) : [-3.1, 115.4];

            leafletMap = L.map(mapElement, {
                center,
                zoom: 10,
                zoomControl: false,
                zoomSnap: 0.25,
                zoomDelta: 0.5,
                maxZoom: 19,
                maxBoundsViscosity: 1.0,
                bounceAtZoomLimits: false,
                scrollWheelZoom: true,
                touchZoom: true,
                attributionControl: false
            });

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(leafletMap);

            // Map offline
            // window.createOfflineBaseMap().addTo(leafletMap);

            // Fallback jika GeoJSON gagal dimuat: fokus sekitar titik pusat
            // regency dan lokasi yang memiliki koordinat. Ini bukan batas resmi.
            let regionBounds = hasRegencyCenter ?
                L.latLngBounds(
                    [center[0] - 0.35, center[1] - 0.35],
                    [center[0] + 0.35, center[1] + 0.35]
                ) :
                L.latLngBounds([-4.65, 114.0], [-1.40, 118.0]);

            try {
                const response = await fetch(geoJsonUrl);
                if (!response.ok) {
                    throw new Error(`Gagal mengakses GeoJSON (HTTP ${response.status})`);
                }

                const geojson = await response.json();
                if (geojson?.type !== 'FeatureCollection' ||
                    !Array.isArray(geojson.features)) {
                    throw new Error('GeoJSON harus memiliki type FeatureCollection dan features.');
                }

                const requested = normalizeRegencyName(regencySlug);
                const selectedFeature = geojson.features.find(feature =>
                    normalizeRegencyName(feature.properties?.shapeName) === requested
                );

                if (!selectedFeature) {
                    throw new Error(`Polygon untuk ${regencySlug} tidak ditemukan di GeoJSON.`);
                }

                // Kabupaten/kota lain: tetap terlihat, tetapi diredupkan.
                // Warnanya tetap mengambil properties.fill dari GeoJSON.
                L.geoJSON(geojson, {
                    filter: feature => feature !== selectedFeature,
                    interactive: false,
                    style(feature) {
                        return {
                            color: '#78716c',
                            weight: 0.9,
                            opacity: 0.5,
                            fillColor: feature.properties?.fill || '#a8a29e',
                            fillOpacity: 0.10
                        };
                    }
                }).addTo(leafletMap);

                // Wilayah yang sedang dibuka: warna asli GeoJSON ditonjolkan.
                const selectedLayer = L.geoJSON(selectedFeature, {
                    style(feature) {
                        return {
                            color: '#000',
                            weight: Math.max(1.5, Number(feature.properties?.['stroke-width']) || 1),
                            opacity: 1,
                            fill: false
                        };
                    },
                    onEachFeature(feature, layer) {
                        layer.bindTooltip(feature.properties?.shapeName || regencySlug, {
                            sticky: true,
                            direction: 'top'
                        });
                    }
                }).addTo(leafletMap);

                const polygonBounds = selectedLayer.getBounds();
                if (!polygonBounds.isValid()) {
                    throw new Error('Batas polygon wilayah tidak valid.');
                }

                // Zoom dan batas bergerak berdasarkan ukuran polygon sebenarnya.
                // Tidak diperluas dengan marker lokasi agar framing tetap fokus.
                regionBounds = polygonBounds;
            } catch (error) {
                console.error('Gagal memuat polygon kabupaten/kota:', error);

                if (noticeElement) {
                    noticeElement.textContent = 'Batas wilayah belum tersedia. Peta dan lokasi tetap dapat dilihat.';
                    noticeElement.classList.remove('hidden');
                }

                locations.forEach(location => {
                    const coordinates = getLocationCoordinates(location);
                    if (coordinates) regionBounds.extend(coordinates);
                });
            }

            let resetCenter = null;
            let resetZoom = null;
            let resetBounds = null;
            let calculating = false;
            let restoring = false;

            function syncMinimumView() {
                if (calculating || restoring || !resetCenter) return;

                const atMinimum = leafletMap.getZoom() <= resetZoom + 0.001;
                if (atMinimum) {
                    leafletMap.dragging.disable();

                    if (leafletMap.getCenter().distanceTo(resetCenter) > 1) {
                        restoring = true;
                        leafletMap.setView(resetCenter, resetZoom, {
                            animate: false
                        });
                        restoring = false;
                    }
                } else {
                    leafletMap.dragging.enable();
                }
            }

            function calculateResetView() {
                if (calculating) return;
                calculating = true;

                // Ukuran container boleh berubah di layar sentuh / responsive.
                leafletMap.setMaxBounds(null);
                leafletMap.setMinZoom(0);
                leafletMap.fitBounds(regionBounds, {
                    padding: [20, 20],
                    animate: false
                });

                resetCenter = leafletMap.getCenter();
                resetZoom = leafletMap.getZoom();
                resetBounds = leafletMap.getBounds();

                // Batas drag = cakupan area yang terlihat saat Reset.
                leafletMap.setMaxBounds(resetBounds);
                leafletMap.setMinZoom(resetZoom);
                calculating = false;
                syncMinimumView();
            }

            leafletMap.on('zoomend moveend', syncMinimumView);
            leafletMap.on('resize', calculateResetView);
            calculateResetView();

            // Marker selalu berada di atas polygon GeoJSON (pane marker Leaflet).
            renderMarkers();

            document.getElementById('zoomInButton')?.addEventListener('click', () => {
                leafletMap.zoomIn(0.5);
            });

            document.getElementById('zoomOutButton')?.addEventListener('click', () => {
                leafletMap.zoomOut(0.5);
            });

            document.getElementById('resetMapButton')?.addEventListener('click', () => {
                leafletMap.stop();
                leafletMap.setView(resetCenter, resetZoom, {
                    animate: true
                });
            });
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
            if (!leafletMap) return;
            locationMarkers.forEach((marker, id) => {
                const location = locations.find(
                    item => Number(item.id) === id
                );

                if (!location) return;
                const matches = [
                    location.name,
                    location.address,
                    location.category
                ].some(value =>
                    String(value ?? '')
                    .toLowerCase()
                    .includes(keyword)
                );

                if (matches) {
                    if (!leafletMap.hasLayer(marker)) {
                        marker.addTo(leafletMap);
                    }
                } else {
                    if (leafletMap.hasLayer(marker)) {
                        leafletMap.removeLayer(marker);
                    }
                }
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
            setActiveMarker(locationId);

            listView.hidden = true;
            detailView.hidden = false;

            // Inisialisasi galeri setelah panel detail ditampilkan
            renderMediaGallery(location);

            document.getElementById('detailContent').scrollTop = 0;
        }

        // Navigasi kembali dari detail ke daftar lokasi.
        document.getElementById('backButton').addEventListener('click', () => {
            detailPlayer?.pause();
            detailView.hidden = true;
            listView.hidden = false;
            setActiveMarker(null);
        });

        // Inisialisasi interaksi peta: wheel zoom, drag, tombol zoom, dan resize.
        if (document.readyState === 'complete') {
            initializeLeafletMap().catch(error => console.error('Inisialisasi peta gagal:', error));
        } else {
            window.addEventListener('load', () => {
                initializeLeafletMap().catch(error => console.error('Inisialisasi peta gagal:', error));
            }, {
                once: true
            });

        }

        // Inisialisasi pencarian lokasi dan render awal.
        const searchLocation = document.getElementById('searchLocation');
        searchLocation.addEventListener('input', (event) => {
            filterLocations(event.target.value);
        });

        renderList();
    </script>
    @include('components.location-gallery')
</body>

</html>
