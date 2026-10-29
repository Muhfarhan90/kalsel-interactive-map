<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $menu->title ?: $menu->name }} - Kalimantan Selatan</title>

    <link rel="icon" type="image/png" href="{{ asset($homepage->header_logo) }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .province-regency-marker {
            background: transparent;
            border: none;
            cursor: pointer;
        }

        .province-regency-marker svg {
            display: block;
            filter: drop-shadow(0 2px 3px rgba(218, 61, 61, 0.25));
            transition: scale 150ms ease;
        }

        .province-regency-marker:hover svg,
        .province-regency-marker:focus-visible svg {
            scale: 1.12;
        }


        /* GeoJSON polygon interaktif; warna konsisten dengan tampilan publik. */
        #provinceLeafletMap .leaflet-interactive {
            cursor: pointer;
        }
    </style>
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

                    <div id="mapFrame"
                        class="relative mx-auto aspect-square w-full max-w-[calc(100vh-6rem)] overflow-hidden rounded-lg border-[6px] max-[850px]:max-w-[35dvh]"
                        style="border-color: var(--page-header-background)">

                        <div id="provinceLeafletMap" class="absolute inset-0 z-0 size-full rounded-sm"></div>

                        <p id="provinceMapNotice" role="alert"
                            class="pointer-events-none absolute bottom-2 left-2 right-2 z-[1000] hidden rounded bg-white/95 p-2 text-xs text-red-700 shadow">
                        </p>

                        {{-- Kontrol peta sama dengan halaman menu --}}
                        <div class="absolute right-2 top-2 z-[1000] flex overflow-hidden rounded-lg bg-white/90 shadow-md"
                            aria-label="Kontrol zoom peta">
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

    @php
        // Nomor marker mengikuti urutan daftar kabupaten/kota di sebelah kanan.
        $mapRegencies = $regencies
            ->values()
            ->map(function ($regency, $index) use ($menu) {
                return [
                    'id' => $regency->id,
                    'name' => $regency->name,
                    'slug' => $regency->slug,
                    'number' => $index + 1,
                    'latitude' => $regency->latitude,
                    'longitude' => $regency->longitude,
                    'url' => route('regencies.menu', [
                        'regency' => $regency->slug,
                        'menu' => $menu->slug,
                    ]),
                ];
            })
            ->all();
    @endphp

    <script>
        (() => {
            const regencies = @js($mapRegencies);
            const geoJsonUrl = @js(asset('geojson/regency_kalsel.geojson'));
            const mapElement = document.getElementById('provinceLeafletMap');
            const notice = document.getElementById('provinceMapNotice');

            // GeoJSON memakai "shapeName" (bukan "name"/"slug").
            // Normalisasi agar cocok dengan nama / slug tabel regencies.
            function normalizeRegencyName(value) {
                let name = String(value ?? '')
                    .toLowerCase()
                    .replace(/[_-]+/g, ' ')
                    .replace(/\s+/g, ' ')
                    .trim();

                // Alias khusus berdasarkan isi file GeoJSON yang diunggah.
                if (name === 'kota baru' || name === 'kabupaten kota baru') {
                    return 'kotabaru';
                }
                if (name === 'kota banjar baru') {
                    return 'banjarbaru';
                }

                return name
                    .replace(/^(kabupaten|kota)\s+/, '')
                    .replace(/[^a-z0-9]/g, '');
            }

            function getCoordinates(regency) {
                if (regency.latitude == null || regency.longitude == null ||
                    regency.latitude === '' || regency.longitude === '') {
                    return null;
                }

                const lat = Number(regency.latitude);
                const lng = Number(regency.longitude);

                return Number.isFinite(lat) && Number.isFinite(lng) &&
                    Math.abs(lat) <= 90 && Math.abs(lng) <= 180 ? [lat, lng] :
                    null;
            }

            function createRegencyIcon(L, number) {
                return L.divIcon({
                    className: 'province-regency-marker',
                    iconSize: [40, 52],
                    iconAnchor: [20, 52],
                    html: `
                        <svg viewBox="0 0 36 46" width="40" height="52" aria-hidden="true">
                            <path d="M18 1.5C9.2 1.5 2 8.4 2 16.9C2 29.1 18 44.5 18 44.5S34 29.1 34 16.9C34 8.4 26.8 1.5 18 1.5Z"
                                fill="#da251d" stroke="white" stroke-width="2.5" />
                            <circle cx="18" cy="17" r="8" fill="white" />
                            <text x="18" y="20.5" text-anchor="middle" fill="#da251d"
                                font-size="10" font-weight="700">${number}</text>
                        </svg>
                    `
                });
            }

            async function initializeProvinceMap() {
                const L = window.L;
                if (!mapElement || !L) {
                    console.error('Leaflet belum tersedia. Periksa import window.L di resources/js/app.js.');
                    return;
                }

                const map = L.map(mapElement, {
                    zoomControl: false,
                    zoomSnap: 0.25,
                    zoomDelta: 0.5,
                    maxZoom: 19,
                    maxBoundsViscosity: 1.0,
                    bounceAtZoomLimits: false,
                    scrollWheelZoom: true,
                    touchZoom: true,
                    attributionControl: false,
                });

                // Map online
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                // Map offline
                // window.createOfflineBaseMap().addTo(map);

                const byName = new Map();
                regencies.forEach(regency => {
                    byName.set(normalizeRegencyName(regency.name), regency);
                    byName.set(normalizeRegencyName(regency.slug), regency);
                });

                // Jika koordinat regency belum disimpan di admin, marker tetap
                // dapat memakai pusat bounding box polygon sebagai fallback.
                const geoCenters = new Map();
                let provinceBounds = L.latLngBounds([-4.65, 114.0], [-1.40, 118.0]);

                try {
                    const response = await fetch(geoJsonUrl);
                    if (!response.ok) {
                        throw new Error(`GeoJSON gagal dimuat (HTTP ${response.status})`);
                    }

                    const geojson = await response.json();
                    if (geojson?.type !== 'FeatureCollection' || !Array.isArray(geojson.features)) {
                        throw new Error('Format GeoJSON harus FeatureCollection dengan array features.');
                    }

                    const themeColor = getComputedStyle(document.body)
                        .getPropertyValue('--page-header-background').trim() || '#da251d';

                    let regionLayer;
                    regionLayer = L.geoJSON(geojson, {
                        style: (feature) => {
                            const properties = feature.properties ?? {};

                            return {
                                color: '#966a43',
                                weight: Number(properties['stroke-width'] ?? 1),
                                opacity: 1,
                                fillColor: properties.fill || '#d1b38a',
                                fillOpacity: 0.5
                            };
                        },
                        onEachFeature(feature, layer) {
                            const name = feature.properties?.shapeName ?? 'Wilayah';
                            const key = normalizeRegencyName(name);
                            const regency = byName.get(key);

                            if (!regency) {
                                console.warn('Nama wilayah GeoJSON belum cocok dengan database:', name);
                            } else {
                                geoCenters.set(key, layer.getBounds().getCenter());
                            }

                            const label = document.createElement('span');
                            label.textContent = regency?.name ?? name;
                            layer.bindTooltip(label, {
                                sticky: true,
                                direction: 'top'
                            });

                            if (!regency) return;

                            layer.on('mouseover', () => {
                                layer.setStyle({
                                    color: themeColor,
                                    weight: 3,
                                    fillOpacity: 0.46
                                });
                                layer.bringToFront();
                            });
                            layer.on('mouseout', () => regionLayer.resetStyle(layer));
                            layer.on('click', () => window.location.assign(regency.url));
                        }
                    }).addTo(map);

                    if (regionLayer.getBounds().isValid()) {
                        // Batas peta mengikuti SELURUH 13 polygon, bukan angka manual.
                        provinceBounds = regionLayer.getBounds();
                    }
                } catch (error) {
                    console.error('Gagal membaca GeoJSON provinsi:', error);
                    if (notice) {
                        notice.textContent = 'Batas wilayah belum bisa dimuat. Peta dan marker tetap tersedia.';
                        notice.classList.remove('hidden');
                    }
                }

                // Marker custom, tetap memakai nomor sesuai daftar sebelah kanan.
                regencies.forEach(regency => {
                    const key = normalizeRegencyName(regency.name);
                    const coordinates = getCoordinates(regency) ?? geoCenters.get(key);

                    if (!coordinates) {
                        console.warn('Marker wilayah tidak punya koordinat / polygon:', regency.name);
                        return;
                    }

                    const marker = L.marker(coordinates, {
                        icon: createRegencyIcon(L, regency.number),
                        title: `${regency.number}. ${regency.name}`,
                        keyboard: true,
                        riseOnHover: true
                    }).addTo(map);

                    marker.on('click', () => window.location.assign(regency.url));
                    const element = marker.getElement();
                    if (element) {
                        element.style.color = '#da251d';
                        element.setAttribute('aria-label', `Buka ${regency.name}, nomor ${regency.number}`);
                    }
                });

                let resetCenter = null;
                let resetZoom = null;
                let computingReset = false;
                let restoring = false;

                function syncMinimumView() {
                    if (computingReset || restoring || !resetCenter) return;

                    const atMinimum = map.getZoom() <= resetZoom + 0.001;
                    if (atMinimum) {
                        map.dragging.disable();
                        if (map.getCenter().distanceTo(resetCenter) > 1) {
                            restoring = true;
                            map.panTo(resetCenter, {
                                animate: false
                            });
                            restoring = false;
                        }
                    } else {
                        map.dragging.enable();
                    }
                }

                function setInitialViewport() {
                    computingReset = true;
                    map.stop();
                    map.setMaxBounds(null);
                    map.setMinZoom(0);

                    map.fitBounds(provinceBounds, {
                        padding: [16, 16],
                        animate: false
                    });

                    resetCenter = map.getCenter();
                    resetZoom = map.getZoom();
                    const resetBounds = map.getBounds();

                    map.setMinZoom(resetZoom);
                    // Area yang boleh dilihat ketika zoom-in tidak boleh
                    // melebihi cakupan geografis dari tampilan Reset.
                    map.setMaxBounds(resetBounds);
                    computingReset = false;
                    syncMinimumView();
                }

                map.on('zoomend moveend', syncMinimumView);
                map.on('resize', setInitialViewport);
                setInitialViewport();

                if (typeof ResizeObserver !== 'undefined') {
                    const observer = new ResizeObserver(() => {
                        map.invalidateSize({
                            animate: false,
                            pan: false
                        });
                    });
                    observer.observe(mapElement);
                }

                document.getElementById('zoomInButton')?.addEventListener('click', () => {
                    map.zoomIn(0.5);
                });
                document.getElementById('zoomOutButton')?.addEventListener('click', () => {
                    map.zoomOut(0.5);
                });
                document.getElementById('resetMapButton')?.addEventListener('click', () => {
                    if (!resetCenter) return;
                    map.stop();
                    map.setView(resetCenter, resetZoom, {
                        animate: true
                    });
                });
            }

            if (document.readyState === 'complete') {
                initializeProvinceMap().catch(console.error);
            } else {
                window.addEventListener('load', () => {
                    initializeProvinceMap().catch(console.error);
                }, {
                    once: true
                });
            }
        })();
    </script>

</body>

</html>
