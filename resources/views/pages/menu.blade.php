<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $menu?->title ?: 'Peta Wisata Kalimantan Selatan' }}</title>
    @vite('resources/css/app.css')
</head>

<body
    class="public-page m-0 h-screen overflow-hidden bg-gray-100 font-sans text-gray-800 max-[850px]:h-auto max-[850px]:min-h-screen max-[850px]:overflow-y-auto"
    style="--page-header-background: {{ $menu->color }}">
    <x-public-page-header :settings="$pageHeader" background-color="{{ $menu->color }}" />

    <main class="h-[calc(100vh-4rem)] w-full max-[850px]:h-auto">
        <section class="grid h-full grid-cols-12 rounded-lg bg-white max-[850px]:h-auto max-[850px]:grid-cols-1">
            <div class="col-span-7 min-w-0"
                style="background-color: color-mix(in srgb, var(--page-header-background) 14%, white)">
                <div class="p-3">
                    <div class="relative mx-auto aspect-square w-full max-w-[calc(100vh-6rem)] border-[6px] rounded-lg"
                        style="border-color: var(--page-header-background)" id="mapFrame">
                        <img class="block size-full rounded-sm"
                            src="{{ asset($map?->map_image ?: 'images/maps/peta_provinsi_kalsel.png') }}"
                            alt="{{ $menu?->title ?: 'Peta Kalimantan Selatan' }}">
                    </div>
                </div>
            </div>

            <aside class="col-span-5 min-h-0 min-w-0 p-3">
                <div id="listView" class="h-full overflow-y-auto">
                    <div class="grid">
                        <header class="border-b border-gray-200 pb-2">
                            <div class="flex items-center justify-between gap-3 max-[520px]:items-start">
                                <div class="flex min-w-0 items-center gap-2.5">
                                    <img class="h-auto w-16 md:w-28 shrink-0 object-contain"
                                        src="{{ asset($menu?->logo ?: 'images/logo/logo_kalsel.svg') }}"
                                        alt="Logo {{ $menu?->title ?: 'Kalimantan Selatan' }}">
                                    <div class="min-w-0">
                                        <h2 class="m-0 break-words text-2xl font-bold"
                                            style="color: {{ $menu->color }}">
                                            {{ $menu?->title ?: 'Peta Wisata Kalimantan Selatan' }}</h2>
                                        @if ($menu?->sub_title)
                                            <p class="text-sm text-gray-500">{{ $menu->sub_title }}</p>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('home') }}" data-public-page-link
                                    class="inline-flex min-h-10 shrink-0 items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-bold text-white shadow-sm transition-opacity hover:opacity-90 focus-visible:outline-4 focus-visible:outline-offset-2 focus-visible:outline-red-200 max-[520px]:px-2 max-[520px]:text-xs"
                                    style="background-color: var(--page-header-background)"
                                    aria-label="Kembali ke halaman utama">
                                    Kembali
                                </a>
                            </div>
                        </header>

                        <div class="pt-2.5 pr-1">
                            <div class="grid grid-cols-2 gap-2.5 max-[520px]:grid-cols-1" id="categoryGrid"></div>
                            @if ($map?->map_background_image)
                                <x-map-background-banner :image="$map->map_background_image" :text="$map->map_background_text" />
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
                                <button
                                    class="inline-flex min-h-10 shrink-0 cursor-pointer items-center whitespace-nowrap rounded-lg border-0 px-3 py-2 text-sm font-bold text-white shadow-sm focus:outline-none focus-visible:ring-4 focus-visible:ring-gray-300 max-[520px]:px-2 max-[520px]:text-xs"
                                    style="background-color: var(--page-header-background)" id="backButton"
                                    type="button">
                                    Kembali ke daftar
                                </button>
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
                                <p class="m-0 text-sm leading-6 font-bold" id="detailAddress"></p>
                            </div>

                            <div class="text-justify [&_h1]:mb-3 [&_h1]:mt-6 [&_h1]:text-3xl [&_h1]:font-bold [&_h2]:mb-3 [&_h2]:mt-5 [&_h2]:text-2xl [&_h2]:font-bold [&_h3]:mb-2 [&_h3]:mt-4 [&_h3]:text-xl [&_h3]:font-semibold [&_ol]:list-decimal [&_ul]:list-disc [&_li]:ml-5"
                                id="detailDescription"></div>
                        </div>
                    </article>
                </div>
            </aside>
        </section>
    </main>

    <script>
        const locations = @json($locations);

        const mapFrame = document.getElementById('mapFrame');
        const categoryGrid = document.getElementById('categoryGrid');
        const listView = document.getElementById('listView');
        const detailView = document.getElementById('detailView');
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

        function renderMarkers() {
            locations.forEach((location) => {
                const categoryNumber = getCategoryNumber(location);
                const marker = document.createElement('button');
                marker.type = 'button';
                marker.className =
                    'pointer-events-none absolute z-10 grid size-12 origin-bottom -translate-x-1/2 -translate-y-full cursor-pointer place-items-end justify-items-center border-0 bg-transparent p-0 drop-shadow-md focus:outline-none focus-visible:ring-4 focus-visible:ring-red-200';
                marker.dataset.locationId = location.id;
                marker.style.left = location.x_location + '%';
                marker.style.top = location.y_location + '%';
                marker.style.color = location.color;
                marker.innerHTML =
                    '<svg class="h-10 w-8 overflow-visible" style="cursor:pointer" viewBox="0 0 36 46" aria-hidden="true">' +
                    '<path d="M18 1.5C9.2 1.5 2 8.4 2 16.9C2 29.1 18 44.5 18 44.5S34 29.1 34 16.9C34 8.4 26.8 1.5 18 1.5Z" fill="currentColor" stroke="white" stroke-width="2.5" style="pointer-events:visiblePainted"/>' +
                    '<circle cx="18" cy="17" r="8" fill="white" style="pointer-events:visiblePainted"/>' +
                    '<text class="marker-number" x="18" y="20.5" text-anchor="middle" font-size="10" font-weight="700" fill="currentColor" style="pointer-events:visiblePainted">' +
                    categoryNumber + '</text>' +
                    '</svg>';
                marker.title = location.name;
                marker.setAttribute('aria-label', 'Lihat detail ' + location.name);
                marker.addEventListener('click', () => showDetail(location.id));
                mapFrame.appendChild(marker);
            });
        }

        function renderList() {
            categoryGrid.innerHTML = Object.entries(groupedLocations).map(([category, group]) =>
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
                    '" alt="" aria-hidden="true" loading="lazy" class="pointer-events-none absolute inset-y-0 right-0 h-full w-[55%] object-cover object-center">' +
                    '<div class="pointer-events-none absolute inset-y-0 right-0 w-[60%] bg-gradient-to-r from-white via-white/85 to-transparent"></div>' :
                    '') +
                '<div class="relative">' +
                group.locations.map((location, index) =>
                    '<button class="flex w-full cursor-pointer items-center gap-2 border-0 bg-transparent py-2 text-left text-sm text-gray-800" type="button" data-location-id="' +
                    location.id + '">' +
                    '<span class="grid size-[22px] shrink-0 place-items-center rounded-full text-[10px] font-bold text-white" style="background-color: ' +
                    escapeHtml(location.color) + '">' +
                    (index + 1) +
                    '</span>' +
                    '<span class="category-list-text">' + escapeHtml(location.name) + '</span>' +
                    '</button>'
                ).join('') +
                '</div></div></section>'
            ).join('');

            if (categoryGrid.children.length % 2) categoryGrid.lastElementChild.style.gridColumn = '1 / -1';

            categoryGrid.querySelectorAll('[data-location-id]').forEach((button) => {
                button.addEventListener('click', () => showDetail(Number(button.dataset.locationId)));
            });
        }

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

            if (!mediaUrl) {
                detailMedia.innerHTML = '<span>Media belum tersedia</span>';
            } else if (isVideo) {
                detailMedia.innerHTML =
                    '<video class="absolute inset-0 block bg-black" style="width:100%;height:100%;object-fit:contain" autoplay controls playsinline preload="metadata" aria-label="Video ' +
                    escapeHtml(location.name) + '">' +
                    '<source src="' + escapeHtml(mediaUrl) + '">' +
                    'Browser tidak mendukung pemutaran video.' +
                    '</video>';
                detailMedia.querySelector('video').play().catch(() => {});
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

        document.getElementById('backButton').addEventListener('click', () => {
            detailView.querySelector('video')?.pause();
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

        renderMarkers();
        renderList();
    </script>
</body>

</html>
