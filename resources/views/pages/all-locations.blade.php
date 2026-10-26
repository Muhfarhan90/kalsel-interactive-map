<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Peta Kalimantan Selatan - Semua Lokasi</title>

    <link rel="icon" type="image/png" href="{{ asset($homepage->header_logo) }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }

        .kalsel-location-marker {
            background: transparent;
            border: 0;
        }

        .kalsel-location-marker svg {
            display: block;
            transform-origin: center bottom;
        }

        .kalsel-location-marker.is-active svg {
            scale: 1.12;
            filter: drop-shadow(0 2px 5px rgba(0, 0, 0, .25));
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

            {{-- PETA --}}
            <div class="col-span-7 min-w-0 max-[850px]:col-span-1"
                style="background-color: color-mix(in srgb, var(--page-header-background) 14%, white)">

                <div class="p-3">
                    <div id="mapFrame"
                        class="relative mx-auto aspect-square w-full max-w-[calc(100vh-6rem)] overflow-hidden rounded-lg border-[6px] max-[850px]:max-w-[35dvh]"
                        style="border-color: var(--page-header-background)">

                        <div id="leafletMap" class="absolute inset-0 z-0 size-full rounded-sm">
                        </div>

                        <div id="mapNotice"
                            class="absolute bottom-9 left-3 z-[600] hidden rounded-md bg-white/90 px-3 py-2 text-xs text-gray-700 shadow">
                        </div>

                        {{-- Kontrol zoom --}}
                        <div
                            class="absolute right-2 top-2 z-[1000] flex overflow-hidden rounded-lg bg-white/90 shadow-md">

                            <button type="button" id="zoomInButton"
                                class="grid size-9 cursor-pointer place-items-center text-xl font-bold hover:bg-gray-100">
                                +
                            </button>

                            <button type="button" id="zoomOutButton"
                                class="grid size-9 cursor-pointer place-items-center border-l border-gray-200 text-xl font-bold hover:bg-gray-100">
                                −
                            </button>

                            <button type="button" id="resetMapButton"
                                class="grid size-9 cursor-pointer place-items-center border-l border-gray-200 text-lg font-bold hover:bg-gray-100">
                                ↺
                            </button>

                        </div>
                    </div>
                </div>
            </div>

            {{-- PANEL KANAN --}}
            <aside class="col-span-5 min-h-0 min-w-0 p-3 max-[850px]:col-span-1">

                {{-- DAFTAR LOKASI --}}
                <div id="listView" class="h-full min-h-0 overflow-hidden">

                    <div class="flex h-full min-h-0 flex-col">

                        <header class="shrink-0 border-b border-gray-200 pb-2">

                            <div class="flex flex-wrap items-center justify-between gap-3">

                                <div class="flex min-w-0 items-center gap-2.5">

                                    <img src="{{ asset($menu->logo ?: 'images/logo/logo-wonderful-kalsel.svg') }}"
                                        alt="Logo Kalimantan Selatan"
                                        class="h-auto w-16 shrink-0 object-contain md:w-28">

                                    <div class="min-w-0">
                                        <h2 class="m-0 break-words text-2xl font-bold"
                                            style="color: {{ $menu->color }}">
                                            Kalimantan Selatan
                                        </h2>

                                        <p class="text-sm text-gray-500">
                                            {{ $menu->name }}
                                        </p>

                                        <p id="locationCount" class="text-xs text-gray-500">
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">

                                    <div class="relative">
                                        <i
                                            class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>

                                        <input type="search" id="searchLocation" placeholder="Cari lokasi..."
                                            autocomplete="off"
                                            class="h-10 w-52 rounded-lg border border-gray-400 bg-white pl-9 pr-3 text-sm outline-none focus:border-red-500 max-[520px]:w-28">
                                    </div>

                                    <a href="{{ route('home') }}" data-public-page-link
                                        class="inline-flex h-10 items-center rounded-lg px-3 py-2 text-sm font-bold text-white hover:opacity-90"
                                        style="background-color: var(--page-header-background)">
                                        Kembali
                                    </a>

                                </div>
                            </div>
                        </header>

                        <div id="categoryScroll" tabindex="0"
                            class="min-h-0 flex-1 overflow-x-hidden overflow-y-auto overscroll-contain pt-2.5 pr-1">

                            <div id="categoryGrid" class="grid grid-cols-2 gap-2.5 max-[520px]:grid-cols-1">
                            </div>

                        </div>
                    </div>
                </div>

                {{-- DETAIL LOKASI --}}
                <div id="detailView" class="h-full" hidden>

                    <article class="flex h-full min-w-0 flex-col overflow-hidden rounded-lg border border-gray-200">

                        <header class="shrink-0 border-b border-gray-200 p-4 pb-2">

                            <div class="mb-2 flex items-center justify-between gap-3">

                                <div class="flex min-w-0 flex-1 items-center gap-2.5">

                                    <span id="detailNumber"
                                        class="grid size-8 shrink-0 place-items-center rounded-full text-sm font-bold text-white">
                                    </span>

                                    <h2 id="detailName"
                                        class="m-0 min-w-0 break-words text-2xl font-bold max-[520px]:text-xl">
                                    </h2>
                                </div>

                                <div class="ml-auto flex shrink-0 items-center gap-2">

                                    <button id="fullscreenButton" type="button" @click="openModal()"
                                        :aria-expanded="fullscreenOpen" aria-controls="detailModal"
                                        class="inline-flex min-h-10 cursor-pointer items-center rounded-lg px-3 py-2 text-sm font-bold text-white"
                                        style="background-color: var(--page-header-background)">
                                        Perluas
                                    </button>

                                    <button id="backButton" type="button"
                                        class="inline-flex min-h-10 cursor-pointer items-center rounded-lg px-3 py-2 text-sm font-bold text-white"
                                        style="background-color: var(--page-header-background)">
                                        Kembali ke daftar
                                    </button>

                                </div>
                            </div>

                            <div id="detailCategory"
                                class="inline-flex items-center gap-1.5 rounded-full px-2.5 text-lg font-semibold">
                            </div>
                        </header>

                        <div id="detailContent" class="min-h-0 flex-1 overflow-y-auto p-4">

                            <div id="detailMedia"
                                class="relative mb-4 grid aspect-video place-items-center overflow-hidden rounded-lg bg-black text-white">
                            </div>

                            <p class="mb-4 text-sm text-gray-600">
                                <strong>Sumber:</strong>
                                <span id="detailSource"></span>
                            </p>

                            <p id="detailAddress" class="mb-4 text-base font-bold leading-6">
                            </p>

                            <div id="detailDescription"
                                class="text-justify leading-7 [&_h1]:mb-3 [&_h1]:text-3xl [&_h1]:font-bold [&_h2]:mb-3 [&_h2]:text-2xl [&_h2]:font-bold [&_h3]:mb-2 [&_h3]:text-xl [&_h3]:font-semibold [&_ul]:list-disc [&_ol]:list-decimal [&_li]:ml-5">
                            </div>

                        </div>
                    </article>
                </div>

            </aside>
        </section>
    </main>

    {{-- MODAL FULLSCREEN --}}
    <x-ui.modal id="detailModal" x-model="fullscreenOpen" aria-labelledby="fullscreenDetailName"
        panelClass="h-[100dvh] max-h-[100dvh] w-screen max-w-none overflow-hidden p-0 mt-8">

        <article class="flex h-full max-h-full min-w-0 flex-col">

            <header class="shrink-0 border-b border-gray-200 p-4 pr-14 sm:p-6">

                <div class="flex items-start gap-3">

                    <span id="fullscreenDetailNumber"
                        class="grid size-10 shrink-0 place-items-center rounded-full text-lg font-bold text-white">
                    </span>

                    <h2 id="fullscreenDetailName" class="break-words text-2xl font-bold lg:text-3xl">
                    </h2>

                </div>
            </header>

            <div id="fullscreenDetailContent" class="min-h-0 flex-1 overflow-y-auto">

                <div id="fullscreenDetailMedia"
                    class="relative flex h-[calc(100dvh-6rem)] min-h-[60dvh] w-full items-center justify-center overflow-hidden bg-black text-white [&_img]:max-h-full [&_img]:max-w-full [&_img]:object-contain [&_video]:max-h-full [&_video]:max-w-full">
                </div>

                <div class="p-4 md:p-6">

                    <p class="mb-4">
                        <strong>Sumber:</strong>
                        <span id="fullscreenDetailSource"></span>
                    </p>

                    <p id="fullscreenDetailAddress" class="mb-4 font-bold">
                    </p>

                    <div id="fullscreenDetailDescription"
                        class="leading-8 [&_h1]:mb-3 [&_h1]:text-3xl [&_h1]:font-bold [&_h2]:mb-3 [&_h2]:text-2xl [&_h2]:font-bold [&_h3]:mb-2 [&_h3]:text-xl [&_h3]:font-semibold [&_ul]:list-disc [&_ol]:list-decimal [&_li]:ml-6">
                    </div>

                </div>
            </div>
        </article>
    </x-ui.modal>

    <script>
        // Modal fullscreen: media yang sama dipindahkan,
        // sehingga posisi video tetap tersinkronisasi.
        function locationFullscreenModal() {
            return {
                fullscreenOpen: false,
                mediaMoved: false,
                inertElements: [],

                init() {
                    this.$watch('fullscreenOpen', (open) => {
                        if (!open) this.stopPlayback();
                    });
                },

                openModal() {
                    if (this.fullscreenOpen) return;

                    ['Name', 'Number', 'Source', 'Address'].forEach(field => {
                        document.getElementById('fullscreenDetail' + field)
                            .textContent =
                            document.getElementById('detail' + field).textContent;
                    });

                    document.getElementById('fullscreenDetailNumber')
                        .style.backgroundColor =
                        document.getElementById('detailNumber')
                        .style.backgroundColor;

                    document.getElementById('fullscreenDetailDescription')
                        .innerHTML =
                        document.getElementById('detailDescription').innerHTML;

                    const source = document.getElementById('detailMedia');
                    const target = document.getElementById('fullscreenDetailMedia');

                    const wasPlaying = Boolean(detailPlayer && !detailPlayer.paused);

                    detailPlayer?.pause();

                    // Memindahkan DOM media, bukan membuat video baru.
                    target.replaceChildren(...source.childNodes);

                    this.mediaMoved = true;

                    this.inertElements = [...document.body.children]
                        .filter(el => ['HEADER', 'MAIN'].includes(el.tagName) &&
                            !el.inert
                        );

                    this.inertElements.forEach(el => el.inert = true);

                    this.fullscreenOpen = true;

                    this.$nextTick(() => {
                        document.getElementById('fullscreenDetailContent')
                            .scrollTop = 0;

                        if (wasPlaying && detailPlayer) {
                            Promise.resolve(detailPlayer.play()).catch(() => {});
                        }
                    });
                },

                stopPlayback() {
                    if (!this.mediaMoved) return;

                    const source = document.getElementById('detailMedia');
                    const target = document.getElementById('fullscreenDetailMedia');

                    const wasPlaying = Boolean(detailPlayer && !detailPlayer.paused);

                    detailPlayer?.pause();

                    source.replaceChildren(...target.childNodes);

                    this.mediaMoved = false;

                    this.inertElements.forEach(el => el.inert = false);
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
                    )].filter(el =>
                        !el.disabled &&
                        el.tabIndex >= 0 &&
                        el.getClientRects().length
                    );

                    const first = focusable[0];
                    const last = focusable.at(-1);

                    if (!first) return;

                    if (event.shiftKey && document.activeElement === first) {
                        event.preventDefault();
                        last.focus();
                    } else if (!event.shiftKey && document.activeElement === last) {
                        event.preventDefault();
                        first.focus();
                    }
                }
            };
        }
    </script>

    <script>
        // DATA DARI CONTROLLER
        const locations = @json($locations);
        const geoJsonUrl = @js(asset('geojson/regency_kalsel.geojson'));

        const categoryGrid = document.getElementById('categoryGrid');
        const listView = document.getElementById('listView');
        const detailView = document.getElementById('detailView');
        const locationCount = document.getElementById('locationCount');

        const locationMarkers = new Map();
        const locationById = new Map(
            locations.map(location => [Number(location.id), location])
        );

        let leafletMap = null;
        let detailPlayer = null;

        const escapeHtml = value => String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

        const markerColor = location => {
            const color = String(location.color ?? '');

            return /^#[0-9a-f]{3}(?:[0-9a-f]{3})?$/i.test(color) ?
                color :
                '#da251d';
        };

        function safeDescription(html) {
            const template = document.createElement('template');
            template.innerHTML = html ?? '';

            template.content.querySelectorAll(
                'script, style, iframe, object, svg, math'
            ).forEach(el => el.remove());

            const allowed = new Set([
                'P', 'H1', 'H2', 'H3', 'BR', 'STRONG', 'B',
                'EM', 'I', 'U', 'UL', 'OL', 'LI', 'BLOCKQUOTE'
            ]);

            template.content.querySelectorAll('*').forEach(el => {
                if (!allowed.has(el.tagName)) {
                    el.replaceWith(...el.childNodes);
                    return;
                }

                [...el.attributes].forEach(attr => {
                    el.removeAttribute(attr.name);
                });
            });

            return template.innerHTML;
        }

        // KELOMPOKKAN BERDASARKAN MENU + KATEGORI
        const groupKey = location =>
            `${location.menu_id}:${location.category_id}`;

        const groupedLocations = new Map();
        const categoryNumbers = new Map();

        locations.forEach(location => {
            const key = groupKey(location);

            if (!groupedLocations.has(key)) {
                groupedLocations.set(key, {
                    menu: location.menu,
                    category: location.category,
                    color: markerColor(location),
                    icon: location.icon,
                    background: location.background,
                    locations: []
                });
            }

            const group = groupedLocations.get(key);

            group.locations.push(location);
            categoryNumbers.set(Number(location.id), group.locations.length);
        });

        const getCategoryNumber = location =>
            categoryNumbers.get(Number(location.id)) ?? 0;

        // VALIDASI KOORDINAT
        function getLocationCoordinates(location) {
            if (
                location.latitude == null ||
                location.longitude == null ||
                location.latitude === '' ||
                location.longitude === ''
            ) return null;

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

        // MARKER SVG SEPERTI PADA MENU LAMA
        function createMarkerIcon(location) {
            const number = getCategoryNumber(location);
            const color = markerColor(location);

            return L.divIcon({
                className: 'kalsel-location-marker',
                iconSize: [40, 52],
                iconAnchor: [20, 52],
                html: `
                    <svg viewBox="0 0 36 46"
                         width="40" height="52"
                         style="color:${color}"
                         aria-hidden="true">

                        <path
                            d="M18 1.5C9.2 1.5 2 8.4 2 16.9C2 29.1 18 44.5 18 44.5S34 29.1 34 16.9C34 8.4 26.8 1.5 18 1.5Z"
                            fill="currentColor"
                            stroke="white"
                            stroke-width="2.5"/>

                        <circle cx="18" cy="17" r="8" fill="white"/>

                        <text class="marker-number"
                              x="18" y="20.5"
                              text-anchor="middle"
                              fill="currentColor"
                              font-size="10"
                              font-weight="700">${number}</text>
                    </svg>
                `
            });
        }

        function renderMarkers() {
            locations.forEach(location => {
                const coordinates = getLocationCoordinates(location);

                if (!coordinates) {
                    console.warn(
                        'Koordinat tidak valid:',
                        location.name,
                        location.latitude,
                        location.longitude
                    );
                    return;
                }

                const marker = L.marker(coordinates, {
                    icon: createMarkerIcon(location),
                    title: location.name,
                    riseOnHover: true,
                    keyboard: true
                }).addTo(leafletMap);

                marker.on('click', () => {
                    showDetail(Number(location.id));
                });

                locationMarkers.set(Number(location.id), marker);
            });
        }

        function setActiveMarker(locationId = null) {
            locationMarkers.forEach((marker, id) => {
                const active = id === locationId;
                const el = marker.getElement();

                el?.classList.toggle('is-active', active);
                el?.querySelector('.marker-number')
                    ?.classList.toggle('hidden', active);

                marker.setZIndexOffset(active ? 1000 : 0);
            });
        }

        // INISIALISASI LEAFLET SELURUH KALSEL
        async function initializeLeafletMap() {
            const notice = document.getElementById('mapNotice');

            if (!window.L) {
                console.error('Leaflet belum tersedia.');
                notice.textContent = 'Library Leaflet belum dimuat.';
                notice.classList.remove('hidden');
                return;
            }

            const fallbackBounds = L.latLngBounds(
                [-4.65, 114.0],
                [-1.4, 118.0]
            );

            let provinceBounds = fallbackBounds;

            leafletMap = L.map('leafletMap', {
                center: [-3.1, 115.4],
                zoom: 8,
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

            L.tileLayer(
                'https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap contributors'
                }
            ).addTo(leafletMap);

            // POLYGON SEMUA KABUPATEN/KOTA
            try {
                const response = await fetch(geoJsonUrl);

                if (!response.ok) {
                    throw new Error(`GeoJSON HTTP ${response.status}`);
                }

                const geojson = await response.json();

                if (
                    geojson.type !== 'FeatureCollection' ||
                    !Array.isArray(geojson.features)
                ) {
                    throw new Error('Format GeoJSON tidak valid.');
                }


                const polygonLayer = L.geoJSON(geojson, {
                    interactive: false,

                    style(feature) {
                        const properties = feature.properties ?? {};

                        return {
                            // Warna garis batas kabupaten/kota
                            color: '#78350f',

                            // Ketebalan garis polygon
                            weight: 2.5,

                            // Garis terlihat penuh
                            opacity: 1,

                            // Warna area berdasarkan GeoJSON
                            fillColor: properties.fill || '#a8a29e',

                            // Transparansi area polygon
                            fillOpacity: 0.12
                        };
                    }
                }).addTo(leafletMap);

                // Pastikan batas polygon berada di atas basemap
                polygonLayer.bringToFront();

                // Gunakan batas seluruh wilayah untuk zoom awal
                if (polygonLayer.getBounds().isValid()) {
                    provinceBounds = polygonLayer.getBounds();
                }

                console.log('Jumlah polygon:', polygonLayer.getLayers().length);


            } catch (error) {
                console.error('Gagal memuat GeoJSON:', error);

                notice.textContent =
                    'Batas wilayah belum tersedia. Lokasi tetap dapat dilihat.';
                notice.classList.remove('hidden');
            }

            let resetCenter = null;
            let resetZoom = null;
            let calculating = false;
            let restoring = false;

            function syncMinimumView() {
                if (calculating || restoring || !resetCenter) return;

                const minimum =
                    leafletMap.getZoom() <= resetZoom + 0.001;

                if (minimum) {
                    leafletMap.dragging.disable();

                    if (
                        leafletMap.getCenter().distanceTo(resetCenter) > 1
                    ) {
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

                leafletMap.invalidateSize();
                leafletMap.setMaxBounds(null);
                leafletMap.setMinZoom(0);

                leafletMap.fitBounds(provinceBounds, {
                    padding: [18, 18],
                    animate: false
                });

                resetCenter = leafletMap.getCenter();
                resetZoom = leafletMap.getZoom();

                leafletMap.setMaxBounds(leafletMap.getBounds());
                leafletMap.setMinZoom(resetZoom);

                calculating = false;
                syncMinimumView();
            }

            leafletMap.on('zoomend moveend', syncMinimumView);
            leafletMap.on('resize', calculateResetView);

            calculateResetView();
            renderMarkers();

            document.getElementById('zoomInButton')
                .addEventListener('click', () => {
                    leafletMap.zoomIn(0.5);
                });

            document.getElementById('zoomOutButton')
                .addEventListener('click', () => {
                    leafletMap.zoomOut(0.5);
                });

            document.getElementById('resetMapButton')
                .addEventListener('click', () => {
                    leafletMap.stop();

                    leafletMap.setView(resetCenter, resetZoom, {
                        animate: true
                    });
                });
        }

        // LIVE SEARCH
        const matchesLocation = (location, keyword) => [
            location.name,
            location.address,
            location.menu,
            location.category,
            location.regency
        ].some(value =>
            String(value ?? '').toLowerCase().includes(keyword)
        );

        function renderList(search = '') {
            const keyword = search.toLowerCase().trim();

            let total = 0;

            categoryGrid.innerHTML = [...groupedLocations.values()]
                .map(group => {
                    const filtered = group.locations.filter(location =>
                        matchesLocation(location, keyword)
                    );

                    if (!filtered.length) return '';

                    total += filtered.length;

                    const color = group.color;
                    const icon = String(group.icon || 'location-dot')
                        .replace(/[^a-z0-9-]/gi, '');

                    return `
                        <section
                            class="flex flex-col overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">

                            <h3
                                class="m-0 flex items-center gap-2 px-2.5 py-2.5 text-md font-bold"
                                style="background-color:color-mix(in srgb, ${color} 14%, white)">

                                <span
                                    class="grid size-8 shrink-0 place-items-center rounded-lg bg-white"
                                    style="color:${color}">
                                    <i class="fas fa-${icon}"></i>
                                </span>

                                <span style="color:${color}">
                                    ${escapeHtml(group.category)}
                                </span>
                            </h3>

                            <div class="relative flex-1 px-2.5 py-1">
                                ${group.background ? `
                                            <img
                                                src="${escapeHtml(group.background)}"
                                                alt=""
                                                class="pointer-events-none absolute inset-0 size-full object-cover">

                                            <div
                                                class="pointer-events-none absolute inset-0 bg-gradient-to-r from-white via-white/85 to-transparent">
                                            </div>
                                        ` : ''}

                                <div class="relative">
                                    ${filtered.map(location => `
                                                <button
                                                    type="button"
                                                    data-location-id="${Number(location.id)}"
                                                    class="flex w-full cursor-pointer items-center gap-2 border-0 bg-transparent py-2 text-left text-sm text-gray-800">

                                                    <span
                                                        class="grid size-[22px] shrink-0 place-items-center rounded-full text-[10px] font-bold text-white"
                                                        style="background-color:${markerColor(location)}">
                                                        ${getCategoryNumber(location)}
                                                    </span>

                                                    <span class="min-w-0 flex-1">
                                                        <span class="block break-words">
                                                            ${escapeHtml(location.name)}
                                                        </span>
                                                    </span>
                                                </button>
                                            `).join('')}
                                </div>
                            </div>
                        </section>
                    `;
                })
                .join('');

            if (!total) {
                categoryGrid.innerHTML = `
                    <p class="col-span-full py-10 text-center text-sm text-gray-500">
                        Lokasi tidak ditemukan.
                    </p>
                `;
            }

            locationCount.textContent =
                `${total} lokasi ditampilkan`;

            categoryGrid.querySelectorAll('[data-location-id]')
                .forEach(button => {
                    button.addEventListener('click', () => {
                        showDetail(Number(button.dataset.locationId));
                    });
                });
        }

        function filterLocations(search = '') {
            const keyword = search.toLowerCase().trim();

            renderList(keyword);

            if (!leafletMap) return;

            locationMarkers.forEach((marker, id) => {
                const location = locationById.get(id);
                if (!location) return;

                const matches = matchesLocation(location, keyword);

                if (matches && !leafletMap.hasLayer(marker)) {
                    marker.addTo(leafletMap);
                } else if (!matches && leafletMap.hasLayer(marker)) {
                    leafletMap.removeLayer(marker);
                }
            });
        }

        // DETAIL LOKASI
        function showDetail(locationId) {
            const location = locationById.get(Number(locationId));

            if (!location) return;

            const category = document.getElementById('detailCategory');
            const color = markerColor(location);

            category.textContent = [location.menu, location.category]
                .filter(Boolean)
                .join(' · ');

            category.style.color = color;

            const number = document.getElementById('detailNumber');
            number.textContent = getCategoryNumber(location);
            number.style.backgroundColor = color;

            document.getElementById('detailName')
                .textContent = location.name;

            document.getElementById('detailAddress')
                .textContent = [
                    location.address,
                    location.regency
                ].filter(Boolean).join(' — ');

            document.getElementById('detailDescription')
                .innerHTML = safeDescription(location.description);

            document.getElementById('detailSource')
                .textContent = location.source_media || 'Sumber belum tersedia';

            const mediaElement = document.getElementById('detailMedia');
            const mediaUrl = location.media;

            detailPlayer?.destroy?.();
            detailPlayer = null;

            if (!mediaUrl) {
                mediaElement.innerHTML =
                    '<span>Media belum tersedia</span>';

            } else if (/\.mp4(?:\?.*)?$/i.test(mediaUrl)) {
                mediaElement.innerHTML = `
                    <video
                        class="block h-full w-full"
                        controls
                        playsinline
                        preload="metadata">
                        <source
                            src="${escapeHtml(mediaUrl)}"
                            type="video/mp4">
                    </video>
                `;

                const video = mediaElement.querySelector('video');

                detailPlayer = window.Plyr ?
                    new window.Plyr(video, {
                        controls: [
                            'play-large', 'restart', 'rewind', 'play',
                            'fast-forward', 'progress', 'current-time',
                            'duration', 'mute', 'volume', 'settings',
                            'pip', 'fullscreen'
                        ],
                        seekTime: 10,
                        settings: ['speed']
                    }) :
                    video;

            } else {
                mediaElement.innerHTML = `
                    <img
                        src="${escapeHtml(mediaUrl)}"
                        alt="Media ${escapeHtml(location.name)}"
                        class="absolute inset-0 size-full object-contain">
                `;
            }

            setActiveMarker(Number(locationId));

            listView.hidden = true;
            detailView.hidden = false;

            document.getElementById('detailContent').scrollTop = 0;
        }

        document.getElementById('backButton')
            .addEventListener('click', () => {
                detailPlayer?.pause();

                detailView.hidden = true;
                listView.hidden = false;

                setActiveMarker(null);
            });

        document.getElementById('searchLocation')
            .addEventListener('input', event => {
                filterLocations(event.target.value);
            });

        // JALANKAN
        renderList();

        window.addEventListener('load', () => {
            initializeLeafletMap().catch(error => {
                console.error('Gagal inisialisasi Leaflet:', error);
            });
        }, {
            once: true
        });
    </script>

</body>

</html>
