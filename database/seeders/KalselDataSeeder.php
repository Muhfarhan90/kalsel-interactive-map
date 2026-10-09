<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

/**
 * Data asli dari dev_kalsel_interactive_map.sql (MySQL 8.0.30).
 * Tujuan: dev_kalsel_map, skema branch dynamic-menu.
 *
 * Isi: 3 menu, 12 kategori, 43 lokasi, 2 peta, dan header homepage.
 * ID lama hanya menjadi penanda di payload; foreign key memakai ID database baru.
 * Ikon Heroicons dipetakan ke nama Font Awesome. HTML, koordinat, status aktif,
 * timestamp, path media, sumber media, dan background berasal dari SQL.
 *
 * Jalankan: php artisan db:seed --class=KalselDataSeeder
 * Tidak memerlukan file SQL atau koneksi database lama saat dijalankan.
 * Data dengan slug/menu, nama kategori, dan nama lokasi yang sama diperbarui.
 * Data lain tetap ada; tidak ada truncate/delete dan tidak ada akun/session.
 * Menjalankan ulang seeder dengan nama yang sama tidak menambah duplikat.
 * File fisik storage/public perlu disalin terpisah jika pindah project.
 *
 * map_background_image -> menus.banner
 * map_background_text  -> menus.description
 * Header "home"        -> homepages.header_title/header_logo/header_text/color
 */
class KalselDataSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::connection()->getDatabaseName() !== 'dev_kalsel_map') {
            throw new RuntimeException(
                'Seeder ini hanya untuk dev_kalsel_map. Periksa DB_DATABASE di .env, '
                .'lalu jalankan php artisan config:clear.'
            );
        }

        $required = [
            'menus' => ['id', 'name', 'slug', 'icon', 'description', 'title', 'sub_title',
                'logo', 'banner', 'color', 'sort_order', 'is_active', 'created_at', 'updated_at'],
            'categories' => ['id', 'menu_id', 'name', 'icon', 'color', 'description',
                'background', 'sort_order', 'created_at', 'updated_at'],
            'locations' => ['id', 'category_id', 'name', 'address', 'description',
                'x_location', 'y_location', 'media', 'source_media', 'is_active',
                'created_at', 'updated_at'],
            'maps' => ['id', 'map_image', 'map_description', 'created_at', 'updated_at'],
            'homepages' => ['id', 'title', 'label', 'description', 'image',
                'header_title', 'header_logo', 'header_text', 'color', 'created_at', 'updated_at'],
        ];

        foreach ($required as $table => $columns) {
            if (! Schema::hasColumns($table, $columns)) {
                throw new RuntimeException(
                    "Kolom tabel {$table} belum lengkap. Jalankan php artisan migrate terlebih dahulu."
                );
            }
        }

        $data = json_decode(self::DATA, true, 512, JSON_THROW_ON_ERROR);

        DB::transaction(function () use ($data): void {
            foreach ($data['maps'] as $map) {
                $this->save('maps', [
                    'map_image' => $map['map_image'],
                    'map_description' => $map['map_description'],
                ], $map);
            }

            // HomepageController memakai Map::latest('id')->first().
            // Peta aktif lama (dipakai ketiga modul) harus menjadi peta terakhir.
            $activeMap = $data['active_map'];
            $latestMap = DB::table('maps')->orderByDesc('id')->first();

            if ($latestMap
                && $latestMap->map_image === $activeMap['map_image']
                && $latestMap->map_description === $activeMap['map_description']) {
                DB::table('maps')->where('id', $latestMap->id)->update($activeMap);
            } else {
                DB::table('maps')->insert($activeMap);
            }

            $homepageId = DB::table('homepages')->orderBy('id')->value('id');

            if ($homepageId !== null) {
                // Hanya header yang berasal dari tabel lama; isi homepage tetap.
                DB::table('homepages')->where('id', $homepageId)
                    ->update($data['homepage_header']);
            } else {
                // Teks bawaan project digunakan jika homepage belum pernah dibuat.
                DB::table('homepages')->insert(array_merge([
                    'title' => 'Satu tempat untuk mengenal Kalimantan Selatan',
                    'label' => 'Portal Banua',
                    'description' => 'Temukan wisata, cita rasa, dan akses perjalanan di Banua. Pilih cara menjelajah yang paling menarik untukmu.',
                    'image' => 'images/home/menara-pandang.jpeg',
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $data['homepage_header']));
            }

            foreach ($data['menus'] as $menu) {
                $menuId = $this->save('menus', ['slug' => $menu['slug']], $menu['attributes']);

                foreach ($menu['categories'] as $category) {
                    $categoryId = $this->save('categories', [
                        'menu_id' => $menuId,
                        'name' => $category['attributes']['name'],
                    ], $category['attributes']);

                    foreach ($category['locations'] as $location) {
                        $this->save('locations', [
                            'category_id' => $categoryId,
                            'name' => $location['attributes']['name'],
                        ], $location['attributes']);
                    }
                }
            }
        });

        $this->command?->info('Import selesai: 3 menu, 12 kategori, dan 43 lokasi dari SQL.');
        $this->command?->table(['Menu', 'Kategori', 'Lokasi'], [
            ['Wisata', 5, 15],
            ['Kuliner', 4, 15],
            ['Transportasi', 3, 13],
        ]);
    }

    private function save(string $table, array $key, array $attributes): int
    {
        $ids = DB::table($table)->where($key)->limit(2)->pluck('id');

        if ($ids->count() > 1) {
            throw new RuntimeException(
                "Ada data ganda di tabel {$table} untuk ".json_encode($key, JSON_UNESCAPED_UNICODE)
                .'. Seluruh import dibatalkan agar relasi tidak salah.'
            );
        }

        if ($ids->isNotEmpty()) {
            $id = (int) $ids->first();
            DB::table($table)->where('id', $id)->update($attributes);

            return $id;
        }

        return (int) DB::table($table)->insertGetId(array_merge($key, $attributes));
    }

    private const DATA = <<<'KALSEL_DATA_JSON'
{
    "maps": [
        {
            "map_image": "images/maps/peta_provinsi_kalsel.png",
            "map_description": "Peta lokasi kuliner Kalimantan Selatan.",
            "created_at": "2026-10-01 21:11:07",
            "updated_at": "2026-10-01 21:11:07"
        }
    ],
    "active_map": {
        "map_image": "storage/maps/b39b06fc-c4ad-4401-abb2-e25c269b7c52.png",
        "map_description": "Peta utama lokasi wisata Kalimantan Selatan.",
        "created_at": "2026-10-01 21:02:10",
        "updated_at": "2026-10-05 00:16:31"
    },
    "homepage_header": {
        "header_title": "Interactive Map Guidance",
        "header_logo": "storage/headers/header-global.svg",
        "header_text": "Provinsi Kalimantan Selatan",
        "color": "#da251d"
    },
    "menus": [
        {
            "slug": "wisata",
            "attributes": {
                "name": "Wisata",
                "icon": "map-location-dot",
                "description": null,
                "title": "Peta Wisata",
                "sub_title": "Provinsi Kalimantan Selatan",
                "logo": "storage/maps/logo.png",
                "banner": "storage/maps/44df13c0-29df-4f76-b7d9-105990bd7db8.jpg",
                "color": "#da251d",
                "sort_order": 0,
                "is_active": true,
                "created_at": "2026-09-25 20:06:29",
                "updated_at": "2026-10-05 02:27:24"
            },
            "categories": [
                {
                    "legacy_id": 2,
                    "attributes": {
                        "name": "Budaya & Sejarah",
                        "color": "#c67a38",
                        "description": "Warisan budaya, sejarah, dan tradisi Banjar.",
                        "background": "storage/tourism/categories/wVWDx1xFnH3mscoaHNMxb76p5kwgnl39OSLSSz1u.jpg",
                        "sort_order": 1,
                        "created_at": "2026-09-25 19:06:15",
                        "updated_at": "2026-10-04 23:22:32",
                        "icon": "landmark"
                    },
                    "locations": [
                        {
                            "legacy_id": 3,
                            "attributes": {
                                "name": "Pasar Terapung Lok Baintan",
                                "address": "Desa Lok Baintan, Kecamatan Sungai Tabuk, Kabupaten Banjar",
                                "description": "<h1>Pasar Terapung Lok Baintan</h1><p>Pasar Terapung Lok Baintan adalah pasar tradisional di Sungai Martapura. Pedagang datang menggunakan <em>jukung</em> dan menawarkan hasil kebun, makanan, serta kebutuhan rumah tangga langsung dari atas perahu.</p><h2>Nilai budaya</h2><ul><li>Memperlihatkan hubungan masyarakat Banjar dengan sungai sebagai ruang hidup.</li><li>Mempertahankan transaksi tradisional antarpedagang dan pembeli di atas perahu.</li><li>Menyajikan suasana permukiman tepian sungai pada pagi hari.</li></ul><h3>Rencana kunjungan</h3><ol><li>Datang pada pagi hari ketika aktivitas pasar masih ramai.</li><li>Gunakan jasa perahu setempat dan kenakan pelampung.</li><li>Siapkan uang tunai pecahan kecil untuk berbelanja.</li></ol><p><u>Jaga keseimbangan saat berada di jukung</u> dan hindari menghalangi jalur perahu pedagang.</p>",
                                "x_location": 24.33,
                                "y_location": 70.05,
                                "media": "storage/tourism/pasar-terapung-lok-baintan.mp4",
                                "source_media": "YT Wisata Kalimantan",
                                "is_active": true,
                                "created_at": "2026-09-25 19:06:16",
                                "updated_at": "2026-10-04 21:02:26"
                            }
                        },
                        {
                            "legacy_id": 4,
                            "attributes": {
                                "name": "Situs Candi Agung",
                                "address": "Desa Sungai Malang, Kecamatan Amuntai Tengah, Kabupaten Hulu Sungai Utara",
                                "description": "<h1>Situs Candi Agung</h1><p>Situs Candi Agung di Amuntai berkaitan dengan sejarah <strong>Kerajaan Negara Dipa</strong> dan perjalanan awal terbentuknya Kerajaan Banjar. Kawasan ini menjadi ruang pembelajaran mengenai arkeologi, sejarah lokal, dan perkembangan masyarakat di wilayah Hulu Sungai.</p><h2>Yang dapat dipelajari</h2><ul><li>Sisa struktur candi dan material bangunan yang ditemukan di kawasan situs.</li><li>Kisah Negara Dipa dalam sejarah dan tradisi tutur masyarakat Banjar.</li><li>Koleksi pendukung yang menjelaskan temuan arkeologi setempat.</li></ul><h3>Etika mengunjungi situs</h3><ol><li>Ikuti jalur kunjungan dan petunjuk pengelola.</li><li>Jangan menyentuh, memindahkan, atau menaiki bagian situs.</li><li>Jaga ketenangan dan kebersihan kawasan bersejarah.</li></ol><p><em>Kunjungan yang bertanggung jawab membantu menjaga peninggalan sejarah untuk generasi berikutnya.</em></p>",
                                "x_location": 38.34,
                                "y_location": 38.75,
                                "media": "storage/tourism/situs-candi-agung.mp4",
                                "source_media": "YT AI Story Haven",
                                "is_active": true,
                                "created_at": "2026-09-25 19:06:16",
                                "updated_at": "2026-10-04 21:06:22"
                            }
                        },
                        {
                            "legacy_id": 11,
                            "attributes": {
                                "name": "Museum Lambung Mangkurat",
                                "address": "Jl. A. Yani No.KM.36, Loktabat Utara, Kec. Banjarbaru Utara, Kota Banjar Baru, Kalimantan Selatan",
                                "description": "<p>Museum Lambung Mangkurat adalah museum provinsi yang terletak di Banjarbaru, Kalimantan Selatan, dan menjadi pusat pelestarian sejarah serta budaya suku Banjar dan Dayak. Bangunan museum ini sangat ikonik karena mengadopsi arsitektur rumah adat Bubungan Tinggi yang dahulu digunakan sebagai kediaman bangsawan Kerajaan Banjar. Di dalamnya, Anda bisa mengeksplorasi lebih dari 12.000 koleksi benda bersejarah, mulai dari replika singgasana emas, artefak Hindu-Buddha kuno, pakaian adat, hingga kerangka paus sepanjang 4 meter yang menyambut pengunjung di lantai dasar.</p>",
                                "x_location": 27.63,
                                "y_location": 72,
                                "media": "storage/tourism/museum-lambung-mangkurat.mp4",
                                "source_media": "Kanal Youtube Museum Lambung Mangkurat",
                                "is_active": true,
                                "created_at": "2026-10-01 23:53:14",
                                "updated_at": "2026-10-04 21:03:31"
                            }
                        },
                        {
                            "legacy_id": 12,
                            "attributes": {
                                "name": "Museum Rakyat Hulu Sungai Selatan",
                                "address": "Hamalau, Kec. Sungai Raya, Kabupaten Hulu Sungai Selatan, Kalimantan Selatan",
                                "description": "<h3></h3>",
                                "x_location": 39.36,
                                "y_location": 48.04,
                                "media": "storage/tourism/museum-rakyat-hulu-sungai-selatan.mp4",
                                "source_media": "Kanal Youtube HSS TVnet",
                                "is_active": true,
                                "created_at": "2026-10-02 00:14:35",
                                "updated_at": "2026-10-04 21:08:17"
                            }
                        }
                    ]
                },
                {
                    "legacy_id": 1,
                    "attributes": {
                        "name": "Wisata Alam",
                        "color": "#2f7d57",
                        "description": "Destinasi alam, pegunungan, dan bentang hijau.",
                        "background": "storage/tourism/categories/NmIzLWB67ja4T01Ri1BdIF4exo1xAaiC5vHZmBlp.jpg",
                        "sort_order": 2,
                        "created_at": "2026-09-25 19:06:15",
                        "updated_at": "2026-10-05 00:22:27",
                        "icon": "earth-asia"
                    },
                    "locations": [
                        {
                            "legacy_id": 1,
                            "attributes": {
                                "name": "Pegunungan Meratus Loksado",
                                "address": "Kecamatan Loksado, Kabupaten Hulu Sungai Selatan",
                                "description": "<h1>Pegunungan Meratus Loksado</h1><p><strong>Loksado</strong> merupakan kawasan pegunungan di Hulu Sungai Selatan yang memadukan hutan tropis, aliran Sungai Amandit, dan kehidupan masyarakat Dayak Meratus. Suasananya sejuk dan cocok untuk pengunjung yang ingin menikmati wisata alam sekaligus mengenal budaya setempat.</p><h2>Daya tarik utama</h2><ul><li><strong>Balanting paring</strong>, yaitu menyusuri Sungai Amandit menggunakan rakit bambu.</li><li>Jalur trekking menuju perbukitan, air terjun, dan permukiman adat.</li><li>Kerajinan, tradisi, serta hasil kebun masyarakat Dayak Meratus.</li></ul><h3>Tips berkunjung</h3><ol><li>Periksa cuaca dan kondisi sungai sebelum mengikuti aktivitas air.</li><li>Gunakan alas kaki yang nyaman dan bawa perlindungan dari hujan.</li><li>Hormati aturan kampung serta mintalah izin sebelum memotret kegiatan warga.</li></ol><p><em>Waktu dan rute kegiatan dapat berubah mengikuti kondisi alam.</em> <u>Selalu ikuti arahan pemandu lokal.</u></p>",
                                "x_location": 45.97,
                                "y_location": 50.73,
                                "media": "storage/tourism/pegunungan-meratus-loksado.mp4",
                                "source_media": "YT Raga Mutalib",
                                "is_active": true,
                                "created_at": "2026-09-25 19:06:16",
                                "updated_at": "2026-10-04 21:08:58"
                            }
                        },
                        {
                            "legacy_id": 2,
                            "attributes": {
                                "name": "Air Terjun Lano",
                                "address": "Desa Lano, Kecamatan Jaro, Kabupaten Tabalong",
                                "description": "<h1>Air Terjun Lano</h1><p>Air Terjun Lano berada di kawasan hutan Desa Lano, Kecamatan Jaro, dekat perbatasan Kalimantan Selatan dan Kalimantan Timur. Perjalanan menuju air terjun menghadirkan suasana hutan yang masih alami, pepohonan besar, serta beberapa aliran sungai kecil.</p><h2>Pengalaman yang ditawarkan</h2><ul><li>Berjalan kaki sekitar 800 meter dari akses utama menuju air terjun.</li><li>Menikmati udara sejuk dan suara air di tengah kawasan hutan.</li><li>Mengamati vegetasi serta bentang alam khas wilayah Tabalong bagian utara.</li></ul><h3>Persiapan perjalanan</h3><ol><li>Gunakan sepatu dengan daya cengkeram baik.</li><li>Bawa air minum dan simpan kembali seluruh sampah.</li><li>Hindari mendekati arus deras ketika hujan atau debit air meningkat.</li></ol><p><strong>Keselamatan adalah prioritas.</strong> <em>Kondisi jalur dapat berubah setelah hujan.</em></p>",
                                "x_location": 52.09,
                                "y_location": 17.79,
                                "media": "storage/tourism/air-terjun-lano.mp4",
                                "source_media": "YT Air Terjun Lano",
                                "is_active": true,
                                "created_at": "2026-09-25 19:06:16",
                                "updated_at": "2026-09-26 00:45:42"
                            }
                        },
                        {
                            "legacy_id": 9,
                            "attributes": {
                                "name": "Gua Baru Hapu",
                                "address": "Desa Batu Hapu, Kabupaten Tapin, Kalimantan Selatan",
                                "description": "<p>Selanjutnya ada destinasi wisata Kalimantan Selatan yang penuh dengan sejarah unik, yaitu Gua Batu Hapu. Lokasinya berada di Desa Batu Hapu, Kabupaten Tapin, Kalimantan Selatan. Kamu bisa melihat keindahan stalaktit dan stalagmit alami dengan bentuk ornamen yang menakjubkan. Menurut mitos, asal-usul gua ini dari pecahan kapal seorang anak durhaka yang kemudian dikutuk menjadi batu. Itulah sebabnya, gua ini tidak hanya punya panorama yang indah, tapi juga dihiasi cerita rakyatnya. Untuk masuk ke Gua Batu Hapu, pengunjung dikenakan tiket masuk yang sangat terjangkau, sekitar Rp5.000 – Rp10.000 per orang.</p>",
                                "x_location": 34.84,
                                "y_location": 59.41,
                                "media": "storage/tourism/gua-baru-hapu.mp4",
                                "source_media": "Kanal youtube TapinTV",
                                "is_active": true,
                                "created_at": "2026-10-01 21:06:31",
                                "updated_at": "2026-10-04 21:11:12"
                            }
                        },
                        {
                            "legacy_id": 13,
                            "attributes": {
                                "name": "Pulau kaget",
                                "address": "Tabunganen Muara, Kec. Tabunganen, Kabupaten Barito Kuala, Kalimantan Selatan",
                                "description": "<p>Pulau Kaget adalah sebuah delta yang terletak di tengah-tengah Sungai Barito termasuk dalam wilayah administratif Kecamatan Tabunganen, Barito Kuala, Kalimantan Selatan. Pulau Kaget terletak dekat muara sungai Barito.</p><p></p><p>Barito Kuala memiliki beberapa delta yang disebut pulau. Delta tersebut terdiri dari Pulau Kembang, Pulau Bakut, Pulau Sugara, Pulau Alalak, Pulau Sewangi dan Pulau Kaget. Pulau tersebut terdapat di tengah-tengah Sungai Barito yang membelah Barito Kuala</p><p></p><p>Asal muasal Pulau ini dinamakan &#039;Pulau Kaget&#039; adalah karena sebagian orang yang ketika memasuki lokasi tersebut akan merasa kaget karena riuh suara yang dihasilkan oleh bekantan</p><p></p><p>Pulau ini terbentuk dari endapan lumpur di muara Sungai Barito, di mana alirannya membawa sedimen akibat dari pengikisan tepi dan dasar sungai. Sedimen ini berkumpul di muara sungai dan membentuk daratan yang ditumbuhi pepohonan hingga menjadi hutan lebat yang dihuni oleh berbagai macam hewan.</p><p></p><p>Pulau Kaget sudah ditetapkan sebagai cagar alam dengan luas 85 Ha. Kondisi alam pulau ini cukup kritis karena adanya penebangan pohon, khususnya pohon rambai padi yang merupakan sumber makanan bagi bekantan (Nasalis Larvatus). Bekantan merupakan maskot fauna Provinsi Kalimantan Selatan.</p><p></p><p>Pulau Kaget merupakan habitat bagi kera hidung panjang bekantan (Nasalis Larvatus), elang laut perut putih (Heliaetus Leucogaster), elang bondol (Haliastur Indus), elang hitam (Ictinaetus Malayensis), elang tikus (Elanus Caeruleus), elang (Spilornis Sheela), raja udang biru (Halyconchloris), burung madu kelapa (Anthereptes Malaccensis), burung madu (Nectarinia Jugularis). Selain itu, jenis flora yang terdapat di Pulau Kaget adalah rambai (Sonneratia Alba), panggang (Ficus sp), Jambu (Eugeni sp), tancang (Bruguiera sp), rengas (Gluta renghas), nipah (Nypa fructians), pandan (Pandanus sp), bakung (Crinum asiaticum), jeruju (Achantus illicifolius), dungun (Heretiera littoralis), dan lain-lain.</p><p></p><p>Pulau Kaget merupakan lokasi tempat Said Abdul-Rahman Alkadrie merompak kapal Inggris.</p><p></p><p>Kawasan pulau Kaget juga merupakan salah satu objek wisata yang berada di dalam kawasan hutan di Kabupaten Barito Kuala. Tidak jauh dari Banjarmasin, Pulau Kaget dapat ditempuh dalam waktu 15 menit dengan speed boat. Dan dapat menempuh dengan &#039;Kelotok&#039; yang memakan waktu sekitar satu setengah jam.</p>",
                                "x_location": 15.4,
                                "y_location": 72.13,
                                "media": "storage/tourism/pulau-kaget.mp4",
                                "source_media": "Kanal Youtube Bombastic Borneo",
                                "is_active": true,
                                "created_at": "2026-10-02 01:07:31",
                                "updated_at": "2026-10-06 02:31:17"
                            }
                        },
                        {
                            "legacy_id": 14,
                            "attributes": {
                                "name": "Taman Hutan Meranti",
                                "address": "Sebelimbingan, Kec. Pulau Laut Utara, Kab. Kotabaru, Kalimantan Selatan",
                                "description": "<p>Hutan Meranti adalah salah satu hutan di Indonesia yang terkenal karena keindahan alamnya. Kotabaru sendiri merupakan sebuah kabupaten di Provinsi Kalimantan Selatan, Indonesia. Hutan Meranti Putih memiliki keanekaragaman hayati yang kaya, termasuk berbagai spesies flora dan fauna yang langka. Selain menjadi habitat bagi berbagai jenis satwa liar, Hutan Meranti Putih juga memiliki potensi ekonomi yang besar melalui kegiatan-kegiatan seperti ekowisata dan pengelolaan sumber daya alam secara lestari. Namun, upaya pelestarian hutan ini juga menjadi penting untuk mencegah kerusakan lingkungan dan menjaga keseimbangan ekosistemnya.</p>",
                                "x_location": 68.96,
                                "y_location": 69.56,
                                "media": "storage/tourism/taman-hutan-meranti.mp4",
                                "source_media": "Kanal Youtube Banjarmasin Post News Video",
                                "is_active": true,
                                "created_at": "2026-10-02 01:25:16",
                                "updated_at": "2026-10-04 21:09:19"
                            }
                        },
                        {
                            "legacy_id": 15,
                            "attributes": {
                                "name": "Puncak Gunung Hauk",
                                "address": "Dayak Pitap, Kec. Tebing Tinggi, Kabupaten Balangan, Kalimantan Selatan",
                                "description": "<p></p><p>Kalimantan Selatan menyimpan pesona pegunungan yang tidak hanya menantang secara fisik, tetapi juga kaya akan nilai spiritual. Salah satunya adalah Gunung Hauk. Terletak di Kabupaten Balangan, gunung ini merupakan salah satu titik tertinggi di jajaran Pegunungan Meratus yang memegang peranan penting bagi kehidupan masyarakat lokal.</p><p></p><p></p><p>menyimpan pesona pegunungan yang tidak hanya menantang secara fisik, tetapi juga kaya akan nilai spiritual. Salah satunya adalah Gunung Hauk. Terletak di Kabupaten Balangan, gunung ini merupakan salah satu titik tertinggi di jajaran Pegunungan Meratus yang memegang peranan penting bagi kehidupan masyarakat lokal.</p><p></p><p></p><p></p>",
                                "x_location": 52.02,
                                "y_location": 41.76,
                                "media": "storage/tourism/puncak-gunung-hauk.mp4",
                                "source_media": "Kanal Youtube Banjarmasin Post News Video",
                                "is_active": true,
                                "created_at": "2026-10-02 01:53:11",
                                "updated_at": "2026-10-06 02:18:53"
                            }
                        }
                    ]
                },
                {
                    "legacy_id": 3,
                    "attributes": {
                        "name": "Wisata Religi",
                        "color": "#7963a9",
                        "description": "Masjid, makam ulama, dan tujuan perjalanan religi.",
                        "background": "storage/tourism/categories/G2R5ZCAtYUuO5HNsyWB2q3XVp6HobdDHx2OTZpJB.jpg",
                        "sort_order": 3,
                        "created_at": "2026-09-25 19:06:15",
                        "updated_at": "2026-10-05 00:23:52",
                        "icon": "landmark"
                    },
                    "locations": [
                        {
                            "legacy_id": 5,
                            "attributes": {
                                "name": "Masjid Sultan Suriansyah",
                                "address": "Jalan Kuin Utara, Kelurahan Kuin Utara, Kecamatan Banjarmasin Utara, Kota Banjarmasin",
                                "description": "<h1>Masjid Sultan Suriansyah</h1><p>Masjid Sultan Suriansyah atau Masjid Kuin merupakan salah satu masjid bersejarah di Kalimantan Selatan. Masjid ini berada di kawasan tepian Sungai Kuin dan berkaitan dengan Sultan Suriansyah, raja Banjar pertama yang memeluk Islam.</p><h2>Keunikan bangunan</h2><ul><li>Konstruksi yang banyak menggunakan kayu ulin.</li><li>Atap bertumpang yang mempertahankan karakter arsitektur tradisional Banjar.</li><li>Lingkungan bersejarah yang berdekatan dengan kompleks makam Sultan Suriansyah.</li></ul><h3>Adab berkunjung</h3><ol><li>Kenakan pakaian sopan dan jaga ketenangan selama berada di masjid.</li><li>Hindari mengganggu pelaksanaan ibadah.</li><li>Mintalah izin sebelum mengambil foto di area tertentu.</li></ol><p><strong>Masjid tetap berfungsi sebagai tempat ibadah.</strong> <u>Dahulukan kepentingan jemaah.</u></p>",
                                "x_location": 18.83,
                                "y_location": 61.61,
                                "media": "storage/tourism/masjid-sultan-suriansyah.mp4",
                                "source_media": "YT Kesultanan Nusantara",
                                "is_active": true,
                                "created_at": "2026-09-25 19:06:17",
                                "updated_at": "2026-10-04 20:44:48"
                            }
                        },
                        {
                            "legacy_id": 6,
                            "attributes": {
                                "name": "Makam Datu Sanggul",
                                "address": "Desa Suato Tatakan, Kecamatan Tapin Selatan, Kabupaten Tapin",
                                "description": "<h1>Makam Datu Sanggul</h1><p>Makam Datu Sanggul merupakan tujuan ziarah di Desa Suato Tatakan, Kecamatan Tapin Selatan. Datu Sanggul dikenal sebagai ulama dan tokoh masyarakat yang hidup pada abad ke-18 serta sezaman dengan Syekh Muhammad Arsyad al-Banjari.</p><h2>Makna kunjungan</h2><ul><li>Mengenal perjalanan tokoh agama yang dihormati masyarakat Tapin.</li><li>Melihat tradisi ziarah yang tetap dijalankan oleh masyarakat.</li><li>Mengunjungi salah satu rangkaian destinasi religi di kawasan Tatakan.</li></ul><h3>Adab ziarah</h3><ol><li>Berpakaian sopan dan berbicara dengan tenang.</li><li>Ikuti aturan serta arahan pengelola makam.</li><li>Jaga kebersihan dan hormati peziarah lain.</li></ol><p><em>Kawasan ini adalah ruang ibadah dan refleksi.</em> <u>Hindari kegiatan yang mengganggu kekhusyukan.</u></p>",
                                "x_location": 34.9,
                                "y_location": 52.69,
                                "media": "storage/tourism/makam-datu-sanggul.mp4",
                                "source_media": "YT Tribun Network",
                                "is_active": true,
                                "created_at": "2026-09-25 19:06:17",
                                "updated_at": "2026-10-04 20:42:27"
                            }
                        }
                    ]
                },
                {
                    "legacy_id": 4,
                    "attributes": {
                        "name": "Wisata Bahari",
                        "color": "#287f9c",
                        "description": "Pantai, pulau, dan wisata perairan.",
                        "background": "storage/tourism/categories/nl3yoMpdEFf5i3dTYiE5izpVogehTAu1kq2417Xh.jpg",
                        "sort_order": 4,
                        "created_at": "2026-09-25 19:06:15",
                        "updated_at": "2026-10-05 00:25:06",
                        "icon": "sun"
                    },
                    "locations": [
                        {
                            "legacy_id": 7,
                            "attributes": {
                                "name": "Pantai Angsana",
                                "address": "Desa Angsana, Kecamatan Angsana, Kabupaten Tanah Bumbu",
                                "description": "<h1>Pantai Angsana</h1><p>Pantai Angsana dikenal sebagai destinasi bahari di Kabupaten Tanah Bumbu. Garis pantai, suasana pesisir, dan kawasan terumbu karang di perairan sekitarnya menjadi daya tarik bagi pengunjung yang ingin menikmati kegiatan laut.</p><h2>Aktivitas wisata</h2><ul><li>Bersantai di pantai dan menikmati matahari terbenam.</li><li>Snorkeling atau menyelam pada titik yang diizinkan.</li><li>Menuju kawasan terumbu karang menggunakan perahu dan pemandu setempat.</li></ul><h3>Persiapan aktivitas laut</h3><ol><li>Periksa cuaca, gelombang, dan ketersediaan operator sebelum berangkat.</li><li>Gunakan pelampung serta perlengkapan sesuai standar.</li><li>Jangan menginjak, menyentuh, atau mengambil bagian dari terumbu karang.</li></ol><p><strong>Kelestarian laut adalah tanggung jawab bersama.</strong> <em>Bawa kembali sampah dan gunakan produk yang ramah lingkungan.</em></p>",
                                "x_location": 53.18,
                                "y_location": 80.93,
                                "media": "storage/tourism/pantai-angsana.mp4",
                                "source_media": "YT Duta TV",
                                "is_active": true,
                                "created_at": "2026-09-25 19:06:17",
                                "updated_at": "2026-10-04 21:09:51"
                            }
                        },
                        {
                            "legacy_id": 8,
                            "attributes": {
                                "name": "Pantai Batakan Baru",
                                "address": "Jalan Pariwisata, Desa Batakan, Kecamatan Panyipatan, Kabupaten Tanah Laut",
                                "description": "<h1>Pantai Batakan Baru</h1><p>Pantai Batakan Baru merupakan destinasi pesisir di Desa Batakan, Kecamatan Panyipatan. Kawasan ini memiliki garis pantai yang panjang, pepohonan rindang, serta panorama Pulau Datu yang dapat terlihat dari sekitar pantai.</p><h2>Daya tarik pantai</h2><ul><li>Area rekreasi keluarga dengan ruang terbuka di sepanjang pesisir.</li><li>Pemandangan matahari terbit dan terbenam saat kondisi cuaca mendukung.</li><li>Aktivitas berkuda, bersantai, dan menikmati kuliner dari usaha setempat.</li></ul><h3>Tips berkunjung</h3><ol><li>Perhatikan batas aman ketika bermain di dekat air.</li><li>Awasi anak-anak dan ikuti petunjuk pengelola kawasan.</li><li>Gunakan tempat sampah serta jaga kebersihan fasilitas umum.</li></ol><p><u>Fasilitas dan aktivitas dapat berubah mengikuti kondisi kawasan.</u> <em>Konfirmasikan informasi terbaru sebelum berangkat.</em></p>",
                                "x_location": 20.54,
                                "y_location": 91.81,
                                "media": "storage/tourism/pantai-batakan-baru.mp4",
                                "source_media": "YT Drone Channel",
                                "is_active": true,
                                "created_at": "2026-09-25 19:06:17",
                                "updated_at": "2026-10-04 21:10:43"
                            }
                        }
                    ]
                },
                {
                    "legacy_id": 5,
                    "attributes": {
                        "name": "Wisata Rekreasi",
                        "color": "#da251d",
                        "description": "Tempat rekreasi, hiburan, dan jalan jalan",
                        "background": null,
                        "sort_order": 5,
                        "created_at": "2026-10-01 21:38:29",
                        "updated_at": "2026-10-04 21:01:05",
                        "icon": "rocket"
                    },
                    "locations": [
                        {
                            "legacy_id": 10,
                            "attributes": {
                                "name": "Taman Labirin Pelaihari",
                                "address": "Kec. Tambang Ulang, Kabupaten Tanah Laut, Kalimantan Selatan",
                                "description": "<p></p><p></p><p><em>background </em></p><p></p>",
                                "x_location": 20.9,
                                "y_location": 80.93,
                                "media": "storage/tourism/taman-labirin-pelaihari.mp4",
                                "source_media": "Kanal Youtube Banjarmasin Post News Video",
                                "is_active": false,
                                "created_at": "2026-10-01 21:43:22",
                                "updated_at": "2026-10-02 01:53:35"
                            }
                        }
                    ]
                }
            ]
        },
        {
            "slug": "kuliner",
            "attributes": {
                "name": "Kuliner",
                "icon": "utensils",
                "description": "Nikmati cita rasa khas Banua di setiap perjalanan.",
                "title": "Peta Kuliner",
                "sub_title": "Kalimantan Selatan",
                "logo": "storage/maps/a8ddcc77-1bdc-4984-85df-b459160c7800.png",
                "banner": "storage/maps/5db1b03b-02e9-4eb3-891f-462249619208.jpg",
                "color": "#e4a02b",
                "sort_order": 1,
                "is_active": true,
                "created_at": "2026-10-02 00:56:44",
                "updated_at": "2026-10-05 03:03:38"
            },
            "categories": [
                {
                    "legacy_id": 2,
                    "attributes": {
                        "name": "Makanan Khas",
                        "color": "#da251d",
                        "description": "Hidangan khas Banjar dan Kalimantan Selatan.",
                        "background": "storage/culinary/categories/doTKVOBB1U1dCuX5kH4jgQVVgDzEDSinXiucpMVT.jpg",
                        "sort_order": 0,
                        "created_at": "2026-10-02 01:25:28",
                        "updated_at": "2026-10-05 02:02:19",
                        "icon": "fire"
                    },
                    "locations": [
                        {
                            "legacy_id": 1,
                            "attributes": {
                                "name": "Soto Banjar",
                                "address": "Jl. Banua Anyar No.6, Benua Anyar, Kec. Banjarmasin Timur, Kota Banjarmasin, Kalimantan Selatan",
                                "description": "<h1>Soto Banjar</h1><p>Soto Banjar adalah hidangan berkuah khas Banjarmasin dengan ayam dan aroma rempah seperti kayu manis, cengkih, serta kapulaga. Kuahnya dapat disajikan bening atau lebih gurih sesuai racikan masing-masing rumah makan.</p><h2>Ciri khas sajian</h2><ul><li>Ayam suwir dengan kuah rempah yang harum.</li><li>Disantap bersama ketupat atau nasi, perkedel, telur, dan soun.</li><li>Sambal serta jeruk nipis dapat ditambahkan sesuai selera.</li></ul><h3>Pengalaman kuliner</h3><ol><li>Nikmati selagi hangat agar aroma rempahnya terasa.</li><li>Coba pelengkap satu per satu untuk menemukan paduan rasa favorit.</li><li>Kawasan Banua Anyar menjadi salah satu tujuan untuk mencari sajian ini.</li></ol><p><strong>Soto Banjar mencerminkan kekayaan rempah dan tradisi makan masyarakat Banjar.</strong></p>",
                                "x_location": 16.5,
                                "y_location": 68.55,
                                "media": "storage/culinary/soto-banjar-fb35bf08-e4f8-4457-b441-9770776b6b08.mp4",
                                "source_media": "Kanal Youtube Cerita Rasa",
                                "is_active": true,
                                "created_at": "2026-10-02 01:25:28",
                                "updated_at": "2026-10-02 20:52:54"
                            }
                        },
                        {
                            "legacy_id": 2,
                            "attributes": {
                                "name": "Ketupat Kandangan",
                                "address": "Jl Ahmad Yani no 21, Kec. Kandangan, Kabupaten Hulu Sungai Selatan, Kalimantan Selatan",
                                "description": "<h1>Ketupat Kandangan</h1><p>Hidangan asal Kandangan ini memadukan ketupat bertekstur padat dengan ikan haruan panggang dan kuah santan berbumbu. Perpaduan gurih, aroma ikan panggang, dan rempah menjadikannya salah satu sajian yang dikenal dari Hulu Sungai Selatan.</p><h2>Yang membuatnya khas</h2><ul><li>Ikan haruan atau gabus menjadi lauk yang umum disajikan.</li><li>Kuah santan berbumbu meresap ke ketupat.</li><li>Beberapa rumah makan juga menyediakan pilihan lauk lain.</li></ul><h3>Saat menikmati</h3><ol><li>Cicipi kuah dan ikan bersama ketupat dalam satu suapan.</li><li>Tambahkan sambal bila menyukai rasa yang lebih pedas.</li><li>Tanyakan pilihan lauk yang tersedia di tempat makan.</li></ol><p><em>Ketupat Kandangan kerap dinikmati sebagai sarapan maupun hidangan keluarga.</em></p>",
                                "x_location": 43.5,
                                "y_location": 52.7,
                                "media": "storage/culinary/ketupat-kandangan-13df5d3e-52a5-44a0-84c7-f5a18f6face8.mp4",
                                "source_media": "Kanal Youtube WJB Channel",
                                "is_active": true,
                                "created_at": "2026-10-02 01:25:28",
                                "updated_at": "2026-10-02 20:37:03"
                            }
                        },
                        {
                            "legacy_id": 5,
                            "attributes": {
                                "name": "Mie Habang",
                                "address": "Kota Banjarmasin",
                                "description": "<h1>Mie Habang</h1><p>Mie Habang adalah sajian mi berwarna merah yang dikenal dalam kuliner Banjar. Hidangan ini memiliki rasa gurih-manis dan biasanya disajikan sebagai menu utama dengan racikan serta pelengkap yang berbeda di setiap penjual.</p><h2>Karakter hidangan</h2><ul><li>Warna merah menjadi ciri yang mudah dikenali.</li><li>Bumbu dan cara penyajian mengikuti resep masing-masing penjual.</li><li>Dapat disantap sebagai hidangan sehari-hari maupun menu berbuka.</li></ul><h3>Menjelajahi rasa Banjar</h3><ol><li>Coba sajian dari penjual lokal untuk mengenal variasi racikannya.</li><li>Tanyakan tingkat pedas dan pilihan pelengkap sebelum memesan.</li><li>Nikmati selagi hangat.</li></ol><p><strong>Mie Habang merupakan salah satu hidangan yang turut memperkenalkan rasa Banua.</strong></p>",
                                "x_location": 18.85,
                                "y_location": 67.19,
                                "media": "storage/culinary/mie-habang-b0cb16a1-f6ca-45c1-a290-476deafa8603.mp4",
                                "source_media": "Kanal Youtube KOMPASTV",
                                "is_active": true,
                                "created_at": "2026-10-02 01:25:28",
                                "updated_at": "2026-10-02 21:01:02"
                            }
                        },
                        {
                            "legacy_id": 13,
                            "attributes": {
                                "name": "Gangan Paliat",
                                "address": "Mabu'un, Kec. Murung Pudak, Kabupaten Tabalong, Kalimantan Selatan",
                                "description": "<p>Gangan Paliat (Kuliner Ikan Kuah Santan Kental) adalah hidangan paling ikonik dan wajib dicoba di Tabalong. Gangan Paliat merupakan masakan berbahan dasar ikan (biasanya patin, haruan/gabus, nila, atau udang galah) yang dimasak dengan kuah santan kental berempah kuning khas, menghasilkan rasa gurih-asam yang pekat. Hidangan ini biasa disajikan bersama sayur rebusan dan mandai goreng.</p>",
                                "x_location": 44.82,
                                "y_location": 29.69,
                                "media": "storage/culinary/gangan-paliat-a98d5ac3-2d78-4739-8fd6-7ae2fce2c69b.mp4",
                                "source_media": "Kanal Youtube Endro S Efendi",
                                "is_active": true,
                                "created_at": "2026-10-02 23:53:10",
                                "updated_at": "2026-10-02 23:53:10"
                            }
                        }
                    ]
                },
                {
                    "legacy_id": 3,
                    "attributes": {
                        "name": "Olahan Ikan",
                        "color": "#287f9c",
                        "description": "Hidangan berbahan ikan sungai dan hasil perairan Banua.",
                        "background": "storage/culinary/categories/WKF3IoIHe8tEorTNzp8VMs6gTt5sCkXerDfIHv8m.png",
                        "sort_order": 0,
                        "created_at": "2026-10-02 01:25:28",
                        "updated_at": "2026-10-05 02:20:57",
                        "icon": "flask"
                    },
                    "locations": [
                        {
                            "legacy_id": 4,
                            "attributes": {
                                "name": "Ikan Bakar Banua Anyar",
                                "address": "Kawasan Wisata Kuliner Banua Anyar, Kota Banjarmasin",
                                "description": "<h1>Ikan Bakar Banua Anyar</h1><p>Kawasan Banua Anyar di Banjarmasin dikenal sebagai salah satu tujuan untuk menikmati ikan bakar. Ikan sungai atau ikan tambak seperti patin, lais, haruan, papuyu, dan seluang dapat dijumpai dalam beragam sajian, bergantung pada ketersediaan.</p><h2>Pelengkap yang umum</h2><ul><li>Nasi hangat dan sambal.</li><li>Lalapan atau sayuran rebus sebagai pendamping.</li><li>Pilihan ikan dan cara pengolahan yang berbeda antarwarung.</li></ul><h3>Sebelum memesan</h3><ol><li>Tanyakan jenis ikan yang tersedia hari itu.</li><li>Pilih tingkat kematangan sesuai selera.</li><li>Ikuti arahan pengelola saat menikmati area tepi sungai.</li></ol><p><em>Suasana tepian sungai membuat pengalaman bersantap di Banua Anyar terasa khas.</em></p>",
                                "x_location": 25.3,
                                "y_location": 68.9,
                                "media": null,
                                "source_media": "https://www.indonesia.travel/cn/en/travel-ideas/gastronomy/kuliner-khas-kalimantan-selatan-ini-pantang-dilewatkan",
                                "is_active": false,
                                "created_at": "2026-10-02 01:25:28",
                                "updated_at": "2026-10-04 19:49:56"
                            }
                        },
                        {
                            "legacy_id": 8,
                            "attributes": {
                                "name": "Haruan Masak Habang",
                                "address": "Kota Banjarmasin dan Banjarbaru, Kalimantan Selatan",
                                "description": "<h1>Haruan Masak Habang</h1><p>Haruan masak habang menyajikan ikan gabus dengan bumbu merah khas Banjar. Hidangan ini kerap menjadi lauk nasi kuning dan mudah dijumpai di warung sarapan maupun rumah makan Banjar.</p><h2>Ciri sajian</h2><ul><li>Ikan haruan atau gabus dimasak dengan bumbu habang.</li><li>Rasa gurih dan rempah berpadu dengan karakter bumbu merah.</li><li>Sering disajikan sebagai lauk nasi kuning.</li></ul><h3>Ide menikmati</h3><ol><li>Padukan dengan nasi kuning atau nasi putih hangat.</li><li>Tanyakan tingkat kepedasan dan lauk pendamping yang tersedia.</li><li>Coba sajian pada waktu sarapan untuk mengenal kebiasaan kuliner setempat.</li></ol><p><em>Masak habang juga digunakan untuk beragam lauk dalam tradisi kuliner Banjar.</em></p>",
                                "x_location": 27.4,
                                "y_location": 73.65,
                                "media": "storage/culinary/haruan-masak-habang-570fab9b-9be0-4ccb-b97f-9ef147a65b9a.jpg",
                                "source_media": "https://www.indonesia.travel/id/id/destination/kalimantan/south-kalimantan",
                                "is_active": true,
                                "created_at": "2026-10-02 01:56:41",
                                "updated_at": "2026-10-04 19:25:08"
                            }
                        },
                        {
                            "legacy_id": 9,
                            "attributes": {
                                "name": "Wadi Patin",
                                "address": "Kota Banjarmasin dan wilayah Banjar, Kalimantan Selatan",
                                "description": "<h1>Wadi Patin</h1><p>Wadi patin adalah olahan ikan patin melalui proses fermentasi tradisional. Proses pengolahan tersebut menghasilkan cita rasa asam-gurih yang khas dan menjadi salah satu cara masyarakat mengolah ikan sungai.</p><h2>Cara penyajian</h2><ul><li>Dapat digoreng atau dimasak kembali dengan bumbu.</li><li>Sering disantap bersama nasi hangat sebagai lauk.</li><li>Rasa dan tingkat fermentasi dapat berbeda menurut resep pembuatnya.</li></ul><h3>Saat mencicipi</h3><ol><li>Tanyakan cara penyajian dan tingkat rasa asamnya.</li><li>Padukan dengan nasi serta sayur atau sambal sesuai selera.</li><li>Pilih produk yang disimpan dan disajikan dengan baik.</li></ol><p><strong>Wadi memperlihatkan cara pengolahan ikan yang diwariskan di Kalimantan.</strong></p>",
                                "x_location": 19.14,
                                "y_location": 71.1,
                                "media": "storage/culinary/wadi-patin-404b0bb2-356d-402d-aead-4d8840167212.jpg",
                                "source_media": "https://www.indonesia.travel/id/id/travel-ideas/wadi-patin",
                                "is_active": true,
                                "created_at": "2026-10-02 01:56:41",
                                "updated_at": "2026-10-04 19:47:46"
                            }
                        }
                    ]
                },
                {
                    "legacy_id": 4,
                    "attributes": {
                        "name": "Kue & Wadai",
                        "color": "#c67a38",
                        "description": "Kue tradisional dan wadai khas Banua.",
                        "background": "storage/culinary/categories/NhGkRyA6Mu32oNoou0uZfMsmDF2Pe0igyfCvKiCl.jpg",
                        "sort_order": 0,
                        "created_at": "2026-10-02 01:25:28",
                        "updated_at": "2026-10-05 02:22:56",
                        "icon": "cake-candles"
                    },
                    "locations": [
                        {
                            "legacy_id": 6,
                            "attributes": {
                                "name": "Bingka Banjar",
                                "address": "Kota Banjarmasin dan Banjarbaru, Kalimantan Selatan",
                                "description": "<h1>Bingka Banjar</h1><p>Bingka Banjar adalah kue tradisional bercita rasa manis dan bertekstur lembut. Bahan yang lazim digunakan antara lain tepung, telur, santan, dan gula; kini bingka juga hadir dengan beragam varian rasa.</p><h2>Yang membuatnya istimewa</h2><ul><li>Teksturnya lembut dengan rasa gurih-manis.</li><li>Memiliki beragam varian sesuai bahan dan resep pembuatnya.</li><li>Sering disajikan dalam acara keluarga, perayaan, dan waktu berkumpul.</li></ul><h3>Tips memilih</h3><ol><li>Pilih varian sesuai selera dan tanyakan bahan yang digunakan.</li><li>Nikmati sebagai teman minum teh atau kopi.</li><li>Perhatikan petunjuk penyimpanan pada kemasan.</li></ol><p><em>Bingka menjadi salah satu wadai yang lekat dengan tradisi kuliner Banjar.</em></p>",
                                "x_location": 19.24,
                                "y_location": 66.99,
                                "media": null,
                                "source_media": "https://www.indonesia.travel/id/id/destination/kalimantan/south-kalimantan",
                                "is_active": false,
                                "created_at": "2026-10-02 01:25:28",
                                "updated_at": "2026-10-02 20:58:16"
                            }
                        },
                        {
                            "legacy_id": 7,
                            "attributes": {
                                "name": "Wadai Cincin",
                                "address": "\"Warung Wadai Cincin Laris\" Jl. Jend. Sudirman No.63, Tibung Raya, Kec. Kandangan, Kabupaten Hulu Sungai Selatan, Kalimantan Selatan",
                                "description": "<h1>Wadai Cincin</h1><p>Wadai cincin merupakan kudapan tradisional Banjar yang dikenal dari bentuknya menyerupai cincin dan rasa manisnya. Kue ini menjadi bagian dari ragam wadai yang dijumpai di pasar tradisional serta lapak kue lokal.</p><h2>Pengalaman mencicipi</h2><ul><li>Bentuk dan ukuran dapat berbeda menurut pembuatnya.</li><li>Cocok dinikmati sebagai kudapan di sela waktu makan.</li><li>Dapat ditemukan bersama aneka wadai Banjar lainnya.</li></ul><h3>Saat berkunjung ke pasar</h3><ol><li>Tanyakan pilihan wadai yang baru dibuat.</li><li>Coba beberapa jenis kue untuk mengenal ragam penganan Banjar.</li><li>Gunakan kemasan yang sesuai jika ingin dibawa pulang.</li></ol><p><em>Ragam wadai menunjukkan kekayaan tradisi penganan masyarakat Banjar.</em></p>",
                                "x_location": 38.36,
                                "y_location": 51.28,
                                "media": "storage/culinary/wadai-cincin-c448319d-472f-478e-9411-16333f11edb9.webp",
                                "source_media": "https://repositori.kemendikdasmen.go.id/27388/2/Makna%20Simbolik%20dan%20Nilai%20Budaya%20Kuliner%20Wadai%20Banjar%2041%20Macam%20Banjar%20Kalsel.pdf",
                                "is_active": true,
                                "created_at": "2026-10-02 01:25:28",
                                "updated_at": "2026-10-04 19:57:13"
                            }
                        },
                        {
                            "legacy_id": 10,
                            "attributes": {
                                "name": "Amparan Tatak",
                                "address": "Kota Banjarmasin, Kalimantan Selatan",
                                "description": "<h1>Amparan Tatak</h1><p>Amparan tatak adalah kue basah tradisional Banjar yang umumnya dibuat dari tepung beras, santan, gula, dan pisang. Adonannya dikukus dalam loyang, lalu dipotong menjadi bagian-bagian untuk disajikan.</p><h2>Tekstur dan rasa</h2><ul><li>Perpaduan rasa manis pisang dan gurih santan.</li><li>Teksturnya lembut dan disajikan dalam potongan.</li><li>Resep keluarga dapat memiliki variasi bahan dan lapisan.</li></ul><h3>Menikmati wadai</h3><ol><li>Cari di pasar wadai atau toko kue tradisional setempat.</li><li>Nikmati sebagai kudapan atau teman minum.</li><li>Tanyakan bahan dan cara penyimpanan bila membeli untuk dibawa pulang.</li></ol><p><strong>Amparan tatak memperkaya ragam kue tradisional Banjar.</strong></p>",
                                "x_location": 20.65,
                                "y_location": 69.44,
                                "media": "storage/culinary/amparan-tatak-406a26f7-26ab-4629-902e-944cac5fec0b.jpg",
                                "source_media": "https://www.visitbanjarmasin.id/en/kuliner",
                                "is_active": true,
                                "created_at": "2026-10-02 01:56:41",
                                "updated_at": "2026-10-04 19:38:57"
                            }
                        },
                        {
                            "legacy_id": 15,
                            "attributes": {
                                "name": "Apam Barabai",
                                "address": "Sepanjang jalan Ahmad Yani, Barabai Utara, Kec. Barabai, Kabupaten Hulu Sungai Tengah, Kalimantan Selatan",
                                "description": "<p>Apam Barabai merupakan kue tradisional legendaris sekaligus warisan kuliner kebanggaan dari Kota Barabai, Kabupaten Hulu Sungai Tengah (HST), Kalimantan Selatan. Berkat nilai sejarah dan keunikan budayanya, kue basah ini telah ditetapkan sebagai Warisan Budaya Takbenda (WBTb) oleh Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi sejak tahun 2012.</p><p>Kue tradisional ini memiliki karakteristik yang sangat khas dibandingkan kue apem dari daerah lain:</p><p>• Bentuk &amp; Tekstur: Berbentuk bulat dan tipis, dengan tekstur yang luar biasa lembut, kenyal, dan sedikit membal saat digigit.</p><p>• Bahan Alami: Dibuat dari formula sederhana berupa tepung beras, santan, tape singkong, serta gula. Penggunaan tape singkong berfungsi sebagai pengembang alami yang sekaligus memberikan aroma wangi khas hasil fermentasi.</p><p>• Dua Varian Rasa Utama:</p><p>\t1. Varian Gula Merah (Aren): Memiliki warna merah kecokelatan dengan rasa manis legit yang kuat dan aroma karamel aren yang pekat.</p><p>\t2. Varian Gula Putih: Berwarna putih bersih dengan rasa manis yang lebih ringan dan bersih.</p><p>• Kemasan Tradisional: Biasanya dikemas bertumpuk rapi menggunakan bungkusan daun pisang yang menyumbang aroma segar alami serta menjaga kue tetap lembap. Hebatnya, kue ini dapat bertahan hingga 3 hari di suhu ruang tanpa bahan pengawet buatan</p>",
                                "x_location": 43.65,
                                "y_location": 43.55,
                                "media": "storage/culinary/apam-barabai-757d78e0-1293-47a0-af55-c0c9469fab5a.mp4",
                                "source_media": "Kanal Youtube Banjarmasin Post News Video",
                                "is_active": true,
                                "created_at": "2026-10-03 01:58:27",
                                "updated_at": "2026-10-03 01:58:27"
                            }
                        }
                    ]
                },
                {
                    "legacy_id": 5,
                    "attributes": {
                        "name": "Camilan & Banua",
                        "color": "#7963a9",
                        "description": "Camilan dan penganan khas Kalimantan Selatan.",
                        "background": "storage/culinary/categories/XIeGRPOy5ZOKR4GNJMXqzLpbfmCyyOve2uDyAJaW.jpg",
                        "sort_order": 0,
                        "created_at": "2026-10-02 01:25:28",
                        "updated_at": "2026-10-05 02:30:57",
                        "icon": "gift"
                    },
                    "locations": [
                        {
                            "legacy_id": 3,
                            "attributes": {
                                "name": "Dodol Kandangan",
                                "address": "Jl. Kapuh Madani, Telaga Bidadari, Kec. Sungai Raya, Kabupaten Hulu Sungai Selatan, Kalimantan Selatan",
                                "description": "<h1>Dodol Kandangan</h1><p>Dodol Kandangan dikenal dengan rasa manis dan teksturnya yang kenyal. Adonan berbahan santan, gula merah, dan tepung ketan dimasak perlahan sambil terus diaduk hingga menjadi legit.</p><h2>Keistimewaan dodol</h2><ul><li>Proses memasak yang lama menghasilkan tekstur lembut dan padat.</li><li>Biasanya dipotong kecil sehingga mudah disajikan atau dibagikan.</li><li>Daya simpannya menjadikannya pilihan buah tangan dari Kandangan.</li></ul><h3>Untuk buah tangan</h3><ol><li>Pilih kemasan yang masih tertutup baik.</li><li>Simpan sesuai petunjuk pada kemasan.</li><li>Tanyakan varian dan tanggal produksi kepada penjual.</li></ol><p><strong>Dodol ini menjadi bagian dari tradisi penganan dan oleh-oleh Hulu Sungai Selatan.</strong></p>",
                                "x_location": 40.92,
                                "y_location": 50.39,
                                "media": "storage/culinary/dodol-kandangan-2bfab63e-ca45-44ac-865a-0774e80963bc.mp4",
                                "source_media": "Kanal Youtube Tribun Network Banjarmasin Post",
                                "is_active": true,
                                "created_at": "2026-10-02 01:25:28",
                                "updated_at": "2026-10-02 20:19:03"
                            }
                        },
                        {
                            "legacy_id": 11,
                            "attributes": {
                                "name": "Amplang Ikan Kotabaru",
                                "address": "Jl. Patmaraga, Kotabaru Tengah, Kec. Pulau Laut Utara, Kab. Kotabaru, Kalimantan Selatan",
                                "description": "<h1>Amplang Ikan Kotabaru</h1><p>Amplang ikan adalah camilan renyah berbahan ikan yang dikenal sebagai salah satu buah tangan dari wilayah pesisir Kalimantan. Kotabaru menjadi salah satu daerah penghasil amplang di Kalimantan Selatan.</p><h2>Kenali camilan ini</h2><ul><li>Teksturnya renyah dan praktis dibawa dalam kemasan.</li><li>Rasa dan jenis ikan dapat berbeda antara produsen.</li><li>Dapat ditemukan di toko oleh-oleh dan sentra produk lokal.</li></ul><h3>Memilih oleh-oleh</h3><ol><li>Periksa kemasan dan tanggal kedaluwarsa.</li><li>Pilih ukuran kemasan yang sesuai untuk perjalanan.</li><li>Simpan dalam wadah tertutup agar kerenyahannya terjaga.</li></ol><p><em>Amplang memperkenalkan hasil perikanan daerah dalam bentuk camilan.</em></p>",
                                "x_location": 70.21,
                                "y_location": 66.21,
                                "media": "storage/culinary/amplang-ikan-kotabaru-9df34b24-9f58-4ee4-9bb6-5c54e5a1c3c9.mp4",
                                "source_media": "Kanal Youtube Ahmad Bungas",
                                "is_active": true,
                                "created_at": "2026-10-02 01:56:41",
                                "updated_at": "2026-10-02 20:20:19"
                            }
                        },
                        {
                            "legacy_id": 12,
                            "attributes": {
                                "name": "Mandai Goreng",
                                "address": "Kabupaten Hulu Sungai Selatan dan wilayah Banjar",
                                "description": "<h1>Mandai Goreng</h1><p>Mandai dibuat dari kulit buah cempedak yang diawetkan melalui fermentasi, lalu dapat diolah kembali, salah satunya dengan cara digoreng. Rasanya gurih dengan aroma dan karakter fermentasi yang khas.</p><h2>Cara menikmati</h2><ul><li>Dapat digoreng hingga bagian luarnya kecokelatan.</li><li>Sering disajikan sebagai lauk pendamping nasi.</li><li>Rasa akhir dipengaruhi lama fermentasi dan bumbu yang digunakan.</li></ul><h3>Saat mencicipi</h3><ol><li>Tanyakan tingkat fermentasi jika belum pernah mencobanya.</li><li>Nikmati bersama nasi hangat dan lauk sederhana.</li><li>Pilih produk yang disimpan dengan baik.</li></ol><p><strong>Mandai menunjukkan pemanfaatan bahan pangan lokal dalam tradisi kuliner Banjar.</strong></p>",
                                "x_location": 47,
                                "y_location": 57,
                                "media": "storage/culinary/mandai-goreng-b55c255f-215d-48a3-8b85-6728826140de.jpg",
                                "source_media": "https://www.visitbanjarmasin.id/en/kuliner",
                                "is_active": true,
                                "created_at": "2026-10-02 01:56:41",
                                "updated_at": "2026-10-04 19:52:20"
                            }
                        },
                        {
                            "legacy_id": 14,
                            "attributes": {
                                "name": "Gula Batu Itik",
                                "address": "Jl. Surya Wangsa No.10, Kembang Kuning, Kec. Amuntai Tengah, Kabupaten Hulu Sungai Utara, Kalimantan Selatan",
                                "description": "<p>Jika anda berkunjung ke Amuntai Hulu Sungai Utara (HSU), jangan lupa untuk membeli oleh-oleh makanan khas unik yang terkenal dan hanya dibuat di Kota Amuntai.  Makanan khas ini sejenis permen yang terbuat dari gula pasir asli. Orang-orang di daerah ini biasa menyebutnya gula batu. Uniknya, gula batu khas Kota Amuntai ini memiliki beraneka ragam bentuk, seperti bebek, bunga, kapal layar, dan lain-lain.  Gula batu unik ini dibuat secara manual, tanpa menggunakan mesin atau cetakan.</p><p></p><p>Produsen gula batu khas Amuntai tersebut kini sudah sangat jarang ditemukan. Padahal jenis makanan khas ini masih memiliki peminat yang cukup tinggi.</p><p></p><p>Setelah ditelusuri, gula batu unik yang termasuk kerajinan tangan ini ternyata hanya dibuat oleh beberapa keluarga yang masih ada hubungan kerabat.  Resep pembuatannya diwariskan secara turun-temurun.</p><p></p><p>Salah seorang produsen gula batu unik khas Amuntai, Ibu Masrufah, saat ditemui di kediamannya di Desa Kembang Kuning Kecamatan Amuntai Tengah baru-baru ini, membenarkan bahwa gula batu unik khas Amuntai tersebut memang hanya dibuat oleh keluarganya secara turun-temurun.</p><p></p><p>“Resep pembuatan gula batu ini telah ada turun-temurun di keluarga saya. Pembuatannya benar-benar tanpa menggunakan mesin atau cetakan”, ujar Ibu Masrufah.</p><p></p><p>Gula batu ini selain dijadikan oleh-oleh, seringkali dijadikan kado ataupun hantaran dalam acara perkawinan.  Harga gula batu unik tersebut bervariasi, mulai dari yang paling murah dengan harga lima ribu rupiah hingga ratusan ribu rupiah, tergantung dari ukuran dan tingkat kesulitan pembuatannya berdasarkan pesanan konsumen.</p><p></p><p>Pembeli bisa mendapatkan gula batu unik tersebut di pasar Amuntai atau membeli langsung dari produsennya</p>",
                                "x_location": 38.96,
                                "y_location": 39.45,
                                "media": "storage/culinary/gula-batu-itik-256a80b3-7b23-4f84-b778-506973f9dcf3.mp4",
                                "source_media": "Kanal Youtube TRANS7 OFFICIAL",
                                "is_active": true,
                                "created_at": "2026-10-03 00:33:37",
                                "updated_at": "2026-10-03 00:58:02"
                            }
                        }
                    ]
                }
            ]
        },
        {
            "slug": "transportasi",
            "attributes": {
                "name": "Transportasi",
                "icon": "bus",
                "description": "Temukan akses perjalanan untuk menjelajahi Kalimantan Selatan.",
                "title": "Peta Transportasi Kalimantan Selatan",
                "sub_title": "Interactive Map Guidance",
                "logo": "storage/maps/ffc51aae-1365-4fa8-b89f-1f2d4a60169c.png",
                "banner": "storage/maps/da4b62f5-fe9f-4925-a53d-dc37fba8426d.jpg",
                "color": "#da251d",
                "sort_order": 2,
                "is_active": true,
                "created_at": "2026-10-02 19:12:57",
                "updated_at": "2026-10-05 03:03:52"
            },
            "categories": [
                {
                    "legacy_id": 1,
                    "attributes": {
                        "name": "Bandara",
                        "color": "#1f5da8",
                        "description": "Bandara dan fasilitas penerbangan di Kalimantan Selatan.",
                        "background": "storage/transportation/categories/SAgcfVwGarQlLDZwG3JdzWv0FcsYq30kq5alEkLL.jpg",
                        "sort_order": 1,
                        "created_at": "2026-10-02 19:12:57",
                        "updated_at": "2026-10-05 02:35:16",
                        "icon": "plane"
                    },
                    "locations": [
                        {
                            "legacy_id": 1,
                            "attributes": {
                                "name": "Bandara Internasional Syamsudin Noor",
                                "address": "Jl. Angkasa, Kelurahan Landasan Ulin Timur, Kecamatan Landasan Ulin, Kota Banjarbaru, Kalimantan Selatan",
                                "description": "<p>Bandar Udara Internasional Syamsudin Noor (IATA: BDJ; ICAO: WAOO) berada di Landasan Ulin Timur, Kota Banjarbaru. Bandara yang dikelola PT Angkasa Pura Indonesia ini menjadi gerbang penerbangan utama Kalimantan Selatan.</p><h3>Rute penerbangan</h3><ul><li><strong>Antarkota:</strong> terhubung langsung dengan Jakarta, Surabaya, Semarang, Balikpapan, Denpasar, Makassar, Yogyakarta, dan Pontianak.</li><li><strong>Dalam Kalimantan Selatan:</strong> penerbangan menuju Bandar Udara Bersujud di Batulicin dan Bandar Udara Gusti Sjamsir Alam di Kotabaru.</li></ul><h3>Fasilitas penumpang</h3><ul><li>Terminal domestik seluas 77.569 meter persegi dengan restoran, pujasera, kedai kopi, layanan perbankan, dan galeri pengantar.</li><li>Landasan pacu berukuran 2.500 × 45 meter untuk kegiatan penerbangan.</li></ul><p>Data Direktorat Jenderal Perhubungan Udara mencatat 1.542.321 penumpang melalui bandara ini pada 2025.</p>",
                                "x_location": 24,
                                "y_location": 72.39,
                                "media": "storage/transportation/bandara-internasional-syamsudin-noor-e0918246-f504-4a53-a77d-86c6a20339a3.mp4",
                                "source_media": "Youtube TvONE HD",
                                "is_active": true,
                                "created_at": "2026-10-02 19:12:58",
                                "updated_at": "2026-10-03 01:22:06"
                            }
                        },
                        {
                            "legacy_id": 11,
                            "attributes": {
                                "name": "Bandar Udara Bersujud",
                                "address": "Jl. Kodeco KM 2, Bersujud, Kecamatan Simpang Empat, Kabupaten Tanah Bumbu, Kalimantan Selatan",
                                "description": "<p>Bandar Udara Bersujud (IATA: BTW; ICAO: WAOC) berada di Jalan Kodeco KM 2, Kecamatan Simpang Empat, Kabupaten Tanah Bumbu. Bandara yang dikelola pemerintah daerah ini melayani penerbangan domestik untuk kawasan Batulicin.</p><h3>Koneksi penerbangan</h3><p>Direktorat Jenderal Perhubungan Udara mencatat rute antara Bersujud dan Bandar Udara Syamsudin Noor di Banjarbaru. Rute tersebut menghubungkan Tanah Bumbu dengan pusat penerbangan utama Kalimantan Selatan.</p><h3>Prasarana</h3><ul><li>Landasan pacu berukuran 1.800 × 45 meter.</li><li>Gedung terminal domestik seluas 760 meter persegi dengan kapasitas rancangan 30.100 penumpang per tahun.</li></ul><p>Sepanjang 2025, Kementerian Perhubungan mencatat 13.288 penumpang dan 334 pergerakan pesawat di bandara ini.</p>",
                                "x_location": 60.33,
                                "y_location": 72.81,
                                "media": "storage/transportation/bandar-udara-bersujud-bd9ac038-7674-4d0c-89fe-e1e01168febb.mp4",
                                "source_media": "Youtube Indosiar",
                                "is_active": true,
                                "created_at": "2026-10-02 19:12:58",
                                "updated_at": "2026-10-03 02:13:37"
                            }
                        },
                        {
                            "legacy_id": 14,
                            "attributes": {
                                "name": "Bandar Udara Gusti Sjamsir Alam",
                                "address": "Jl. Raya Stagen KM 10, Desa Stagen, Kecamatan Pulau Laut Utara, Kabupaten Kotabaru, Kalimantan Selatan 72114",
                                "description": "<p>Bandar Udara Gusti Sjamsir Alam (IATA: KBU; ICAO: WAOK) berada di Stagen, Kecamatan Pulau Laut Utara, Kabupaten Kotabaru. Bandara domestik yang dikelola UPT Direktorat Jenderal Perhubungan Udara ini menjadi pintu udara kawasan Pulau Laut.</p><h3>Rute penerbangan</h3><ul><li>Rute ke Bandar Udara Syamsudin Noor menghubungkan Kotabaru dengan Banjarbaru.</li><li>Direktorat Jenderal Perhubungan Udara juga mencatat koneksi domestik menuju Bandar Udara Sultan Hasanuddin, Makassar.</li></ul><h3>Fasilitas dan prasarana</h3><ul><li>Terminal domestik seluas sekitar 1.692 meter persegi dan landasan pacu berukuran 1.650 × 30 meter.</li><li>Kantin, musala, ruang menyusui, taman bermain, ruang tunggu CIP, toilet umum, dan toilet aksesibel.</li></ul><p>Data Kementerian Perhubungan mencatat 4.963 penumpang di bandara ini pada 2025.</p>",
                                "x_location": 69.71,
                                "y_location": 67.11,
                                "media": "storage/transportation/bandar-udara-gusti-sjamsir-alam-33a47109-8674-4e1e-bee4-2bbbf697e63b.mp4",
                                "source_media": "Yotube Bandar Udara Gusti Sjamsir Alam",
                                "is_active": true,
                                "created_at": "2026-10-02 19:12:59",
                                "updated_at": "2026-10-03 01:23:32"
                            }
                        }
                    ]
                },
                {
                    "legacy_id": 2,
                    "attributes": {
                        "name": "Pelabuhan",
                        "color": "#0f766e",
                        "description": "Pelabuhan laut, sungai, dan penyeberangan di Kalimantan Selatan.",
                        "background": "storage/transportation/categories/KZeTynJGS4dVVxd8uJj5RkJpQgtN37CSVikgIa0n.jpg",
                        "sort_order": 2,
                        "created_at": "2026-10-02 19:12:57",
                        "updated_at": "2026-10-05 02:38:49",
                        "icon": "life-ring"
                    },
                    "locations": [
                        {
                            "legacy_id": 3,
                            "attributes": {
                                "name": "Pelabuhan Trisakti",
                                "address": "Jl. Barito Hilir Trisakti No. 6, Telaga Biru, Kecamatan Banjarmasin Barat, Kota Banjarmasin, Kalimantan Selatan 70119",
                                "description": "<p>Pelabuhan Trisakti berada di Jalan Barito Hilir, Banjarmasin Barat, di kawasan muara Sungai Barito. Kawasan pelabuhan yang dikelola Pelindo ini melayani pergerakan penumpang, kendaraan, dan barang di Banjarmasin.</p><h3>Layanan di kawasan Trisakti</h3><ul><li><strong>Terminal penumpang:</strong> tempat keberangkatan dan kedatangan penumpang kapal laut.</li><li><strong>Terminal peti kemas:</strong> mendukung bongkar muat dan distribusi barang melalui Pelabuhan Banjarmasin.</li><li><strong>Terminal Ro-Ro:</strong> menangani kendaraan yang diangkut dengan kapal.</li></ul><p>Kementerian Perhubungan juga mencatat keberadaan Vessel Traffic Service (VTS) di kompleks Trisakti untuk memantau lalu lintas kapal. Alamat kantor Pelindo Pelabuhan Banjarmasin berada di Jalan Barito Hilir Trisakti No. 6; telepon (0511) 3365866.</p>",
                                "x_location": 17.36,
                                "y_location": 68.09,
                                "media": "storage/transportation/pelabuhan-trisakti-cd59db03-31be-4e22-9e20-b98afa0e6255.mp4",
                                "source_media": "Youtube Dimensity Project",
                                "is_active": true,
                                "created_at": "2026-10-02 19:12:58",
                                "updated_at": "2026-10-03 01:19:03"
                            }
                        },
                        {
                            "legacy_id": 4,
                            "attributes": {
                                "name": "Pelabuhan Penyeberangan Batulicin",
                                "address": "Jl. Pelabuhan Ferry Batulicin, Kecamatan Batulicin, Kabupaten Tanah Bumbu, Kalimantan Selatan 72273",
                                "description": "<p>Pelabuhan Penyeberangan Batulicin berada di Jalan Pelabuhan Ferry Batulicin, Kabupaten Tanah Bumbu. Dari sini feri menyeberang menuju Tanjung Serdang di Pulau Laut, Kabupaten Kotabaru, membawa penumpang, kendaraan, dan barang.</p><h3>Lintasan dan layanan</h3><ul><li>Batulicin–Tanjung Serdang tercatat sebagai lintasan komersial yang dilayani ASDP.</li><li>Pelabuhan menyediakan terminal penumpang dan area parkir kendaraan.</li><li>Reservasi tiket melalui Ferizy dan pembayaran non-tunai tersedia untuk pengguna lintasan ini.</li></ul><h3>Prasarana pelabuhan</h3><p>ASDP mencatat penataan pagar dermaga, jalan keluar, fender, trestle, gerbang masuk, dan perkerasan area pelabuhan untuk mendukung pergerakan kapal serta kendaraan.</p>",
                                "x_location": 63.46,
                                "y_location": 68.67,
                                "media": "storage/transportation/pelabuhan-penyeberangan-batulicin-f99fc06a-bdf3-48c7-9041-a451d01e52b3.mp4",
                                "source_media": "Youtube Museum Maritim Indonesia",
                                "is_active": true,
                                "created_at": "2026-10-02 19:12:58",
                                "updated_at": "2026-10-03 02:15:28"
                            }
                        },
                        {
                            "legacy_id": 12,
                            "attributes": {
                                "name": "Pelabuhan Penyeberangan Tanjung Serdang",
                                "address": "Tanjung Serdang, Kecamatan Pulau Laut Tengah, Kabupaten Kotabaru, Kalimantan Selatan",
                                "description": "<p>Pelabuhan Penyeberangan Tanjung Serdang berada di Kecamatan Pulau Laut Tengah, Kabupaten Kotabaru. Pelabuhan ini menjadi pintu penyeberangan Pulau Laut menuju Batulicin di Kabupaten Tanah Bumbu.</p><h3>Layanan penyeberangan</h3><ul><li>Feri pada lintasan komersial Batulicin–Tanjung Serdang mengangkut penumpang, kendaraan, dan barang antarkabupaten.</li><li>Reservasi tiket melalui Ferizy dan pembayaran non-tunai tersedia untuk pengguna lintasan ini.</li></ul><h3>Fasilitas dan pengawasan</h3><p>Kawasan pelabuhan memiliki ruang tunggu penumpang. Dinas Perhubungan Provinsi Kalimantan Selatan memasukkan dermaga ini dalam pengawasan rutin keamanan, fasilitas, dan alat keselamatan penyeberangan.</p>",
                                "x_location": 65.41,
                                "y_location": 74.77,
                                "media": "storage/transportation/pelabuhan-penyeberangan-tanjung-serdang-00962669-76a3-42df-be94-80b0cda642d7.mp4",
                                "source_media": "Youtube Elang X",
                                "is_active": true,
                                "created_at": "2026-10-02 19:12:58",
                                "updated_at": "2026-10-03 02:42:18"
                            }
                        }
                    ]
                },
                {
                    "legacy_id": 3,
                    "attributes": {
                        "name": "Terminal",
                        "color": "#b7791f",
                        "description": "Terminal dan layanan angkutan darat di Kalimantan Selatan.",
                        "background": "storage/transportation/categories/V4sfIjSTF5Rs5MbUcP6XLQwXNZRcSCz16NQCkxsJ.webp",
                        "sort_order": 3,
                        "created_at": "2026-10-02 19:12:57",
                        "updated_at": "2026-10-05 02:20:17",
                        "icon": "building"
                    },
                    "locations": [
                        {
                            "legacy_id": 2,
                            "attributes": {
                                "name": "Terminal Tipe A Gambut Barakat",
                                "address": "Jl. A. Yani KM 17, Malintang, Kecamatan Gambut, Kabupaten Banjar, Kalimantan Selatan 70652",
                                "description": "<p>Terminal Tipe A Gambut Barakat berada di Jalan A. Yani KM 17, Malintang, Kecamatan Gambut, Kabupaten Banjar. Terminal induk ini merupakan satu-satunya terminal tipe A di Kalimantan Selatan dan melayani AKAP, AKDP, angkutan perkotaan, angkutan perdesaan, serta BRT/BTS Trans Banjarbakula.</p><h3>Trayek bus antarkota</h3><ul><li><strong>AKAP:</strong> Buntok, Pangkalan Bun, Muara Teweh, Balikpapan, dan Samarinda.</li><li><strong>AKDP:</strong> Banjarmasin, Banjarbaru, Bati-Bati, Handil Bakti, Sengayam, dan Loksado.</li></ul><h3>Trans Banjarbakula dan DAMRI</h3><ul><li><strong>Trans Banjarbakula:</strong> koridor 1A Gambut Barakat–Martapura, koridor 1B Gambut Barakat–Siring KM 0, dan koridor 4 Gambut Barakat–Terminal Soemarsono, Pelaihari.</li><li><strong>DAMRI perintis:</strong> rute Gambut Barakat–Loksado dan Gambut Barakat–Marabahan.</li><li><strong>DAMRI reguler:</strong> layanan bus DAMRI juga beroperasi dari terminal ini.</li></ul><h3>Fasilitas</h3><ul><li>Jalur keberangkatan dan kedatangan bus.</li><li>Ruang tunggu penumpang dan area parkir untuk penjemput atau pengantar.</li><li>Loket tiket, pusat informasi, dan media informasi.</li><li>Toilet umum, gedung kantor terminal, dan gudang.</li><li>Klinik, fasilitas keamanan, gerai ATM, area parkir bus, kios atau kantin, masjid, Wi-Fi, dan tempat bermain anak.</li></ul>",
                                "x_location": 21.46,
                                "y_location": 70.82,
                                "media": "storage/transportation/terminal-tipe-a-gambut-barakat-8e6ffa95-e51f-40bd-a95c-76afa8393fbe.mp4",
                                "source_media": "Youtube Banjarmasin Post News Video",
                                "is_active": true,
                                "created_at": "2026-10-02 19:12:58",
                                "updated_at": "2026-10-03 01:51:53"
                            }
                        },
                        {
                            "legacy_id": 16,
                            "attributes": {
                                "name": "Terminal Mabuun",
                                "address": "Kelurahan Mabuun, Kecamatan Murung Pudak, Kabupaten Tabalong, Kalimantan Selatan",
                                "description": "<p>Karena letaknya cukup strategis dan melayani jalur trans Kalimantan, terminal Mabu&#039;un jadi salah satu yang tersibuk. Terminal ini melayani perjalanan antar kabupaten dan antar provinsi, seperti keberangkatan menuju Balikpapan, Samarinda, dan wilayah perbatasan Kalimantan Tengah.</p><p></p><p>Alamat: Jalan Mabu&#039;un Raya, Kecamatan Murung Pudak, Kabupaten Tabalong, Kalimantan Selatan 71571.</p><p></p><p><strong>Rute:</strong></p><ul><li>Tanjung - Banjarmasin</li><li>Tanjung - Amuntai</li><li>Tanjung - Balikpapan</li><li>Tanjung - Samarinda</li></ul><p></p><p><strong>Jadwal keberangkatan:</strong></p><p></p><p>Bus AKDP dan travel mulai beroperasi sekitar 05.00 - 20.00 WITA, sementara sejumlah armada tujuan Kalimantan Timur tersedia pada siang hingga malam hari. Penumpang bisa membeli tiket langsung di loket dengan keberangkatan tercepat.</p>",
                                "x_location": 45,
                                "y_location": 28,
                                "media": "storage/transportation/terminal-mabuun-af3e8982-50ec-478b-93ff-cc8e4befb14b.mp4",
                                "source_media": "Website Metro Kalimantan",
                                "is_active": true,
                                "created_at": "2026-10-02 19:12:59",
                                "updated_at": "2026-10-04 19:11:12"
                            }
                        },
                        {
                            "legacy_id": 30,
                            "attributes": {
                                "name": "Terminal H. Soemarsono P.A.",
                                "address": "Desa Ambungan, Kecamatan Pelaihari, Kabupaten Tanah Laut, Kalimantan Selatan",
                                "description": "<p>Terminal H. Soemarsono P.A. berada di Desa Ambungan, Kecamatan Pelaihari, Kabupaten Tanah Laut. Terminal tipe C ini mempertemukan bus antarkawasan Banjarbakula dengan angkutan menuju desa dan tujuan wisata di Tanah Laut.</p><h3>Layanan bus</h3><ul><li><strong>Trans Banjarbakula koridor 4:</strong> menghubungkan Terminal Soemarsono dengan Terminal Tipe A Gambut Barakat.</li><li><strong>DAMRI perintis:</strong> melayani tiga tujuan dari Pelaihari, yaitu Desa Tabanio, kawasan Pantai Batakan, dan Kantor Kecamatan Jorong.</li><li><strong>LAKATAN:</strong> layanan pengumpan yang menghubungkan terminal dengan perjalanan lokal di Pelaihari.</li></ul><h3>Fasilitas terminal</h3><p>Inventarisasi lapangan mencatat jalur kedatangan dan keberangkatan, jalur pejalan kaki, area parkir, ruang tunggu, toilet, kantor administrasi, serta media informasi untuk penumpang.</p>",
                                "x_location": 22.64,
                                "y_location": 85.74,
                                "media": "storage/transportation/terminal-h-soemarsono-pa-ae349b9c-5545-41ac-bed6-a53068ca4093.mp4",
                                "source_media": "Youtube Lentera Kalimantan",
                                "is_active": true,
                                "created_at": "2026-10-02 19:37:51",
                                "updated_at": "2026-10-03 01:27:42"
                            }
                        },
                        {
                            "legacy_id": 32,
                            "attributes": {
                                "name": "Terminal Stagen",
                                "address": "Stagen, Kecamatan Pulau Laut Utara, Kabupaten Kotabaru, Kalimantan Selatan 72114",
                                "description": "<p>Terminal Stagen berada di Jalan Raya Stagen, Kecamatan Pulau Laut Utara, Kabupaten Kotabaru. Terminal tipe B ini menjadi titik pertemuan perjalanan dari wilayah utara dan selatan Pulau Laut. Kawasannya berdampingan dengan unit pengujian kendaraan bermotor.</p><h3>Jaringan angkutan</h3><ul><li>Angkutan antarkota dalam provinsi menuju Banjarmasin.</li><li>Angkutan perkotaan pada koridor Kotabaru–Stagen.</li><li>Angkutan perdesaan pada jaringan Gunung Ulin, Lontar, Megasari, Sambuluhan, Tanjung Lalak, Tanjung Pelayar, dan Tanjung Seloka.</li></ul><h3>Fasilitas terminal</h3><p>Kajian lapangan Politeknik Transportasi Darat Indonesia mencatat jalur kedatangan dan keberangkatan, ruang tunggu, loket, area parkir, serta kios. Luas kawasan terminal sekitar 29.400 meter persegi.</p>",
                                "x_location": 67.17,
                                "y_location": 68.52,
                                "media": "storage/transportation/terminal-stagen-87716ed2-095d-4299-8763-54b602fa9563.jpg",
                                "source_media": "Website Metro Kalimantan",
                                "is_active": true,
                                "created_at": "2026-10-02 19:37:51",
                                "updated_at": "2026-10-03 02:14:37"
                            }
                        },
                        {
                            "legacy_id": 35,
                            "attributes": {
                                "name": "Terminal Induk KM 6",
                                "address": "Jl. Pramuka, Kecamatan Banjarmasin Timur, Kota Banjarmasin, Kalimantan Selatan",
                                "description": "<p>Terminal Induk KM 6 berada di Jalan Pramuka, Kecamatan Banjarmasin Timur. Terminal tipe B ini dikelola UPTD Terminal Tipe B Dinas Perhubungan Provinsi Kalimantan Selatan dan menjadi simpul perpindahan angkutan umum di Banjarmasin.</p><h3>Koneksi angkutan</h3><ul><li><strong>Trans Banjarbakula koridor 3:</strong> menghubungkan Terminal Induk KM 6 dengan Anjir Muara di Kabupaten Barito Kuala.</li><li><strong>Trans Banjarmasin:</strong> menghubungkan terminal ini dengan Terminal Antasari di pusat kota.</li></ul><h3>Peran terminal</h3><p>Di kawasan terminal, penumpang dapat berpindah dari angkutan kota ke bus antarkawasan Banjarbakula. Pemerintah provinsi melakukan pemeliharaan gedung terminal untuk mendukung layanan BRT dan BTS; pelayanan bus AKAP dipusatkan di Terminal Tipe A Gambut Barakat.</p>",
                                "x_location": 12,
                                "y_location": 70,
                                "media": "storage/transportation/terminal-induk-km-6-97698b90-3341-434c-9002-f9db6bbe96a6.jpg",
                                "source_media": "Dinas Komunikasi dan Informatika, Media Center Kalimantan Selatan",
                                "is_active": true,
                                "created_at": "2026-10-02 20:24:55",
                                "updated_at": "2026-10-03 02:08:35"
                            }
                        },
                        {
                            "legacy_id": 36,
                            "attributes": {
                                "name": "Terminal Banua Lima",
                                "address": "Kecamatan Amuntai Tengah, Kabupaten Hulu Sungai Utara, Kalimantan Selatan",
                                "description": "<p><strong>Alamat</strong>: Jalan Brigjen H. Hasan Baseri No. 6, Kebun Sari, Kecamatan Amuntai Tengah, Kabupaten Hulu Sungai Utara, Kalimantan Selatan 71419.</p><p></p><p><strong>Rute:</strong></p><p></p><ul><li>Amuntai - Banjarmasin</li><li>Amuntai - Barabai</li><li>Amuntai - Tanjung</li><li>Amuntai - Balangan</li><li>Angkutan pedesaan di wilayah Hulu Sungai Utara</li></ul><p></p><p><strong>Jadwal keberangkatan:</strong></p><p></p><p>Bus dan minibus AKDP di terminal ini mulai beroperasi sejak pukul 05.30 - 17.00 WITA. Adapun jadwal keberangkatannya menyesuaikan jumlah penumpang atau menunggu sampai terisi penuh.</p><p></p><p>Terminal Banua Lima adalah terminal utama di Kabupaten Hulu Sungai Utara. Keberadaan terminal ini sangat penting karena berfungsi sebagai penghubung kawasan Banua Enam menuju Banjarmasin maupun kabupaten lain di bagian utara Kalimantan Selatan.</p>",
                                "x_location": 36.5,
                                "y_location": 39,
                                "media": "storage/transportation/terminal-banua-lima-5907e0f4-c8fe-401c-8028-c309022b8b6f.mp4",
                                "source_media": "Youtube Banjarmasin Post News Video",
                                "is_active": true,
                                "created_at": "2026-10-02 22:00:35",
                                "updated_at": "2026-10-03 02:18:10"
                            }
                        },
                        {
                            "legacy_id": 37,
                            "attributes": {
                                "name": "Terminal Bus Kandangan",
                                "address": "Jl. Brigjen H. M. Yusi, Gambah Luar Muka, Kecamatan Kandangan, Kabupaten Hulu Sungai Selatan, Kalimantan Selatan",
                                "description": "<p>Karena letaknya cukup strategis dan melayani jalur trans Kalimantan, terminal Mabu&#039;un jadi salah satu yang tersibuk. Terminal ini melayani perjalanan antar kabupaten dan antar provinsi, seperti keberangkatan menuju Balikpapan, Samarinda, dan wilayah perbatasan Kalimantan Tengah.</p><p></p><p>Alamat: Jalan Mabu&#039;un Raya, Kecamatan Murung Pudak, Kabupaten Tabalong, Kalimantan Selatan 71571.</p><p></p><p><strong>Rute</strong>:</p><ul><li>Tanjung - Banjarmasin</li><li>Tanjung - Amuntai</li><li>Tanjung - Balikpapan</li><li>Tanjung - Samarinda</li></ul><p></p><p><strong>Jadwal keberangkatan:</strong></p><p></p><p>Bus AKDP dan travel mulai beroperasi sekitar 05.00 - 20.00 WITA, sementara sejumlah armada tujuan Kalimantan Timur tersedia pada siang hingga malam hari. Penumpang bisa membeli tiket langsung di loket dengan keberangkatan tercepat.</p>",
                                "x_location": 39,
                                "y_location": 50,
                                "media": "storage/transportation/terminal-bus-kandangan-17efa741-10ae-48a4-821d-5eed388b4b3f.jpg",
                                "source_media": "Wikipedia",
                                "is_active": true,
                                "created_at": "2026-10-02 22:00:35",
                                "updated_at": "2026-10-03 02:12:06"
                            }
                        }
                    ]
                }
            ]
        }
    ]
}
KALSEL_DATA_JSON;
}
