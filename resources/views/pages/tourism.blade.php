<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $map?->map_title ?: 'Peta Wisata Kalimantan Selatan' }}</title>
    @vite('resources/css/app.css')
</head>

<body
    class="m-0 h-screen overflow-hidden bg-gray-100 font-sans text-gray-800 max-[850px]:h-auto max-[850px]:min-h-screen max-[850px]:overflow-y-auto">
    <header
        class="flex h-16 items-center justify-between border-b border-red-800 bg-[#da251d] px-4 py-1 text-[26px] font-bold text-white uppercase max-[520px]:px-2 max-[520px]:text-sm">
        <div class="min-w-0 flex-1 truncate">Kalimantan Selatan</div>
        <div class="shrink-0 whitespace-nowrap text-center tracking-wide">Interactive Map Guidance</div>
        <time class="min-w-0 flex-1 text-right tabular-nums" id="clock" aria-label="Waktu sekarang">--:--</time>
    </header>

    <main class="h-[calc(100vh-4rem)] w-full px-2 py-1 max-[850px]:h-auto">
        <section
            class="grid h-full grid-cols-12 rounded-lg bg-white max-[850px]:h-auto max-[850px]:grid-cols-1">
            <div class="col-span-7 min-w-0 bg-gray-50">
                <div class="p-3">
                    <div class="relative mx-auto aspect-square w-full max-w-[calc(100vh-6rem)]" id="mapFrame">
                        <img class="block size-full rounded"
                            src="{{ asset($map?->map_image ?: 'images/maps/peta_provinsi_kalsel.png') }}" alt="{{ $map?->map_title ?: 'Peta Kalimantan Selatan' }}">
                    </div>
                </div>
            </div>

            <aside class="col-span-5 min-h-0 min-w-0 p-3">
                <div id="listView" class="h-full overflow-y-auto">
                    <div class="grid">
                        <header class="border-b border-gray-200 pb-2">
                            <div class="flex items-center gap-2.5">
                                <img class="w-12 h-auto object-contain" src="{{ asset($map?->map_logo ?: 'images/logo/logo_kalsel.svg') }}"
                                    alt="Logo {{ $map?->map_title ?: 'Kalimantan Selatan' }}">
                                <div>
                                    <h2 class="m-0 text-2xl font-bold text-[#da251d]">{{ $map?->map_title ?: 'Peta Wisata Kalimantan Selatan' }}</h2>
                                    @if ($map?->map_sub_title)
                                        <p class="mt-1 text-sm leading-5 text-gray-500">{{ $map->map_sub_title }}</p>
                                    @endif
                                </div>
                            </div>
                        </header>

                        <div class="pt-2.5 pr-1">
                            <div class="grid grid-cols-2 gap-2.5 max-[520px]:grid-cols-1" id="categoryGrid"></div>
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
                                    <h2 class="m-0 min-w-0 break-words text-2xl font-bold max-[520px]:text-xl" id="detailName"></h2>
                                </div>
                                <button
                                    class="inline-flex min-h-10 shrink-0 cursor-pointer items-center whitespace-nowrap rounded-lg border-0 bg-[#da251d] px-3 py-2 text-sm font-bold text-white shadow-sm focus:outline-none focus-visible:ring-4 focus-visible:ring-red-200 max-[520px]:px-2 max-[520px]:text-xs"
                                    id="backButton" type="button">
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

                            <div class="text-justify [&_h1]:mb-3 [&_h1]:mt-6 [&_h1]:text-3xl [&_h1]:font-bold [&_h2]:mb-3 [&_h2]:mt-5 [&_h2]:text-2xl [&_h2]:font-bold [&_h3]:mb-2 [&_h3]:mt-4 [&_h3]:text-xl [&_h3]:font-semibold [&_ol]:list-decimal [&_ul]:list-disc [&_li]:ml-5" id="detailDescription"></div>
                        </div>
                    </article>
                </div>
            </aside>
        </section>
    </main>

    <script>
        const tourismLocations = @json($tourismLocations);

        const mapFrame = document.getElementById('mapFrame');
        const categoryGrid = document.getElementById('categoryGrid');
        const listView = document.getElementById('listView');
        const detailView = document.getElementById('detailView');
        const clock = document.getElementById('clock');

        function updateClock() {
            clock.textContent = new Intl.DateTimeFormat('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            }).format(new Date());
        }

        const escapeHtml = (value) => String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

        function safeDescription(html) {
            const template = document.createElement('template');
            template.innerHTML = html ?? '';
            template.content.querySelectorAll('script, style, iframe, object, svg, math').forEach((element) => element.remove());

            const allowedTags = new Set(['P', 'H1', 'H2', 'H3', 'BR', 'STRONG', 'B', 'EM', 'I', 'U', 'UL', 'OL', 'LI', 'BLOCKQUOTE']);
            template.content.querySelectorAll('*').forEach((element) => {
                if (!allowedTags.has(element.tagName)) {
                    element.replaceWith(...element.childNodes);
                    return;
                }

                [...element.attributes].forEach((attribute) => element.removeAttribute(attribute.name));
            });

            return template.innerHTML;
        }

        const groupedLocations = tourismLocations.reduce((groups, location) => {
            if (!groups[location.category]) {
                groups[location.category] = {
                    color: location.category_color,
                    icon: location.category_icon_svg,
                    locations: []
                };
            }

            groups[location.category].locations.push(location);
            return groups;
        }, {});

        const getCategoryNumber = (location) => tourismLocations
            .filter((item) => item.category === location.category)
            .findIndex((item) => item.id === location.id) + 1;

        function renderMarkers() {
            tourismLocations.forEach((location) => {
                const categoryNumber = getCategoryNumber(location);
                const marker = document.createElement('button');
                marker.type = 'button';
                marker.className =
                    'absolute z-10 grid size-12 origin-bottom -translate-x-1/2 -translate-y-full cursor-pointer place-items-end border-0 bg-transparent p-0 drop-shadow-md focus:outline-none focus-visible:ring-4 focus-visible:ring-red-200';
                marker.dataset.locationId = location.id;
                marker.style.left = location.coordinate_x + '%';
                marker.style.top = location.coordinate_y + '%';
                marker.style.color = location.category_color;
                marker.innerHTML =
                    '<svg class="h-10 w-8 overflow-visible" viewBox="0 0 36 46" aria-hidden="true">' +
                    '<path d="M18 1.5C9.2 1.5 2 8.4 2 16.9C2 29.1 18 44.5 18 44.5S34 29.1 34 16.9C34 8.4 26.8 1.5 18 1.5Z" fill="currentColor" stroke="white" stroke-width="2.5"/>' +
                    '<circle cx="18" cy="17" r="8" fill="white"/>' +
                    '<text class="marker-number" x="18" y="20.5" text-anchor="middle" font-size="10" font-weight="700" fill="currentColor">' +
                    categoryNumber + '</text>' +
                    '</svg>';
                marker.title = location.location_name;
                marker.setAttribute('aria-label', 'Lihat detail ' + location.location_name);
                marker.addEventListener('click', () => showDetail(location.id));
                mapFrame.appendChild(marker);
            });
        }

        function renderList() {
            categoryGrid.innerHTML = Object.entries(groupedLocations).map(([category, group]) =>
                '<section class="rounded-lg border border-gray-200 p-2.5">' +
                '<h3 class="mb-2 flex items-center gap-2 text-md font-bold">' +
                '<span class="grid size-8 shrink-0 place-items-center rounded-lg bg-gray-50 text-gray-500 dark:bg-gray-800 dark:text-gray-300" style="color: ' +
                escapeHtml(group.color) + '">' + group.icon + '</span>' +
                '<span style="color: ' + escapeHtml(group.color) + '">' + escapeHtml(category) + '</span>' +
                '</h3>' +
                group.locations.map((location, index) =>
                    '<button class="flex w-full cursor-pointer items-center gap-2 border-0 border-t border-solid border-gray-100 bg-transparent py-2 text-left text-sm text-gray-800" type="button" data-location-id="' +
                    location.id + '">' +
                    '<span class="grid size-[22px] shrink-0 place-items-center rounded-full text-[10px] font-bold text-white" style="background-color: ' +
                    escapeHtml(location.category_color) + '">' +
                    (index + 1) +
                    '</span>' +
                    '<span>' + escapeHtml(location.location_name) + '</span>' +
                    '</button>'
                ).join('') +
                '</section>'
            ).join('');

            categoryGrid.querySelectorAll('[data-location-id]').forEach((button) => {
                button.addEventListener('click', () => showDetail(Number(button.dataset.locationId)));
            });
        }

        function showDetail(locationId) {
            const location = tourismLocations.find((item) => item.id === locationId);
            if (!location) return;

            const detailCategory = document.getElementById('detailCategory');
            detailCategory.innerHTML = location.category_icon_svg + '<span>' + escapeHtml(location.category) + '</span>';
            detailCategory.style.color = location.category_color;

            const detailNumber = document.getElementById('detailNumber');
            detailNumber.textContent = getCategoryNumber(location);
            detailNumber.style.backgroundColor = location.category_color;

            document.getElementById('detailName').textContent = location.location_name;
            document.getElementById('detailAddress').textContent = location.location_address;
            document.getElementById('detailDescription').innerHTML = safeDescription(location.location_description);

            const detailMedia = document.getElementById('detailMedia');
            const mediaUrl = location.location_media_url;
            const isVideo = mediaUrl?.toLowerCase().endsWith('.mp4');

            if (!mediaUrl) {
                detailMedia.innerHTML = '<span>Media belum tersedia</span>';
            } else if (isVideo) {
                detailMedia.innerHTML =
                    '<video class="absolute inset-0 block bg-black" style="width:100%;height:100%;object-fit:contain" autoplay controls playsinline preload="metadata" aria-label="Video ' +
                    escapeHtml(location.location_name) + '">' +
                    '<source src="' + escapeHtml(mediaUrl) + '">' +
                    'Browser tidak mendukung pemutaran video.' +
                    '</video>';
                detailMedia.querySelector('video').play().catch(() => {});
            } else {
                detailMedia.innerHTML =
                    '<img class="absolute inset-0 block" style="width:100%;height:100%;object-fit:contain" src="' + escapeHtml(mediaUrl) + '" alt="Media ' +
                    escapeHtml(
                        location.location_name) + '">';
            }

            const source = document.getElementById('detailSource');
            const isUrl = /^https?:\/\//i.test(location.location_source_media ?? '');
            source.innerHTML = isUrl ?
                '<a class="text-[#da251d] underline" href="' + escapeHtml(location.location_source_media) +
                '" target="_blank" rel="noopener noreferrer">' + escapeHtml(location.location_source_media) + '</a>' :
                escapeHtml(location.location_source_media || 'Sumber belum tersedia');

            document.querySelectorAll('[data-location-id]').forEach((element) => {
                if (!element.closest('#mapFrame')) return;
                const markerLocation = tourismLocations.find((item) => item.id === Number(element.dataset
                    .locationId));
                if (!markerLocation) return;
                const active = markerLocation.id === locationId;
                element.style.color = active ? '#da251d' : markerLocation.category_color;
                element.querySelector('.marker-number')?.classList.toggle('hidden', active);
                element.classList.toggle('scale-y-110', active);
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
                const location = tourismLocations.find((item) => item.id === Number(marker.dataset
                    .locationId));
                if (location) marker.style.color = location.category_color;
                marker.querySelector('.marker-number')?.classList.remove('hidden');
                marker.classList.remove('z-20', 'scale-y-110');
            });
        });

        renderMarkers();
        renderList();
        updateClock();
        setInterval(updateClock, 30000);
    </script>
</body>

</html>
