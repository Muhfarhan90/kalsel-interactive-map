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
            ['category' => 'Wisata Alam', 'location_name' => 'Pegunungan Meratus Loksado', 'location_address' => 'Kecamatan Loksado, Kabupaten Hulu Sungai Selatan', 'location_description' => 'Kawasan pegunungan dengan lanskap hijau, aliran sungai, dan kehidupan masyarakat Dayak Meratus.', 'coordinate_x' => 48, 'coordinate_y' => 47],
            ['category' => 'Wisata Alam', 'location_name' => 'Bukit Matang Kaladan', 'location_address' => 'Desa Tiwingan Lama, Kecamatan Aranio, Kabupaten Banjar', 'location_description' => 'Bukit dengan panorama gugusan pulau kecil di kawasan Waduk Riam Kanan.', 'coordinate_x' => 40, 'coordinate_y' => 31],
            ['category' => 'Budaya & Sejarah', 'location_name' => 'Pasar Terapung Lok Baintan', 'location_address' => 'Desa Sungai Pinang, Kecamatan Sungai Tabuk, Kabupaten Banjar', 'location_description' => 'Pasar tradisional di atas Sungai Martapura dengan pedagang yang menggunakan perahu jukung.', 'coordinate_x' => 25, 'coordinate_y' => 23, 'location_media_url' => 'images/maps/kalsel.webp'],
            ['category' => 'Budaya & Sejarah', 'location_name' => 'Museum Lambung Mangkurat', 'location_address' => 'Jalan Ahmad Yani Km 36, Kota Banjarbaru', 'location_description' => 'Museum yang menyimpan koleksi sejarah dan budaya masyarakat Kalimantan Selatan.', 'coordinate_x' => 30, 'coordinate_y' => 28],
            ['category' => 'Wisata Religi', 'location_name' => 'Masjid Sultan Suriansyah', 'location_address' => 'Kuin Utara, Kecamatan Banjarmasin Utara, Kota Banjarmasin', 'location_description' => 'Salah satu masjid tertua di Kalimantan Selatan dengan arsitektur tradisional Banjar.', 'coordinate_x' => 18, 'coordinate_y' => 28],
            ['category' => 'Wisata Religi', 'location_name' => 'Makam Datu Kalampayan', 'location_address' => 'Desa Kalampayan Tengah, Kecamatan Astambul, Kabupaten Banjar', 'location_description' => 'Kompleks makam Syekh Muhammad Arsyad al-Banjari yang menjadi tujuan wisata religi.', 'coordinate_x' => 34, 'coordinate_y' => 24],
            ['category' => 'Wisata Bahari', 'location_name' => 'Pantai Angsana', 'location_address' => 'Kecamatan Angsana, Kabupaten Tanah Bumbu', 'location_description' => 'Pantai dengan wisata bawah laut dan kawasan terumbu karang.', 'coordinate_x' => 64, 'coordinate_y' => 75],
            ['category' => 'Wisata Bahari', 'location_name' => 'Pulau Samber Gelap', 'location_address' => 'Kecamatan Pulau Sebuku, Kabupaten Kotabaru', 'location_description' => 'Pulau kecil dengan pasir putih, air jernih, dan kawasan konservasi penyu.', 'coordinate_x' => 79, 'coordinate_y' => 82],
        ];

        foreach ($locations as $data) {
            $category = $categories[$data['category']];
            unset($data['category']);

            TourismLocation::updateOrCreate(
                ['location_name' => $data['location_name']],
                array_merge($data, [
                    'category_id' => $category->id,
                    'map_id' => $map->id,
                    'location_source_media' => 'Data awal aplikasi',
                    'is_active' => true,
                ]),
            );
        }
    }
}
