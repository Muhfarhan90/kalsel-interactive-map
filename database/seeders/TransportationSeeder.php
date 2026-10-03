<?php

namespace Database\Seeders;

use App\Models\Map;
use App\Models\TransportationCategory;
use App\Models\TransportationLocation;
use App\Models\TransportationMap;
use Illuminate\Database\Seeder;

class TransportationSeeder extends Seeder
{
    public function run(): void
    {
        $sharedMap = Map::shared();
        $map = TransportationMap::query()->first();

        if (!$map) {
            $map = TransportationMap::create([
                'map_id' => $sharedMap->id,
                'map_title' => 'Peta Transportasi Kalimantan Selatan',
                'map_sub_title' => 'Interactive Map Guidance',
                'map_logo' => 'images/logo/logo_kalsel.svg',
            ]);
        } elseif ($map->map_id !== $sharedMap->id) {
            $map->update(['map_id' => $sharedMap->id]);
        }

        foreach ([
            ['Bandara', '#1f5da8', 'paper-airplane', 'Bandara dan fasilitas penerbangan di Kalimantan Selatan.'],
            ['Pelabuhan', '#0f766e', 'lifebuoy', 'Pelabuhan laut, sungai, dan penyeberangan di Kalimantan Selatan.'],
            ['Terminal', '#b7791f', 'building-office', 'Terminal dan layanan angkutan darat di Kalimantan Selatan.'],
        ] as [$name, $color, $icon, $description]) {
            TransportationCategory::firstOrCreate(
                ['category_name' => $name],
                [
                    'category_color' => $color,
                    'category_icon' => $icon,
                    'category_description' => $description,
                ],
            );
        }

        TransportationCategory::query()
            ->where('category_name', 'Stasiun')
            ->whereDoesntHave('transportation_locations')
            ->delete();

        // Keluarkan simpul lokal atau lokasi lama tanpa dukungan data operasional terkini.
        TransportationLocation::query()
            ->whereIn('location_name', [
                'Terminal Paringin', 'Terminal Martapura', 'Dermaga Riam Kanan', 'Terminal Barabai',
                'Terminal Amuntai', 'Terminal Pasar Batuah', 'Terminal KM 6 Kayuh Baimbai',
                'Dermaga Lok Baintan', 'Dermaga Aluh-Aluh', 'Pelabuhan Kintap', 'Bandar Udara Warukin',
                'Simpul Angkutan Handil Bakti', 'Terminal Marabahan', 'Terminal Rantau', 'Dermaga Nagara',
                'Dermaga Bajayau', 'Terminal Pasar Keramat', 'Dermaga Danau Panggang',
                'Terminal Barang Haur Batu', 'Terminal Pasar Batu Mandi',
                'Pelabuhan Penyeberangan Banjar Raya', 'Pelabuhan Danau Aranio',
            ])
            ->delete();

        $categories = TransportationCategory::query()
            ->whereIn('category_name', ['Bandara', 'Pelabuhan', 'Terminal'])
            ->get()
            ->keyBy('category_name');

        foreach ([
            [
                'category' => 'Bandara',
                'location_name' => 'Bandara Internasional Syamsudin Noor',
                'location_address' => 'Jl. Angkasa, Kelurahan Landasan Ulin Timur, Kecamatan Landasan Ulin, Kota Banjarbaru, Kalimantan Selatan',
                'location_description' => '<p>Bandar Udara Internasional Syamsudin Noor (IATA: BDJ; ICAO: WAOO) berada di Landasan Ulin Timur, Kota Banjarbaru. Bandara yang dikelola PT Angkasa Pura Indonesia ini menjadi gerbang penerbangan utama Kalimantan Selatan.</p><h3>Rute penerbangan</h3><ul><li><strong>Antarkota:</strong> terhubung langsung dengan Jakarta, Surabaya, Semarang, Balikpapan, Denpasar, Makassar, Yogyakarta, dan Pontianak.</li><li><strong>Dalam Kalimantan Selatan:</strong> penerbangan menuju Bandar Udara Bersujud di Batulicin dan Bandar Udara Gusti Sjamsir Alam di Kotabaru.</li></ul><h3>Fasilitas penumpang</h3><ul><li>Terminal domestik seluas 77.569 meter persegi dengan restoran, pujasera, kedai kopi, layanan perbankan, dan galeri pengantar.</li><li>Landasan pacu berukuran 2.500 × 45 meter untuk kegiatan penerbangan.</li></ul><p>Data Direktorat Jenderal Perhubungan Udara mencatat 1.542.321 penumpang melalui bandara ini pada 2025.</p>',
                'coordinate_x' => 24.0,
                'coordinate_y' => 71.5,
            ],
            [
                'category' => 'Terminal',
                'location_name' => 'Terminal Tipe A Gambut Barakat',
                'location_address' => 'Jl. A. Yani KM 17, Malintang, Kecamatan Gambut, Kabupaten Banjar, Kalimantan Selatan 70652',
                'location_description' => '<p>Terminal Tipe A Gambut Barakat berada di Jalan A. Yani KM 17, Malintang, Kecamatan Gambut, Kabupaten Banjar. Terminal induk ini merupakan satu-satunya terminal tipe A di Kalimantan Selatan dan melayani AKAP, AKDP, angkutan perkotaan, angkutan perdesaan, serta BRT/BTS Trans Banjarbakula.</p><h3>Trayek bus antarkota</h3><ul><li><strong>AKAP:</strong> Buntok, Pangkalan Bun, Muara Teweh, Balikpapan, dan Samarinda.</li><li><strong>AKDP:</strong> Banjarmasin, Banjarbaru, Bati-Bati, Handil Bakti, Sengayam, dan Loksado.</li></ul><h3>Trans Banjarbakula dan DAMRI</h3><ul><li><strong>Trans Banjarbakula:</strong> koridor 1A Gambut Barakat–Martapura, koridor 1B Gambut Barakat–Siring KM 0, dan koridor 4 Gambut Barakat–Terminal Soemarsono, Pelaihari.</li><li><strong>DAMRI perintis:</strong> rute Gambut Barakat–Loksado dan Gambut Barakat–Marabahan.</li><li><strong>DAMRI reguler:</strong> layanan bus DAMRI juga beroperasi dari terminal ini.</li></ul><h3>Fasilitas</h3><ul><li>Jalur keberangkatan dan kedatangan bus.</li><li>Ruang tunggu penumpang dan area parkir untuk penjemput atau pengantar.</li><li>Loket tiket, pusat informasi, dan media informasi.</li><li>Toilet umum, gedung kantor terminal, dan gudang.</li><li>Klinik, fasilitas keamanan, gerai ATM, area parkir bus, kios atau kantin, masjid, Wi-Fi, dan tempat bermain anak.</li></ul>',
                'coordinate_x' => 20.5,
                'coordinate_y' => 68.0,
            ],
            [
                'category' => 'Pelabuhan',
                'location_name' => 'Pelabuhan Trisakti',
                'location_address' => 'Jl. Barito Hilir Trisakti No. 6, Telaga Biru, Kecamatan Banjarmasin Barat, Kota Banjarmasin, Kalimantan Selatan 70119',
                'location_description' => '<p>Pelabuhan Trisakti berada di Jalan Barito Hilir, Banjarmasin Barat, di kawasan muara Sungai Barito. Kawasan pelabuhan yang dikelola Pelindo ini melayani pergerakan penumpang, kendaraan, dan barang di Banjarmasin.</p><h3>Layanan di kawasan Trisakti</h3><ul><li><strong>Terminal penumpang:</strong> tempat keberangkatan dan kedatangan penumpang kapal laut.</li><li><strong>Terminal peti kemas:</strong> mendukung bongkar muat dan distribusi barang melalui Pelabuhan Banjarmasin.</li><li><strong>Terminal Ro-Ro:</strong> menangani kendaraan yang diangkut dengan kapal.</li></ul><p>Kementerian Perhubungan juga mencatat keberadaan Vessel Traffic Service (VTS) di kompleks Trisakti untuk memantau lalu lintas kapal. Alamat kantor Pelindo Pelabuhan Banjarmasin berada di Jalan Barito Hilir Trisakti No. 6; telepon (0511) 3365866.</p>',
                'coordinate_x' => 16.0,
                'coordinate_y' => 70.0,
            ],
            [
                'category' => 'Pelabuhan',
                'location_name' => 'Pelabuhan Penyeberangan Batulicin',
                'location_address' => 'Jl. Pelabuhan Ferry Batulicin, Kecamatan Batulicin, Kabupaten Tanah Bumbu, Kalimantan Selatan 72273',
                'location_description' => '<p>Pelabuhan Penyeberangan Batulicin berada di Jalan Pelabuhan Ferry Batulicin, Kabupaten Tanah Bumbu. Dari sini feri menyeberang menuju Tanjung Serdang di Pulau Laut, Kabupaten Kotabaru, membawa penumpang, kendaraan, dan barang.</p><h3>Lintasan dan layanan</h3><ul><li>Batulicin–Tanjung Serdang tercatat sebagai lintasan komersial yang dilayani ASDP.</li><li>Pelabuhan menyediakan terminal penumpang dan area parkir kendaraan.</li><li>Reservasi tiket melalui Ferizy dan pembayaran non-tunai tersedia untuk pengguna lintasan ini.</li></ul><h3>Prasarana pelabuhan</h3><p>ASDP mencatat penataan pagar dermaga, jalan keluar, fender, trestle, gerbang masuk, dan perkerasan area pelabuhan untuk mendukung pergerakan kapal serta kendaraan.</p>',
                'coordinate_x' => 54.0,
                'coordinate_y' => 76.0,
            ],
            [
                'category' => 'Terminal',
                'location_name' => 'Terminal Induk KM 6',
                'location_address' => 'Jl. Pramuka, Kecamatan Banjarmasin Timur, Kota Banjarmasin, Kalimantan Selatan',
                'location_description' => '<p>Terminal Induk KM 6 berada di Jalan Pramuka, Kecamatan Banjarmasin Timur. Terminal tipe B ini dikelola UPTD Terminal Tipe B Dinas Perhubungan Provinsi Kalimantan Selatan dan menjadi simpul perpindahan angkutan umum di Banjarmasin.</p><h3>Koneksi angkutan</h3><ul><li><strong>Trans Banjarbakula koridor 3:</strong> menghubungkan Terminal Induk KM 6 dengan Anjir Muara di Kabupaten Barito Kuala.</li><li><strong>Trans Banjarmasin:</strong> menghubungkan terminal ini dengan Terminal Antasari di pusat kota.</li></ul><h3>Peran terminal</h3><p>Di kawasan terminal, penumpang dapat berpindah dari angkutan kota ke bus antarkawasan Banjarbakula. Pemerintah provinsi melakukan pemeliharaan gedung terminal untuk mendukung layanan BRT dan BTS; pelayanan bus AKAP dipusatkan di Terminal Tipe A Gambut Barakat.</p>',
                'coordinate_x' => 12.0,
                'coordinate_y' => 70.0,
            ],
            [
                'category' => 'Bandara',
                'location_name' => 'Bandar Udara Bersujud',
                'location_address' => 'Jl. Kodeco KM 2, Bersujud, Kecamatan Simpang Empat, Kabupaten Tanah Bumbu, Kalimantan Selatan',
                'location_description' => '<p>Bandar Udara Bersujud (IATA: BTW; ICAO: WAOC) berada di Jalan Kodeco KM 2, Kecamatan Simpang Empat, Kabupaten Tanah Bumbu. Bandara yang dikelola pemerintah daerah ini melayani penerbangan domestik untuk kawasan Batulicin.</p><h3>Koneksi penerbangan</h3><p>Direktorat Jenderal Perhubungan Udara mencatat rute antara Bersujud dan Bandar Udara Syamsudin Noor di Banjarbaru. Rute tersebut menghubungkan Tanah Bumbu dengan pusat penerbangan utama Kalimantan Selatan.</p><h3>Prasarana</h3><ul><li>Landasan pacu berukuran 1.800 × 45 meter.</li><li>Gedung terminal domestik seluas 760 meter persegi dengan kapasitas rancangan 30.100 penumpang per tahun.</li></ul><p>Sepanjang 2025, Kementerian Perhubungan mencatat 13.288 penumpang dan 334 pergerakan pesawat di bandara ini.</p>',
                'coordinate_x' => 49.0,
                'coordinate_y' => 73.0,
            ],
            [
                'category' => 'Pelabuhan',
                'location_name' => 'Pelabuhan Penyeberangan Tanjung Serdang',
                'location_address' => 'Tanjung Serdang, Kecamatan Pulau Laut Tengah, Kabupaten Kotabaru, Kalimantan Selatan',
                'location_description' => '<p>Pelabuhan Penyeberangan Tanjung Serdang berada di Kecamatan Pulau Laut Tengah, Kabupaten Kotabaru. Pelabuhan ini menjadi pintu penyeberangan Pulau Laut menuju Batulicin di Kabupaten Tanah Bumbu.</p><h3>Layanan penyeberangan</h3><ul><li>Feri pada lintasan komersial Batulicin–Tanjung Serdang mengangkut penumpang, kendaraan, dan barang antarkabupaten.</li><li>Reservasi tiket melalui Ferizy dan pembayaran non-tunai tersedia untuk pengguna lintasan ini.</li></ul><h3>Fasilitas dan pengawasan</h3><p>Kawasan pelabuhan memiliki ruang tunggu penumpang. Dinas Perhubungan Provinsi Kalimantan Selatan memasukkan dermaga ini dalam pengawasan rutin keamanan, fasilitas, dan alat keselamatan penyeberangan.</p>',
                'coordinate_x' => 62.0,
                'coordinate_y' => 76.0,
            ],
            [
                'category' => 'Bandara',
                'location_name' => 'Bandar Udara Gusti Sjamsir Alam',
                'location_address' => 'Jl. Raya Stagen KM 10, Desa Stagen, Kecamatan Pulau Laut Utara, Kabupaten Kotabaru, Kalimantan Selatan 72114',
                'location_description' => '<p>Bandar Udara Gusti Sjamsir Alam (IATA: KBU; ICAO: WAOK) berada di Stagen, Kecamatan Pulau Laut Utara, Kabupaten Kotabaru. Bandara domestik yang dikelola UPT Direktorat Jenderal Perhubungan Udara ini menjadi pintu udara kawasan Pulau Laut.</p><h3>Rute penerbangan</h3><ul><li>Rute ke Bandar Udara Syamsudin Noor menghubungkan Kotabaru dengan Banjarbaru.</li><li>Direktorat Jenderal Perhubungan Udara juga mencatat koneksi domestik menuju Bandar Udara Sultan Hasanuddin, Makassar.</li></ul><h3>Fasilitas dan prasarana</h3><ul><li>Terminal domestik seluas sekitar 1.692 meter persegi dan landasan pacu berukuran 1.650 × 30 meter.</li><li>Kantin, musala, ruang menyusui, taman bermain, ruang tunggu CIP, toilet umum, dan toilet aksesibel.</li></ul><p>Data Kementerian Perhubungan mencatat 4.963 penumpang di bandara ini pada 2025.</p>',
                'coordinate_x' => 68.0,
                'coordinate_y' => 73.0,
            ],
            [
                'category' => 'Terminal',
                'location_name' => 'Terminal H. Soemarsono P.A.',
                'location_address' => 'Desa Ambungan, Kecamatan Pelaihari, Kabupaten Tanah Laut, Kalimantan Selatan',
                'location_description' => '<p>Terminal H. Soemarsono P.A. berada di Desa Ambungan, Kecamatan Pelaihari, Kabupaten Tanah Laut. Terminal tipe C ini mempertemukan bus antarkawasan Banjarbakula dengan angkutan menuju desa dan tujuan wisata di Tanah Laut.</p><h3>Layanan bus</h3><ul><li><strong>Trans Banjarbakula koridor 4:</strong> menghubungkan Terminal Soemarsono dengan Terminal Tipe A Gambut Barakat.</li><li><strong>DAMRI perintis:</strong> melayani tiga tujuan dari Pelaihari, yaitu Desa Tabanio, kawasan Pantai Batakan, dan Kantor Kecamatan Jorong.</li><li><strong>LAKATAN:</strong> layanan pengumpan yang menghubungkan terminal dengan perjalanan lokal di Pelaihari.</li></ul><h3>Fasilitas terminal</h3><p>Inventarisasi lapangan mencatat jalur kedatangan dan keberangkatan, jalur pejalan kaki, area parkir, ruang tunggu, toilet, kantor administrasi, serta media informasi untuk penumpang.</p>',
                'coordinate_x' => 37.0,
                'coordinate_y' => 77.0,
            ],
            [
                'category' => 'Terminal',
                'location_name' => 'Terminal Stagen',
                'location_address' => 'Stagen, Kecamatan Pulau Laut Utara, Kabupaten Kotabaru, Kalimantan Selatan 72114',
                'location_description' => '<p>Terminal Stagen berada di Kecamatan Pulau Laut Utara, Kabupaten Kotabaru. Pemerintah Provinsi Kalimantan Selatan mencatatnya sebagai terminal tipe B di Pulau Laut. Jaringan terminal provinsi menghubungkan Kotabaru dengan Tanah Bumbu, Tanah Laut, dan Banjarmasin.</p><h3>Rute Trans Saijaan</h3><ul><li><strong>Terminal Stagen–Pelabuhan Panjang Kotabaru:</strong> menghubungkan terminal dengan kawasan pelabuhan penumpang. Dinas Perhubungan mencantumkan layanan rute ini pada hari kerja.</li><li><strong>Terminal Stagen–Siring Laut:</strong> menghubungkan terminal dengan kawasan wisata tepi laut Kotabaru. Rute ini tercantum sebagai layanan akhir pekan.</li></ul><p>Terminal juga dipakai Dinas Perhubungan Kotabaru untuk pemeriksaan kelaikan bus, termasuk pemeriksaan angkutan menjelang masa perjalanan Lebaran.</p>',
                'coordinate_x' => 68.0,
                'coordinate_y' => 76.0,
            ],
            [
                'category' => 'Terminal',
                'location_name' => 'Terminal Banua Lima',
                'location_address' => 'Kecamatan Amuntai Tengah, Kabupaten Hulu Sungai Utara, Kalimantan Selatan',
                'location_description' => '<p>Terminal Banua Lima berada di Amuntai Tengah dan menjadi terminal penumpang tipe B di Kabupaten Hulu Sungai Utara. Terminal ini dikelola Pemerintah Provinsi Kalimantan Selatan sebagai simpul angkutan antarkota dari kawasan Amuntai.</p><h3>Koneksi antardaerah</h3><p>Jaringan transportasi provinsi menempatkan terminal Hulu Sungai Utara pada koridor menuju Tanjung, Barabai, Kandangan, Rantau, Martapura, dan Banjarmasin. Koridor tersebut menghubungkan Amuntai dengan pusat kegiatan di kawasan Hulu Sungai dan Banjarbakula.</p><h3>Lokasi</h3><p>Terminal berada di Kecamatan Amuntai Tengah, dekat kantor Dinas Perhubungan Kabupaten Hulu Sungai Utara. Dokumen perencanaan daerah mencatat kawasan terminal seluas 75 × 400 meter.</p>',
                'coordinate_x' => 36.5,
                'coordinate_y' => 39.0,
            ],
            [
                'category' => 'Terminal',
                'location_name' => 'Terminal Mabuun',
                'location_address' => 'Kelurahan Mabuun, Kecamatan Murung Pudak, Kabupaten Tabalong, Kalimantan Selatan',
                'location_description' => '<p>Terminal Mabuun berada di Kelurahan Mabuun, Kecamatan Murung Pudak, dekat pusat Kota Tanjung. Pemerintah Provinsi Kalimantan Selatan memasukkannya dalam layanan terminal tipe B yang dikelola UPTD Terminal Tipe B dan Trans Perkotaan.</p><h3>Jaringan angkutan</h3><p>Terminal ini menjadi simpul perjalanan darat di Kabupaten Tabalong. Jaringan trayek angkutan kota yang ditetapkan Pemerintah Kabupaten Tabalong mencantumkan rute dari Terminal Mabuun menuju Jangkung, Barunak, Padang Panjang, dan Kembang Kuning. Pada jaringan antarkota Kalimantan Selatan, Tabalong terhubung dengan Paringin, Barabai, Kandangan, Rantau, Martapura, dan Banjarmasin.</p><p>Terminal Mabuun juga digunakan sebagai titik keberangkatan angkutan mudik antardaerah dari Tabalong.</p>',
                'coordinate_x' => 45.0,
                'coordinate_y' => 28.0,
            ],
            [
                'category' => 'Terminal',
                'location_name' => 'Terminal Bus Kandangan',
                'location_address' => 'Jl. Brigjen H. M. Yusi, Gambah Luar Muka, Kecamatan Kandangan, Kabupaten Hulu Sungai Selatan, Kalimantan Selatan',
                'location_description' => '<p>Terminal Bus Kandangan berada di Jalan Brigjen H. M. Yusi, Desa Gambah Luar Muka, Kecamatan Kandangan. Terminal ini menjadi tempat perpindahan penumpang antara angkutan antarkota dan angkutan perkotaan atau perdesaan di Kabupaten Hulu Sungai Selatan.</p><h3>Layanan angkutan</h3><p>Kajian Politeknik Transportasi Darat Indonesia mencatat jalur kedatangan bus AKAP, jalur kedatangan dan keberangkatan AKDP, serta jalur keberangkatan angkutan kota dan desa di kawasan terminal. Pemerintah Provinsi Kalimantan Selatan juga memasukkan Terminal Kandangan dalam jaringan terminal penumpang provinsi.</p><h3>Fasilitas</h3><p>Tersedia tempat tunggu penumpang, toilet, musala, pertokoan dan kantin, tempat parkir, ruang menyusui, serta jalur masuk dan keluar kendaraan. Luas lahan terminal tercatat sekitar 5.445 meter persegi.</p>',
                'coordinate_x' => 39.0,
                'coordinate_y' => 50.0,
            ],
        ] as $data) {
            $category = $categories[$data['category']];
            unset($data['category']);

            TransportationLocation::updateOrCreate(
                ['location_name' => $data['location_name']],
                array_merge($data, [
                    'category_id' => $category->id,
                    'map_id' => $map->id,
                    'is_active' => true,
                ]),
            );
        }
    }
}
