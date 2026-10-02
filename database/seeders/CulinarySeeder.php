<?php

namespace Database\Seeders;

use App\Models\CulinaryCategory;
use App\Models\CulinaryLocation;
use App\Models\CulinaryMap;
use App\Models\Map;
use Illuminate\Database\Seeder;

class CulinarySeeder extends Seeder
{
    public function run(): void
    {
        $sharedMap = Map::shared();
        $map = CulinaryMap::query()->latest('id')->first();

        if (!$map) {
            $map = CulinaryMap::create([
                'map_id' => $sharedMap->id,
                'map_title' => 'Peta Kuliner Kalimantan Selatan',
                'map_sub_title' => 'Interactive Map Guidance',
                'map_logo' => 'images/logo/logo_kalsel.svg',
            ]);
        } elseif ($map->map_id !== $sharedMap->id) {
            $map->update(['map_id' => $sharedMap->id]);
        }

        $categories = collect([
            ['category_name' => 'Makanan Khas', 'category_color' => '#da251d', 'category_icon' => 'fire', 'category_description' => 'Hidangan khas Banjar dan Kalimantan Selatan.'],
            ['category_name' => 'Olahan Ikan', 'category_color' => '#287f9c', 'category_icon' => 'beaker', 'category_description' => 'Hidangan berbahan ikan sungai dan hasil perairan Banua.'],
            ['category_name' => 'Kue & Wadai', 'category_color' => '#c67a38', 'category_icon' => 'cake', 'category_description' => 'Kue tradisional dan wadai khas Banua.'],
            ['category_name' => 'Camilan Banua', 'category_color' => '#7963a9', 'category_icon' => 'gift', 'category_description' => 'Camilan dan penganan khas Kalimantan Selatan.'],
        ])->mapWithKeys(function (array $data) {
            $category = CulinaryCategory::updateOrCreate(
                ['category_name' => $data['category_name']],
                $data,
            );

            return [$category->category_name => $category];
        });

        $legacyLocationNames = [
            'Soto Banjar' => 'Depot Soto Bang Amat',
            'Ketupat Kandangan' => 'Soto Banjar H. Anang Ayam Bapukah',
            'Dodol Kandangan' => 'Sentra Dodol Kandangan',
            'Ikan Bakar Banua Anyar' => 'Rumah Makan H. Fauzan',
            'Mie Habang' => 'Warung Mie Habang Beruntung',
            'Haruan Masak Habang' => 'Rumah Makan Mama Baiti',
            'Wadi Patin' => 'Lesehan Mega',
            'Bingka Banjar' => 'Bingka Haji Thamrin',
            'Wadai Cincin' => 'Warung Acil Imar',
            'Amparan Tatak' => 'Warung Isau Martapura',
            'Amplang Ikan Kotabaru' => 'Amplang Qita',
            'Mandai Goreng' => 'Amplang Maskot Saijaan 2',
        ];

        foreach ($legacyLocationNames as $oldName => $newName) {
            CulinaryLocation::query()
                ->where('location_name', $oldName)
                ->update(['location_name' => $newName]);
        }

        $locations = [
            [
                'category' => 'Makanan Khas',
                'location_name' => 'Depot Soto Bang Amat',
                'location_address' => 'Jl. Banua Anyar No. 6, Benua Anyar, Banjarmasin Timur, Kota Banjarmasin',
                'location_description' => <<<'HTML'
<h1>Depot Soto Bang Amat</h1>
<p>Depot Soto Bang Amat adalah tempat makan di tepi Sungai Martapura yang dikenal dengan soto Banjar berkaldu ayam kampung. Lokasinya berada di Jalan Banua Anyar No. 6, Banjarmasin Timur.</p>
<h2>Menu khas</h2>
<ul><li>Soto Banjar dengan kuah rempah dan ayam kampung.</li><li>Ketupat sebagai pendamping soto.</li><li>Lauk tambahan seperti bagian ayam tersedia untuk melengkapi hidangan.</li></ul>
<h3>Pengalaman berkunjung</h3>
<p>Tempat ini menawarkan suasana bersantap di tepian sungai. Soto biasanya ramai saat sarapan dan makan siang, jadi datang lebih awal dapat membuat kunjungan lebih nyaman.</p>
HTML,
                'location_source_media' => null,
                'coordinate_x' => 17.30,
                'coordinate_y' => 70.60,
            ],
            [
                'category' => 'Makanan Khas',
                'location_name' => 'Soto Banjar H. Anang Ayam Bapukah',
                'location_address' => 'Jl. A. Yani, Loktabat Selatan, Banjarbaru Selatan, Kota Banjarbaru',
                'location_description' => <<<'HTML'
<h1>Soto Banjar H. Anang</h1>
<p>Warung Soto Banjar H. Anang Ayam Bapukah berada di Jalan A. Yani, kawasan Loktabat Selatan, Kota Banjarbaru. Tempat ini menyajikan soto Banjar sebagai menu andalannya.</p>
<h2>Yang dapat dicoba</h2>
<ul><li>Soto Banjar dengan pilihan ayam bapukah.</li><li>Pelengkap soto seperti ketupat dan perkedel, sesuai ketersediaan.</li><li>Menu rumah makan Banjar untuk makan bersama keluarga.</li></ul>
<h3>Lokasi</h3>
<p>Titik peta menunjukkan kawasan Loktabat Selatan di sepanjang Jalan A. Yani. Konfirmasikan jam operasional sebelum berangkat karena informasi jam dapat berubah.</p>
HTML,
                'location_source_media' => null,
                'coordinate_x' => 23.80,
                'coordinate_y' => 73.50,
            ],
            [
                'category' => 'Makanan Khas',
                'location_name' => 'Warung Mie Habang Beruntung',
                'location_address' => 'Jl. Pemurus Dalam, Banjarmasin Selatan, Kota Banjarmasin',
                'location_description' => <<<'HTML'
<h1>Warung Mie Habang Beruntung</h1>
<p>Warung milik Khadijha ini menyajikan mie habang khas Banjar dengan rasa manis-gurih dan bumbu rempah. Lokasinya berada di Jalan Pemurus Dalam, Banjarmasin Selatan.</p>
<h2>Menu warung</h2>
<ul><li>Mie habang dengan pilihan pelengkap ayam atau telur bebek.</li><li>Ada pula ayam goreng yang banyak dipesan pelanggan.</li><li>Racikan mie merah khas Banjar yang dibuat dengan resep turun-temurun.</li></ul>
<h3>Catatan lokasi</h3>
<p>Warung ini pernah berpindah sekitar 500 meter dari lokasi sebelumnya. Pin menunjukkan kawasan Jalan Pemurus Dalam; sebaiknya hubungi penjual untuk memastikan titik terbaru sebelum berkunjung.</p>
HTML,
                'location_source_media' => null,
                'coordinate_x' => 17.50,
                'coordinate_y' => 72.60,
            ],
            [
                'category' => 'Olahan Ikan',
                'location_name' => 'Rumah Makan H. Fauzan',
                'location_address' => 'Jl. A. Yani Km. 40, Kertak Hanyar I, Kabupaten Banjar',
                'location_description' => <<<'HTML'
<h1>Rumah Makan H. Fauzan</h1>
<p>Rumah makan khas Banjar ini berada di koridor Jalan A. Yani Km. 40, Kabupaten Banjar. Menu ikan bakar dan ikan goreng menjadi daya tariknya, dengan suasana makan lesehan.</p>
<h2>Menu yang dikenal</h2>
<ul><li>Ikan bakar seperti patin, haruan, dan baung, mengikuti ketersediaan.</li><li>Ikan goreng dan ikan paisan berbumbu khas Banjar.</li><li>Lauk pendamping seperti ayam goreng, telur asin, dan sambal.</li></ul>
<h3>Menikmati hidangan</h3>
<p>Tanyakan jenis ikan yang tersedia hari itu sebelum memesan. Pin berada di sekitar alamat Km. 40 pada peta kabupaten.</p>
HTML,
                'location_source_media' => null,
                'coordinate_x' => 34.50,
                'coordinate_y' => 66.00,
            ],
            [
                'category' => 'Olahan Ikan',
                'location_name' => 'Rumah Makan Mama Baiti',
                'location_address' => 'Jl. A. Yani Km. 13,7, Gambut, Kabupaten Banjar',
                'location_description' => <<<'HTML'
<h1>Rumah Makan Mama Baiti</h1>
<p>Rumah Makan Mama Baiti berada di Jalan A. Yani Km. 13,7, kawasan Gambut. Rumah makan ini menyajikan masakan Banjar dengan pilihan lauk seperti haruan masak habang, itik, dan ayam.</p>
<h2>Menu khas Banjar</h2>
<ul><li>Haruan masak habang dengan bumbu merah.</li><li>Olahan itik dan ayam sebagai pilihan lauk.</li><li>Nasi hangat untuk menikmati masakan rumahan khas Banua.</li></ul>
<h3>Lokasi</h3>
<p>Lokasinya berada di jalur utama A. Yani antara Banjarmasin dan Martapura. Tanyakan ketersediaan lauk kepada rumah makan sebelum berkunjung.</p>
HTML,
                'location_source_media' => null,
                'coordinate_x' => 22.50,
                'coordinate_y' => 67.00,
            ],
            [
                'category' => 'Olahan Ikan',
                'location_name' => 'Lesehan Mega',
                'location_address' => 'Jalan Sungai Sipai, Martapura, Kabupaten Banjar',
                'location_description' => <<<'HTML'
<h1>Lesehan Mega</h1>
<p>Lesehan Mega adalah tempat makan di Jalan Sungai Sipai, Martapura. Menu yang ditawarkan mencakup ikan bakar dan ikan goreng, cocok untuk santap bersama di kawasan Kabupaten Banjar.</p>
<h2>Pilihan hidangan</h2>
<ul><li>Ikan bakar dengan nasi dan sambal.</li><li>Ikan goreng sebagai pilihan olahan ikan lainnya.</li><li>Menu pendamping mengikuti ketersediaan di tempat.</li></ul>
<h3>Saat berkunjung</h3>
<p>Tanyakan jenis ikan dan sambal yang tersedia saat itu. Pin menunjukkan kawasan Sungai Sipai di Martapura.</p>
HTML,
                'location_source_media' => null,
                'coordinate_x' => 35.70,
                'coordinate_y' => 63.00,
            ],
            [
                'category' => 'Kue & Wadai',
                'location_name' => 'Bingka Haji Thamrin',
                'location_address' => 'Komplek Merpati, kawasan Sultan Adam, Kota Banjarmasin',
                'location_description' => <<<'HTML'
<h1>Bingka Haji Thamrin</h1>
<p>Bingka Haji Thamrin adalah usaha kue Banjar di kawasan Komplek Merpati, Sultan Adam, Banjarmasin. Bingka dibuat langsung di tempat produksi dan dikenal sebagai kue manis-gurih yang telah lama menjadi favorit warga.</p>
<h2>Yang ditawarkan</h2>
<ul><li>Bingka dengan pilihan rasa yang dapat berubah mengikuti produksi.</li><li>Kue dijual langsung dari tempat produksi dan didistribusikan ke Pasar Wadai.</li><li>Cocok dibeli untuk dinikmati bersama atau dijadikan buah tangan.</li></ul>
<h3>Tips membeli</h3>
<p>Produksi dan penjualan dapat mengikuti musim atau ketersediaan. Hubungi penjual terlebih dahulu untuk memastikan waktu pembelian.</p>
HTML,
                'location_source_media' => null,
                'coordinate_x' => 14.80,
                'coordinate_y' => 67.50,
            ],
            [
                'category' => 'Kue & Wadai',
                'location_name' => 'Warung Acil Imar',
                'location_address' => 'Jl. Pahlawan, Seberang Masjid, Banjarmasin Tengah, Kota Banjarmasin',
                'location_description' => <<<'HTML'
<h1>Warung Acil Imar</h1>
<p>Warung Acil Imar menjual kue tradisional Banjar di Jalan Pahlawan, Kelurahan Seberang Masjid, Banjarmasin Tengah. Wadai dibuat segar dan dikenal oleh pelanggan lokal.</p>
<h2>Aneka wadai</h2>
<ul><li>Apam peranggi dan roti pisang.</li><li>Bingka kentang serta bingka gula habang.</li><li>Ragam kue manis-gurih berbahan santan dan gula merah.</li></ul>
<h3>Pengalaman berkunjung</h3>
<p>Warung berada di kawasan permukiman Kampung Melayu. Wadai paling nikmat disantap saat masih hangat; pilihan yang tersedia dapat berbeda setiap hari.</p>
HTML,
                'location_source_media' => null,
                'coordinate_x' => 15.20,
                'coordinate_y' => 71.50,
            ],
            [
                'category' => 'Kue & Wadai',
                'location_name' => 'Warung Isau Martapura',
                'location_address' => 'Jl. Pangeran Abdurrahman, Pasayangan Utara, Martapura, Kabupaten Banjar',
                'location_description' => <<<'HTML'
<h1>Warung Isau Martapura</h1>
<p>Warung Isau Pasayangan menawarkan penganan dan kue basah tradisional di Pasayangan Utara, Martapura. Banyak pilihannya berupa wadai berkuah manis dengan gula merah dan santan.</p>
<h2>Menu yang bisa dicoba</h2>
<ul><li>Intalu karuang, apam batil, selada gumbili, kakoleh, dan lupis.</li><li>Bubur baayak, putu mayang, serabi, serta bubur gunting.</li><li>Warung ini juga menyajikan hidangan Banjar seperti nasi sop, soto, dan ketupat Kandangan.</li></ul>
<h3>Lokasi</h3>
<p>Temukan warung di Jalan Pangeran Abdurrahman, kawasan Pasayangan Utara, Martapura. Menu dapat berubah mengikuti ketersediaan harian.</p>
HTML,
                'location_source_media' => null,
                'coordinate_x' => 33.20,
                'coordinate_y' => 66.00,
            ],
            [
                'category' => 'Camilan Banua',
                'location_name' => 'Sentra Dodol Kandangan',
                'location_address' => 'Desa Telaga Bidadari, Kecamatan Sungai Raya, Kabupaten Hulu Sungai Selatan',
                'location_description' => <<<'HTML'
<h1>Sentra Dodol Kandangan</h1>
<p>Sentra Dodol Kandangan berada di Desa Telaga Bidadari, Kecamatan Sungai Raya, sekitar lima kilometer dari Kota Kandangan. Area di sepanjang Jalan Bina Warga hingga Jalan Kapuh Wadani menjadi tempat banyak produsen dodol tradisional.</p>
<h2>Pengalaman di sentra</h2>
<ul><li>Membeli dodol langsung dari para pembuat setempat.</li><li>Melihat proses memasak tradisional dengan bahan ketan, kelapa, dan gula aren.</li><li>Memilih beberapa kemasan untuk oleh-oleh khas Hulu Sungai Selatan.</li></ul>
<h3>Lokasi pada peta</h3>
<p>Pin menunjukkan Desa Telaga Bidadari, Kecamatan Sungai Raya. Sentra ini membentang di beberapa ruas jalan, bukan satu toko tunggal.</p>
HTML,
                'location_source_media' => null,
                'coordinate_x' => 42.80,
                'coordinate_y' => 53.80,
            ],
            [
                'category' => 'Camilan Banua',
                'location_name' => 'Amplang Qita',
                'location_address' => 'Jl. Tirawan RT 06 RW 03, Desa Baharu Utara, Kabupaten Kotabaru',
                'location_description' => <<<'HTML'
<h1>Amplang Qita</h1>
<p>Amplang Qita adalah produsen camilan ikan yang beralamat di Jalan Tirawan, Desa Baharu Utara, Kabupaten Kotabaru. Amplang renyah berbahan ikan menjadi salah satu produk lokal pesisir yang cocok dibawa sebagai buah tangan.</p>
<h2>Sebelum membeli</h2>
<ul><li>Tanyakan ketersediaan produk dan pilihan ukuran kemasan.</li><li>Periksa kondisi kemasan serta tanggal kedaluwarsa.</li><li>Simpan amplang dalam wadah tertutup agar tetap renyah.</li></ul>
<h3>Lokasi</h3>
<p>Alamat produsen tercatat di kawasan Baharu Utara, Pulau Laut. Konfirmasikan apakah penjualan langsung tersedia sebelum datang.</p>
HTML,
                'location_source_media' => null,
                'coordinate_x' => 69.80,
                'coordinate_y' => 70.00,
            ],
            [
                'category' => 'Camilan Banua',
                'location_name' => 'Amplang Maskot Saijaan 2',
                'location_address' => 'Jl. Brigjend. H. Hasan Basri No. 14, Desa Semayap, Pulau Laut Utara, Kabupaten Kotabaru',
                'location_description' => <<<'HTML'
<h1>Amplang Maskot Saijaan 2</h1>
<p>Amplang Maskot Saijaan 2 merupakan produsen amplang ikan di Desa Semayap, Pulau Laut Utara, Kabupaten Kotabaru. Produk ini menjadi pilihan camilan kering dan oleh-oleh dari wilayah pesisir.</p>
<h2>Kenali produknya</h2>
<ul><li>Amplang dibuat dari olahan ikan dan memiliki tekstur renyah.</li><li>Tersedia dalam kemasan yang praktis dibawa bepergian.</li><li>Produk dan ukuran kemasan dapat berubah mengikuti stok.</li></ul>
<h3>Lokasi</h3>
<p>Alamat produsen berada di Jalan Brigjend. H. Hasan Basri No. 14, Semayap. Pastikan layanan pembelian langsung sebelum berkunjung.</p>
HTML,
                'location_source_media' => null,
                'coordinate_x' => 71.50,
                'coordinate_y' => 72.00,
            ],
        ];

        foreach ($locations as $data) {
            $category = $categories[$data['category']];
            unset($data['category']);

            CulinaryLocation::updateOrCreate(
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
