<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Peta Wisata Kalimantan Selatan</title>
    @vite('resources/css/app.css')
</head>

<body class="m-0 h-screen overflow-hidden bg-gray-100 font-sans text-gray-800 max-[850px]:h-auto max-[850px]:min-h-screen max-[850px]:overflow-auto">
    <header class="flex h-16 items-center justify-between border-b border-red-800 bg-[#da251d] px-4 py-1 text-[26px] font-bold text-white uppercase max-[520px]:px-2 max-[520px]:text-sm">
        <div class="min-w-0 flex-1 truncate">Kalimantan Selatan</div>
        <div class="shrink-0 whitespace-nowrap text-center tracking-wide">Interactive Map Guidance</div>
        <time class="min-w-0 flex-1 text-right tabular-nums" id="clock" aria-label="Waktu sekarang">--:--</time>
    </header>

    <main class="h-[calc(100vh-4rem)] w-full px-2 py-1 max-[850px]:h-auto max-[520px]:px-1">
        <section class="grid h-full grid-cols-2 overflow-hidden rounded-lg bg-white max-[850px]:h-auto max-[850px]:grid-cols-1">
            <div class="flex min-w-0 flex-col bg-gray-50 max-[850px]:min-h-[600px]">
                <div class="min-h-0 flex-1 overflow-hidden">
                    <div class="relative size-full" id="mapFrame">
                        <img
                            class="block size-full rounded object-fill"
                            src="{{ asset('images/maps/peta_provinsi_kalsel.png') }}"
                            alt="Peta Kalimantan Selatan"
                        >
                    </div>
                </div>
            </div>

            <aside class="flex min-h-0 min-w-0 flex-col overflow-hidden px-3 py-2 max-[850px]:min-h-[500px] max-[850px]:overflow-visible">
                <div class="min-h-0 flex-1 overflow-hidden max-[850px]:overflow-visible" id="listView">
                    <div class="grid h-full min-h-0 grid-rows-[auto_minmax(0,1fr)] max-[850px]:h-auto">
                        <header class="border-b border-gray-200 pb-2">
                            <div class="flex items-center gap-2.5">
                                <img
                                    class="h-12 w-10 object-contain"
                                    src="{{ asset('images/logo/logo_kalsel.svg') }}"
                                    alt="Logo Kalimantan Selatan"
                                >
                                <h2 class="m-0 text-lg font-bold text-[#da251d]">Wisata Kalimantan Selatan</h2>
                            </div>
                        </header>

                        <div class="min-h-0 overflow-y-auto pt-2.5 pr-1 max-[850px]:overflow-visible">
                            <div class="grid grid-cols-2 gap-2.5 max-[520px]:grid-cols-1" id="categoryGrid"></div>
                        </div>
                    </div>
                </div>

                <div class="min-h-0 flex-1 overflow-hidden max-[850px]:overflow-visible" id="detailView" hidden>
                    <div class="flex h-full min-h-0 flex-col max-[850px]:h-auto">
                        <button class="mb-2 inline-flex min-h-11 w-fit shrink-0 cursor-pointer items-center rounded-lg border-0 bg-[#da251d] px-4 py-2 text-sm font-bold text-white shadow-sm focus:outline-none focus-visible:ring-4 focus-visible:ring-red-200" id="backButton" type="button">
                            ← Kembali ke daftar
                        </button>

                        <article class="min-h-0 flex-1 overflow-y-auto rounded-lg border border-gray-200 p-4 max-[850px]:overflow-visible">
                            <div class="mb-2 flex items-center gap-2.5">
                                <span class="grid size-8 shrink-0 place-items-center rounded-full text-sm font-bold text-white" id="detailNumber"></span>
                                <h2 class="m-0 text-2xl font-bold" id="detailName"></h2>
                            </div>
                            <div class="mb-4 inline-block rounded-full px-2.5 py-1 text-xs text-white" id="detailCategory"></div>
                            <div class="mb-4 grid h-48 place-items-center overflow-hidden rounded-lg bg-[#da251d] text-white" id="detailMedia"></div>

                            <p class="mb-4 text-sm leading-6 text-gray-600">
                                <strong class="text-gray-800">Sumber:</strong>
                                <span id="detailSource"></span>
                            </p>

                            <div class="mb-4 flex items-start gap-2">
                                <svg class="mt-0.5 size-[18px] shrink-0 fill-[#da251d]" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 2a8 8 0 0 0-8 8c0 5.5 8 12 8 12s8-6.5 8-12a8 8 0 0 0-8-8Zm0 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/>
                                </svg>
                                <p class="m-0 text-sm leading-6 font-bold" id="detailAddress"></p>
                            </div>

                            <p class="m-0 text-sm leading-6 text-gray-600" id="detailDescription"></p>
                        </article>
                    </div>
                </div>
            </aside>
        </section>
    </main>

    <script>
        const tourismLocations = [
            {
                id: 1,
                category: 'Wisata Alam',
                category_color: '#2f7d57',
                location_name: 'Pegunungan Meratus Loksado',
                location_address: 'Kecamatan Loksado, Kabupaten Hulu Sungai Selatan',
                location_description: 'Kawasan pegunungan dengan lanskap hijau, aliran sungai, dan kehidupan masyarakat Dayak Meratus.',
                location_media_url: null,
                location_source_media: 'Data contoh untuk rancangan antarmuka',
                coordinate_x: 48,
                coordinate_y: 47
            },
            {
                id: 2,
                category: 'Wisata Alam',
                category_color: '#2f7d57',
                location_name: 'Bukit Matang Kaladan',
                location_address: 'Desa Tiwingan Lama, Kecamatan Aranio, Kabupaten Banjar',
                location_description: 'Bukit dengan panorama gugusan pulau kecil di kawasan Waduk Riam Kanan.',
                location_media_url: null,
                location_source_media: 'Data contoh untuk rancangan antarmuka',
                coordinate_x: 40,
                coordinate_y: 31
            },
            {
                id: 3,
                category: 'Budaya & Sejarah',
                category_color: '#c67a38',
                location_name: 'Pasar Terapung Lok Baintan',
                location_address: 'Desa Sungai Pinang, Kecamatan Sungai Tabuk, Kabupaten Banjar',
                location_description: 'Pasar tradisional di atas Sungai Martapura dengan pedagang yang menggunakan perahu jukung.',
                location_media_url: '{{ asset('images/maps/kalsel.webp') }}',
                location_source_media: 'Aset prototipe lokal',
                coordinate_x: 25,
                coordinate_y: 23
            },
            {
                id: 4,
                category: 'Budaya & Sejarah',
                category_color: '#c67a38',
                location_name: 'Museum Lambung Mangkurat',
                location_address: 'Jalan Ahmad Yani Km 36, Kota Banjarbaru',
                location_description: 'Museum yang menyimpan koleksi sejarah dan budaya masyarakat Kalimantan Selatan.',
                location_media_url: null,
                location_source_media: 'Data contoh untuk rancangan antarmuka',
                coordinate_x: 30,
                coordinate_y: 28
            },
            {
                id: 5,
                category: 'Wisata Religi',
                category_color: '#7963a9',
                location_name: 'Masjid Sultan Suriansyah',
                location_address: 'Kuin Utara, Kecamatan Banjarmasin Utara, Kota Banjarmasin',
                location_description: 'Salah satu masjid tertua di Kalimantan Selatan dengan arsitektur tradisional Banjar.',
                location_media_url: null,
                location_source_media: 'Data contoh untuk rancangan antarmuka',
                coordinate_x: 18,
                coordinate_y: 28
            },
            {
                id: 6,
                category: 'Wisata Religi',
                category_color: '#7963a9',
                location_name: 'Makam Datu Kalampayan',
                location_address: 'Desa Kalampayan Tengah, Kecamatan Astambul, Kabupaten Banjar',
                location_description: 'Kompleks makam Syekh Muhammad Arsyad al-Banjari yang menjadi tujuan wisata religi.',
                location_media_url: null,
                location_source_media: 'Data contoh untuk rancangan antarmuka',
                coordinate_x: 34,
                coordinate_y: 24
            },
            {
                id: 7,
                category: 'Wisata Bahari',
                category_color: '#287f9c',
                location_name: 'Pantai Angsana',
                location_address: 'Kecamatan Angsana, Kabupaten Tanah Bumbu',
                location_description: 'Pantai dengan wisata bawah laut dan kawasan terumbu karang.',
                location_media_url: null,
                location_source_media: 'Data contoh untuk rancangan antarmuka',
                coordinate_x: 64,
                coordinate_y: 75
            },
            {
                id: 8,
                category: 'Wisata Bahari',
                category_color: '#287f9c',
                location_name: 'Pulau Samber Gelap',
                location_address: 'Kecamatan Pulau Sebuku, Kabupaten Kotabaru',
                location_description: 'Pulau kecil dengan pasir putih, air jernih, dan kawasan konservasi penyu.',
                location_media_url: null,
                location_source_media: 'Data contoh untuk rancangan antarmuka',
                coordinate_x: 79,
                coordinate_y: 82
            }
        ];

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

        const groupedLocations = tourismLocations.reduce((groups, location) => {
            if (!groups[location.category]) {
                groups[location.category] = {
                    color: location.category_color,
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
                marker.className = 'absolute z-10 grid size-12 -translate-x-1/2 -translate-y-full cursor-pointer place-items-end border-0 bg-transparent p-0 drop-shadow-md transition-transform duration-300 focus:outline-none focus-visible:ring-4 focus-visible:ring-red-200';
                marker.dataset.locationId = location.id;
                marker.style.left = location.coordinate_x + '%';
                marker.style.top = location.coordinate_y + '%';
                marker.style.color = location.category_color;
                marker.innerHTML =
                    '<svg class="h-12 w-10 overflow-visible" viewBox="0 0 36 46" aria-hidden="true">' +
                        '<path d="M18 1.5C9.2 1.5 2 8.4 2 16.9C2 29.1 18 44.5 18 44.5S34 29.1 34 16.9C34 8.4 26.8 1.5 18 1.5Z" fill="currentColor" stroke="white" stroke-width="2.5"/>' +
                        '<circle cx="18" cy="17" r="8" fill="white"/>' +
                        '<text x="18" y="20.5" text-anchor="middle" font-size="10" font-weight="700" fill="currentColor">' + categoryNumber + '</text>' +
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
                    '<h3 class="mb-1.5 flex items-center gap-2 text-sm font-bold">' +
                        '<span class="size-2.5 rounded-full" style="background-color: ' + escapeHtml(group.color) + '"></span>' +
                        escapeHtml(category) +
                    '</h3>' +
                    group.locations.map((location, index) =>
                        '<button class="flex w-full cursor-pointer items-center gap-2 border-0 border-t border-solid border-gray-100 bg-transparent py-2 text-left text-sm text-gray-800" type="button" data-location-id="' + location.id + '">' +
                            '<span class="grid size-[22px] shrink-0 place-items-center rounded-full text-[10px] font-bold text-white" style="background-color: ' + escapeHtml(location.category_color) + '">' +
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
            detailCategory.textContent = location.category;
            detailCategory.style.backgroundColor = location.category_color;

            const detailNumber = document.getElementById('detailNumber');
            detailNumber.textContent = getCategoryNumber(location);
            detailNumber.style.backgroundColor = location.category_color;

            document.getElementById('detailName').textContent = location.location_name;
            document.getElementById('detailAddress').textContent = location.location_address;
            document.getElementById('detailDescription').textContent = location.location_description;

            const detailMedia = document.getElementById('detailMedia');
            const mediaUrl = location.location_media_url;
            const isVideo = mediaUrl?.toLowerCase().endsWith('.mp4');

            if (!mediaUrl) {
                detailMedia.innerHTML = '<span>Media belum tersedia</span>';
            } else if (isVideo) {
                detailMedia.innerHTML =
                    '<video class="size-full bg-black object-contain" autoplay controls playsinline preload="metadata" aria-label="Video ' + escapeHtml(location.location_name) + '">' +
                        '<source src="' + escapeHtml(mediaUrl) + '">' +
                        'Browser tidak mendukung pemutaran video.' +
                    '</video>';
                detailMedia.querySelector('video').play().catch(() => {});
            } else {
                detailMedia.innerHTML =
                    '<img class="size-full object-cover" src="' + escapeHtml(mediaUrl) + '" alt="Media ' + escapeHtml(location.location_name) + '">';
            }

            const source = document.getElementById('detailSource');
            const isUrl = /^https?:\/\//i.test(location.location_source_media ?? '');
            source.innerHTML = isUrl
                ? '<a class="text-[#da251d] underline" href="' + escapeHtml(location.location_source_media) + '" target="_blank" rel="noopener noreferrer">' + escapeHtml(location.location_source_media) + '</a>'
                : escapeHtml(location.location_source_media || 'Sumber belum tersedia');

            document.querySelectorAll('[data-location-id]').forEach((element) => {
                if (!element.closest('#mapFrame')) return;
                const active = Number(element.dataset.locationId) === locationId;
                element.classList.toggle('animate-pulse', active);
                element.classList.toggle('scale-110', active);
                element.classList.toggle('z-20', active);
            });

            listView.hidden = true;
            detailView.hidden = false;
        }

        document.getElementById('backButton').addEventListener('click', () => {
            detailView.querySelector('video')?.pause();
            detailView.hidden = true;
            listView.hidden = false;

            document.querySelectorAll('#mapFrame [data-location-id]').forEach((marker) => {
                marker.classList.remove('animate-pulse', 'scale-110', 'z-20');
            });
        });

        renderMarkers();
        renderList();
        updateClock();
        setInterval(updateClock, 30000);
    </script>
</body>
</html>
