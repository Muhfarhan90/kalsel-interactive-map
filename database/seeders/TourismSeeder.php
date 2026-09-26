<?php

namespace Database\Seeders;

use App\Models\TourismCategory;
use App\Models\TourismLocation;
use App\Models\TourismMap;
use Illuminate\Database\Seeder;

class TourismSeeder extends Seeder
{
    public function run(): void
    {
        $map = TourismMap::updateOrCreate(
            ['map_title' => 'Peta Wisata Kalimantan Selatan'],
            [
                'map_sub_title' => 'Interactive Map Guidance',
                'map_logo' => 'images/logo/logo_kalsel.svg',
                'map_image' => 'images/maps/peta_provinsi_kalsel.png',
                'map_description' => 'Peta utama lokasi wisata Kalimantan Selatan.',
                'header_title' => 'Kalimantan Selatan',
                'header_sub_title' => 'Interactive Map Guidance',
            ],
        );

        $categories = collect([
            ['category_name' => 'Wisata Alam', 'category_color' => '#2f7d57', 'category_icon' => 'globe-asia-australia', 'category_description' => 'Destinasi alam, pegunungan, dan bentang hijau.'],
            ['category_name' => 'Budaya & Sejarah', 'category_color' => '#c67a38', 'category_icon' => 'building-library', 'category_description' => 'Warisan budaya, sejarah, dan tradisi Banjar.'],
            ['category_name' => 'Wisata Religi', 'category_color' => '#7963a9', 'category_icon' => 'building-library', 'category_description' => 'Masjid, makam ulama, dan tujuan perjalanan religi.'],
            ['category_name' => 'Wisata Bahari', 'category_color' => '#287f9c', 'category_icon' => 'sun', 'category_description' => 'Pantai, pulau, dan wisata perairan.'],
        ])->mapWithKeys(function (array $data) {
            $category = TourismCategory::updateOrCreate(
                ['category_name' => $data['category_name']],
                $data,
            );

            return [$category->category_name => $category];
        });

        $locations = [
            [
                'category' => 'Wisata Alam',
                'location_name' => 'Pegunungan Meratus Loksado',
                'location_address' => 'Kecamatan Loksado, Kabupaten Hulu Sungai Selatan',
                'location_description' => '<h1>Pegunungan Meratus Loksado</h1>'.
                    '<p><strong>Loksado</strong> merupakan kawasan pegunungan di Hulu Sungai Selatan yang memadukan hutan tropis, aliran Sungai Amandit, dan kehidupan masyarakat Dayak Meratus. Suasananya sejuk dan cocok untuk pengunjung yang ingin menikmati wisata alam sekaligus mengenal budaya setempat.</p>'.
                    '<h2>Daya tarik utama</h2><ul><li><strong>Balanting paring</strong>, yaitu menyusuri Sungai Amandit menggunakan rakit bambu.</li><li>Jalur trekking menuju perbukitan, air terjun, dan permukiman adat.</li><li>Kerajinan, tradisi, serta hasil kebun masyarakat Dayak Meratus.</li></ul>'.
                    '<h3>Tips berkunjung</h3><ol><li>Periksa cuaca dan kondisi sungai sebelum mengikuti aktivitas air.</li><li>Gunakan alas kaki yang nyaman dan bawa perlindungan dari hujan.</li><li>Hormati aturan kampung serta mintalah izin sebelum memotret kegiatan warga.</li></ol>'.
                    '<p><em>Waktu dan rute kegiatan dapat berubah mengikuti kondisi alam.</em> <u>Selalu ikuti arahan pemandu lokal.</u></p>',
                'coordinate_x' => 46.95,
                'coordinate_y' => 51.23,
            ],
            [
                'category' => 'Wisata Alam',
                'location_name' => 'Air Terjun Lano',
                'location_address' => 'Desa Lano, Kecamatan Jaro, Kabupaten Tabalong',
                'location_description' => '<h1>Air Terjun Lano</h1>'.
                    '<p>Air Terjun Lano berada di kawasan hutan Desa Lano, Kecamatan Jaro, dekat perbatasan Kalimantan Selatan dan Kalimantan Timur. Perjalanan menuju air terjun menghadirkan suasana hutan yang masih alami, pepohonan besar, serta beberapa aliran sungai kecil.</p>'.
                    '<h2>Pengalaman yang ditawarkan</h2><ul><li>Berjalan kaki sekitar 800 meter dari akses utama menuju air terjun.</li><li>Menikmati udara sejuk dan suara air di tengah kawasan hutan.</li><li>Mengamati vegetasi serta bentang alam khas wilayah Tabalong bagian utara.</li></ul>'.
                    '<h3>Persiapan perjalanan</h3><ol><li>Gunakan sepatu dengan daya cengkeram baik.</li><li>Bawa air minum dan simpan kembali seluruh sampah.</li><li>Hindari mendekati arus deras ketika hujan atau debit air meningkat.</li></ol>'.
                    '<p><strong>Keselamatan adalah prioritas.</strong> <em>Kondisi jalur dapat berubah setelah hujan.</em></p>',
                'coordinate_x' => 52.09,
                'coordinate_y' => 17.79,
            ],
            [
                'category' => 'Budaya & Sejarah',
                'location_name' => 'Pasar Terapung Lok Baintan',
                'location_address' => 'Desa Lok Baintan, Kecamatan Sungai Tabuk, Kabupaten Banjar',
                'location_description' => '<h1>Pasar Terapung Lok Baintan</h1>'.
                    '<p>Pasar Terapung Lok Baintan adalah pasar tradisional di Sungai Martapura. Pedagang datang menggunakan <em>jukung</em> dan menawarkan hasil kebun, makanan, serta kebutuhan rumah tangga langsung dari atas perahu.</p>'.
                    '<h2>Nilai budaya</h2><ul><li>Memperlihatkan hubungan masyarakat Banjar dengan sungai sebagai ruang hidup.</li><li>Mempertahankan transaksi tradisional antarpedagang dan pembeli di atas perahu.</li><li>Menyajikan suasana permukiman tepian sungai pada pagi hari.</li></ul>'.
                    '<h3>Rencana kunjungan</h3><ol><li>Datang pada pagi hari ketika aktivitas pasar masih ramai.</li><li>Gunakan jasa perahu setempat dan kenakan pelampung.</li><li>Siapkan uang tunai pecahan kecil untuk berbelanja.</li></ol>'.
                    '<p><u>Jaga keseimbangan saat berada di jukung</u> dan hindari menghalangi jalur perahu pedagang.</p>',
                'coordinate_x' => 22.87,
                'coordinate_y' => 67.38,
            ],
            [
                'category' => 'Budaya & Sejarah',
                'location_name' => 'Situs Candi Agung',
                'location_address' => 'Desa Sungai Malang, Kecamatan Amuntai Tengah, Kabupaten Hulu Sungai Utara',
                'location_description' => '<h1>Situs Candi Agung</h1>'.
                    '<p>Situs Candi Agung di Amuntai berkaitan dengan sejarah <strong>Kerajaan Negara Dipa</strong> dan perjalanan awal terbentuknya Kerajaan Banjar. Kawasan ini menjadi ruang pembelajaran mengenai arkeologi, sejarah lokal, dan perkembangan masyarakat di wilayah Hulu Sungai.</p>'.
                    '<h2>Yang dapat dipelajari</h2><ul><li>Sisa struktur candi dan material bangunan yang ditemukan di kawasan situs.</li><li>Kisah Negara Dipa dalam sejarah dan tradisi tutur masyarakat Banjar.</li><li>Koleksi pendukung yang menjelaskan temuan arkeologi setempat.</li></ul>'.
                    '<h3>Etika mengunjungi situs</h3><ol><li>Ikuti jalur kunjungan dan petunjuk pengelola.</li><li>Jangan menyentuh, memindahkan, atau menaiki bagian situs.</li><li>Jaga ketenangan dan kebersihan kawasan bersejarah.</li></ol>'.
                    '<p><em>Kunjungan yang bertanggung jawab membantu menjaga peninggalan sejarah untuk generasi berikutnya.</em></p>',
                'coordinate_x' => 38.34,
                'coordinate_y' => 38.72,
            ],
            [
                'category' => 'Wisata Religi',
                'location_name' => 'Masjid Sultan Suriansyah',
                'location_address' => 'Jalan Kuin Utara, Kelurahan Kuin Utara, Kecamatan Banjarmasin Utara, Kota Banjarmasin',
                'location_description' => '<h1>Masjid Sultan Suriansyah</h1>'.
                    '<p>Masjid Sultan Suriansyah atau Masjid Kuin merupakan salah satu masjid bersejarah di Kalimantan Selatan. Masjid ini berada di kawasan tepian Sungai Kuin dan berkaitan dengan Sultan Suriansyah, raja Banjar pertama yang memeluk Islam.</p>'.
                    '<h2>Keunikan bangunan</h2><ul><li>Konstruksi yang banyak menggunakan kayu ulin.</li><li>Atap bertumpang yang mempertahankan karakter arsitektur tradisional Banjar.</li><li>Lingkungan bersejarah yang berdekatan dengan kompleks makam Sultan Suriansyah.</li></ul>'.
                    '<h3>Adab berkunjung</h3><ol><li>Kenakan pakaian sopan dan jaga ketenangan selama berada di masjid.</li><li>Hindari mengganggu pelaksanaan ibadah.</li><li>Mintalah izin sebelum mengambil foto di area tertentu.</li></ol>'.
                    '<p><strong>Masjid tetap berfungsi sebagai tempat ibadah.</strong> <u>Dahulukan kepentingan jemaah.</u></p>',
                'coordinate_x' => 17.49,
                'coordinate_y' => 67.60,
            ],
            [
                'category' => 'Wisata Religi',
                'location_name' => 'Makam Datu Sanggul',
                'location_address' => 'Desa Suato Tatakan, Kecamatan Tapin Selatan, Kabupaten Tapin',
                'location_description' => '<h1>Makam Datu Sanggul</h1>'.
                    '<p>Makam Datu Sanggul merupakan tujuan ziarah di Desa Suato Tatakan, Kecamatan Tapin Selatan. Datu Sanggul dikenal sebagai ulama dan tokoh masyarakat yang hidup pada abad ke-18 serta sezaman dengan Syekh Muhammad Arsyad al-Banjari.</p>'.
                    '<h2>Makna kunjungan</h2><ul><li>Mengenal perjalanan tokoh agama yang dihormati masyarakat Tapin.</li><li>Melihat tradisi ziarah yang tetap dijalankan oleh masyarakat.</li><li>Mengunjungi salah satu rangkaian destinasi religi di kawasan Tatakan.</li></ul>'.
                    '<h3>Adab ziarah</h3><ol><li>Berpakaian sopan dan berbicara dengan tenang.</li><li>Ikuti aturan serta arahan pengelola makam.</li><li>Jaga kebersihan dan hormati peziarah lain.</li></ol>'.
                    '<p><em>Kawasan ini adalah ruang ibadah dan refleksi.</em> <u>Hindari kegiatan yang mengganggu kekhusyukan.</u></p>',
                'coordinate_x' => 34.90,
                'coordinate_y' => 59.26,
            ],
            [
                'category' => 'Wisata Bahari',
                'location_name' => 'Pantai Angsana',
                'location_address' => 'Desa Angsana, Kecamatan Angsana, Kabupaten Tanah Bumbu',
                'location_description' => '<h1>Pantai Angsana</h1>'.
                    '<p>Pantai Angsana dikenal sebagai destinasi bahari di Kabupaten Tanah Bumbu. Garis pantai, suasana pesisir, dan kawasan terumbu karang di perairan sekitarnya menjadi daya tarik bagi pengunjung yang ingin menikmati kegiatan laut.</p>'.
                    '<h2>Aktivitas wisata</h2><ul><li>Bersantai di pantai dan menikmati matahari terbenam.</li><li>Snorkeling atau menyelam pada titik yang diizinkan.</li><li>Menuju kawasan terumbu karang menggunakan perahu dan pemandu setempat.</li></ul>'.
                    '<h3>Persiapan aktivitas laut</h3><ol><li>Periksa cuaca, gelombang, dan ketersediaan operator sebelum berangkat.</li><li>Gunakan pelampung serta perlengkapan sesuai standar.</li><li>Jangan menginjak, menyentuh, atau mengambil bagian dari terumbu karang.</li></ol>'.
                    '<p><strong>Kelestarian laut adalah tanggung jawab bersama.</strong> <em>Bawa kembali sampah dan gunakan produk yang ramah lingkungan.</em></p>',
                'coordinate_x' => 53.59,
                'coordinate_y' => 80.70,
            ],
            [
                'category' => 'Wisata Bahari',
                'location_name' => 'Pantai Batakan Baru',
                'location_address' => 'Jalan Pariwisata, Desa Batakan, Kecamatan Panyipatan, Kabupaten Tanah Laut',
                'location_description' => '<h1>Pantai Batakan Baru</h1>'.
                    '<p>Pantai Batakan Baru merupakan destinasi pesisir di Desa Batakan, Kecamatan Panyipatan. Kawasan ini memiliki garis pantai yang panjang, pepohonan rindang, serta panorama Pulau Datu yang dapat terlihat dari sekitar pantai.</p>'.
                    '<h2>Daya tarik pantai</h2><ul><li>Area rekreasi keluarga dengan ruang terbuka di sepanjang pesisir.</li><li>Pemandangan matahari terbit dan terbenam saat kondisi cuaca mendukung.</li><li>Aktivitas berkuda, bersantai, dan menikmati kuliner dari usaha setempat.</li></ul>'.
                    '<h3>Tips berkunjung</h3><ol><li>Perhatikan batas aman ketika bermain di dekat air.</li><li>Awasi anak-anak dan ikuti petunjuk pengelola kawasan.</li><li>Gunakan tempat sampah serta jaga kebersihan fasilitas umum.</li></ol>'.
                    '<p><u>Fasilitas dan aktivitas dapat berubah mengikuti kondisi kawasan.</u> <em>Konfirmasikan informasi terbaru sebelum berangkat.</em></p>',
                'coordinate_x' => 18.78,
                'coordinate_y' => 92.56,
            ],
        ];

        TourismLocation::where('location_name', 'Bukit Matang Kaladan')->update(['location_name' => 'Air Terjun Lano']);
        TourismLocation::where('location_name', 'Museum Lambung Mangkurat')->update(['location_name' => 'Situs Candi Agung']);
        TourismLocation::where('location_name', 'Makam Datu Kalampayan')->update(['location_name' => 'Makam Datu Sanggul']);
        TourismLocation::where('location_name', 'Pulau Samber Gelap')->update(['location_name' => 'Pantai Batakan Baru']);

        foreach ($locations as $data) {
            $category = $categories[$data['category']];
            unset($data['category']);

            TourismLocation::updateOrCreate(
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
