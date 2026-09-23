<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Peta Wisata Kalimantan Selatan</title>
    @vite('resources/css/app.css')
</head>

<body class="m-0 min-h-screen bg-gray-100 font-sans text-gray-800">
    <header class="flex min-h-16 items-center justify-between border-b border-red-800 bg-[#da251d] px-4 py-2 text-[26px] font-bold text-white uppercase max-[520px]:px-2 max-[520px]:text-sm">
        <div>Kalimantan Selatan</div>
        <div class="tracking-wide">Interactive Map Guidance</div>
        <time class="min-w-20 text-right tabular-nums max-[520px]:min-w-14" id="clock" aria-label="Waktu sekarang">--:--</time>
    </header>

    <main class="w-full p-3 pt-2 max-[520px]:p-2">
        <section class="grid min-h-[calc(100vh-74px)] grid-cols-2 overflow-hidden rounded-lg border border-gray-200 bg-white max-[850px]:grid-cols-1">
            <div class="flex min-w-0 flex-col border-r border-gray-200 bg-gray-50 p-3 max-[850px]:min-h-[600px] max-[850px]:border-r-0 max-[850px]:border-b">
                <div class="grid flex-1 place-items-center">
                    <div class="relative w-full max-w-[540px]" id="mapFrame">
                        <img
                            class="block h-auto w-full rounded border-[7px] border-[#da251d]"
                            src="{{ asset('images/maps/kalsel.webp') }}"
                            alt="Peta Kalimantan Selatan"
                        >
                    </div>
                </div>
            </div>

            <aside class="flex min-w-0 flex-col p-3 max-[850px]:min-h-[500px]">
                <div id="listView">
                    <header class="border-b border-gray-200 pb-2.5">
                        <div class="flex items-center gap-2.5">
                            <img
                                class="h-12 w-10 object-contain"
                                src="{{ asset('images/logo/logo_kalsel.svg') }}"
                                alt="Logo Kalimantan Selatan"
                            >
                            <h2 class="m-0 text-lg font-bold text-[#da251d]">Wisata Kalimantan Selatan</h2>
                        </div>
                    </header>

                    <div class="max-h-[calc(100vh-150px)] overflow-y-auto pt-2.5 max-[850px]:max-h-none">
                        <div class="grid grid-cols-2 gap-2.5 max-[520px]:grid-cols-1" id="categoryGrid"></div>
                    </div>
                </div>

                <div id="detailView" hidden>
                    <button class="mb-4 cursor-pointer border-0 bg-transparent p-0 font-bold text-[#da251d] hover:underline" id="backButton" type="button">
                        ← Kembali ke daftar
                    </button>

                    <div class="mb-2 inline-block rounded-full px-2.5 py-1 text-xs text-white" id="detailCategory"></div>
                    <h2 class="mb-4 text-2xl font-bold" id="detailName"></h2>
                    <div class="mb-4 grid h-48 place-items-center overflow-hidden rounded-lg bg-[#da251d] text-white" id="detailMedia"></div>

                    <p class="mb-4 text-sm leading-6 text-gray-600">
                        <strong class="text-gray-800">Sumber:</strong>
                        <span id="detailSource"></span>
                    </p>

                    <div class="mb-4 flex items-start gap-2">
                        <svg class="mt-0.5 size-[18px] shrink-0 fill-[#da251d]" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 2a8 8 0 0 0-8 8c0 5.5 8 12 8 12s8-6.5 8-12a8 8 0 0 0-8-8Zm0 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/>
                        </svg>
                        <p class="m-0 text-sm leading-6 text-gray-600" id="detailAddress"></p>
                    </div>

                    <p class="m-0 text-sm leading-6 text-gray-600" id="detailDescription"></p>
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

        function renderMarkers() {
            tourismLocations.forEach((location) => {
                const categoryNumber = tourismLocations
                    .filter((item) => item.category === location.category)
                    .findIndex((item) => item.id === location.id) + 1;
                const marker = document.createElement('button');
                marker.type = 'button';
                marker.className = 'absolute grid size-7 -translate-x-1/2 -translate-y-1/2 cursor-pointer place-items-center rounded-full border-[3px] border-white text-[11px] font-bold text-white shadow-md transition hover:scale-110 focus:outline-none focus:ring-4 focus:ring-red-200';
                marker.dataset.locationId = location.id;
                marker.style.left = location.coordinate_x + '%';
                marker.style.top = location.coordinate_y + '%';
                marker.style.backgroundColor = location.category_color;
                marker.textContent = categoryNumber;
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
                        '<button class="flex w-full cursor-pointer items-center gap-2 border-0 border-t border-solid border-gray-100 bg-transparent py-2 text-left text-sm text-gray-800 hover:font-bold hover:text-[#da251d]" type="button" data-location-id="' + location.id + '">' +
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

            document.getElementById('detailName').textContent = location.location_name;
            document.getElementById('detailAddress').textContent = location.location_address;
            document.getElementById('detailDescription').textContent = location.location_description;

            const detailMedia = document.getElementById('detailMedia');
            detailMedia.innerHTML = location.location_media_url
                ? '<img class="size-full object-cover" src="' + escapeHtml(location.location_media_url) + '" alt="Media ' + escapeHtml(location.location_name) + '">'
                : '<span>Media belum tersedia</span>';

            const source = document.getElementById('detailSource');
            const isUrl = /^https?:\/\//i.test(location.location_source_media ?? '');
            source.innerHTML = isUrl
                ? '<a class="text-[#da251d] underline" href="' + escapeHtml(location.location_source_media) + '" target="_blank" rel="noopener noreferrer">' + escapeHtml(location.location_source_media) + '</a>'
                : escapeHtml(location.location_source_media || 'Sumber belum tersedia');

            document.querySelectorAll('[data-location-id]').forEach((element) => {
                if (!element.closest('#mapFrame')) return;
                const active = Number(element.dataset.locationId) === locationId;
                element.classList.toggle('ring-4', active);
                element.classList.toggle('ring-red-200', active);
                element.classList.toggle('scale-110', active);
            });

            listView.hidden = true;
            detailView.hidden = false;
        }

        document.getElementById('backButton').addEventListener('click', () => {
            detailView.hidden = true;
            listView.hidden = false;

            document.querySelectorAll('#mapFrame [data-location-id]').forEach((marker) => {
                marker.classList.remove('ring-4', 'ring-red-200', 'scale-110');
            });
        });

        renderMarkers();
        renderList();
        updateClock();
        setInterval(updateClock, 30000);
    </script>
</body>
</html>
