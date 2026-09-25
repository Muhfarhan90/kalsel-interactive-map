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
            ['category' => 'Wisata Alam', 'location_name' => 'Pegunungan Meratus Loksado', 'location_address' => 'Kecamatan Loksado, Kabupaten Hulu Sungai Selatan', 'location_description' => 'Loksado berada di kawasan Pegunungan Meratus dan dikenal dengan lanskap hutan serta aliran Sungai Amandit. Salah satu pengalaman khasnya adalah balanting paring atau bamboo rafting, yaitu menyusuri sungai dengan rakit bambu yang dahulu digunakan masyarakat untuk membawa bambu dari hulu ke hilir. Kawasan ini juga menjadi tempat tinggal masyarakat Dayak Meratus, sehingga wisata alamnya beriringan dengan tradisi dan kehidupan budaya setempat.', 'coordinate_x' => 48, 'coordinate_y' => 47],
            ['category' => 'Wisata Alam', 'location_name' => 'Bukit Matang Kaladan', 'location_address' => 'Desa Tiwingan Lama, Kecamatan Aranio, Kabupaten Banjar', 'location_description' => 'Bukit Matang Kaladan berada di Desa Tiwingan Lama, Kecamatan Aranio. Dari puncaknya, pengunjung dapat melihat gugusan pulau kecil di Waduk Riam Kanan dan bentang perairan yang dikelilingi perbukitan. Perjalanan menuju puncak dilakukan melalui jalur pendakian; kawasan ini menarik untuk menikmati panorama, berfoto, serta melihat perubahan suasana waduk pada pagi atau sore hari.', 'coordinate_x' => 40, 'coordinate_y' => 31],
            ['category' => 'Budaya & Sejarah', 'location_name' => 'Pasar Terapung Lok Baintan', 'location_address' => 'Desa Lok Baintan, Kecamatan Sungai Tabuk, Kabupaten Banjar', 'location_description' => 'Pasar Terapung Lok Baintan merupakan pasar tradisional di aliran Sungai Martapura, tempat pedagang dari kampung-kampung sekitar menjajakan hasil kebun, pertanian, dan produk rumah tangga dari atas jukung atau perahu tradisional tanpa mesin. Aktivitas pasar berlangsung pada pagi hari dan mencerminkan hubungan erat masyarakat Banjar dengan sungai sebagai jalur transportasi serta ruang perdagangan. Pengunjung dapat menyaksikan transaksi dari tepian atau menyusuri kawasan dengan perahu setempat.', 'coordinate_x' => 25, 'coordinate_y' => 23],
            ['category' => 'Budaya & Sejarah', 'location_name' => 'Museum Lambung Mangkurat', 'location_address' => 'Jalan Ahmad Yani Km 36, Kota Banjarbaru', 'location_description' => 'Museum Lambung Mangkurat menyajikan sejarah dan kebudayaan Kalimantan Selatan di gedung yang mengadopsi bentuk rumah adat Banjar. Koleksinya mencakup miniatur rumah dan sampan, pakaian adat, kerajinan, alat kesenian, serta diorama yang menggambarkan kehidupan masyarakat. Museum ini juga menyimpan benda-benda bersejarah dari masa Hindu dan Kerajaan Banjar, termasuk replika pusaka kerajaan, sehingga pengunjung dapat mengenal beragam lapisan sejarah Banua.', 'coordinate_x' => 30, 'coordinate_y' => 28],
            ['category' => 'Wisata Religi', 'location_name' => 'Masjid Sultan Suriansyah', 'location_address' => 'Jalan Kuin Utara, Kelurahan Kuin Utara, Kecamatan Banjarmasin Utara, Kota Banjarmasin', 'location_description' => 'Masjid Sultan Suriansyah, yang juga dikenal sebagai Masjid Kuin, merupakan masjid bersejarah di tepi Sungai Kuin. Masjid ini dibangun pada masa pemerintahan Sultan Suriansyah, raja Banjar pertama yang memeluk Islam, sekitar abad ke-16. Ciri arsitekturnya antara lain penggunaan kayu ulin dan bentuk atap tumpang yang mempertahankan karakter bangunan tradisional Banjar. Letaknya berdekatan dengan kompleks makam Sultan Suriansyah dan menjadi bagian penting dari kawasan sejarah Kuin.', 'coordinate_x' => 18, 'coordinate_y' => 28],
            ['category' => 'Wisata Religi', 'location_name' => 'Makam Datu Kalampayan', 'location_address' => 'Desa Kalampayan Tengah, Kecamatan Astambul, Kabupaten Banjar', 'location_description' => 'Kompleks ini merupakan tempat peristirahatan Syekh Muhammad Arsyad al-Banjari, ulama Banjar yang dikenal luas sebagai Datu Kalampayan. Setelah menuntut ilmu di Makkah, beliau kembali ke Banjar untuk mengajarkan agama dan mendirikan pusat pendidikan di Dalam Pagar; karya fikihnya, Sabilal Muhtadin, juga dikenal di kawasan Asia Tenggara. Makam di Kalampayan Tengah menjadi tujuan ziarah dari berbagai daerah sekaligus pengingat kontribusinya bagi perkembangan ilmu dan pendidikan Islam di Kalimantan Selatan.', 'coordinate_x' => 34, 'coordinate_y' => 24],
            ['category' => 'Wisata Bahari', 'location_name' => 'Pantai Angsana', 'location_address' => 'Desa Angsana, Kecamatan Angsana, Kabupaten Tanah Bumbu', 'location_description' => 'Pantai Angsana berada di Desa Angsana, Kabupaten Tanah Bumbu, dan dikenal dengan garis pantai berpasir serta kawasan terumbu karang di perairan sekitarnya. Selain menikmati suasana pantai dan matahari terbenam, pengunjung datang untuk kegiatan bahari seperti snorkeling, menyelam, dan memancing. Sejumlah titik terumbu karang berada di lepas pantai dan dicapai menggunakan perahu; kondisi laut dan layanan setempat dapat berubah mengikuti cuaca serta pengelolaan kawasan.', 'coordinate_x' => 64, 'coordinate_y' => 75],
            ['category' => 'Wisata Bahari', 'location_name' => 'Pulau Samber Gelap', 'location_address' => 'Pulau Sebuku, Kabupaten Kotabaru', 'location_description' => 'Pulau Samber Gelap berada di wilayah Pulau Sebuku, Kabupaten Kotabaru, dan tercatat sebagai destinasi bahari dengan potensi terumbu karang serta habitat penyu. Daya tarik utamanya adalah lingkungan pesisir dan kehidupan laut di sekitarnya, yang membuat kawasan ini bernilai untuk wisata alam sekaligus pelestarian. Saat berkunjung, pengunjung perlu menjaga kebersihan pantai dan tidak mengganggu penyu maupun ekosistem terumbu karang.', 'coordinate_x' => 79, 'coordinate_y' => 82],
        ];

        foreach ($locations as $data) {
            $category = $categories[$data['category']];
            unset($data['category']);

            TourismLocation::updateOrCreate(
                ['location_name' => $data['location_name']],
                array_merge($data, [
                    'category_id' => $category->id,
                    'map_id' => $map->id,
                    'location_source_media' => null,
                    'is_active' => true,
                ]),
            );
        }
    }
}
