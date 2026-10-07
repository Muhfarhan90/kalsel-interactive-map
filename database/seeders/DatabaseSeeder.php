<?php

namespace Database\Seeders;

use App\Models\Homepage;
use App\Models\Location;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([UserSeeder::class]);

        
        $homepage = Homepage::updateOrCreate([
            'title' => 'Satu tempat untuk mengenal Kalimantan Selatan',
            'label' => 'Portal Banua',
            'description' => 'Temukan wisata, cita rasa, dan akses perjalanan di Banua. Pilih cara menjelajah yang paling menarik untukmu.',
            'image' => "images/home/menara-pandang.jpeg",
        ]);

        $menu = Menu::updateOrCreate([
            'name' => 'Wisata',
            'slug' => 'wisata',
            'icon' => 'fas fa-bars',
            'description' => 'Jelajahi berbagai destinasi wisata menarik di Provinsi Kalimantan Selatan.',
            'title' => 'Peta Wisata',
            'sub_title' => 'Provinsi Kalimantan Selatan',
            'logo' => "images/peta_provinsi_kalsel.jpeg",
            'banner' => "images/home/menara-pandang.jpeg",
            'color' => '#FF5733',   
        ]);

        $menu2 = Menu::updateOrCreate([
            'name' => 'Kuliner',
            'slug' => 'kuliner',
            'icon' => 'fas fa-utensils',
            'description' => 'Temukan berbagai kuliner khas Kalimantan Selatan yang menggugah selera.',
            'title' => 'Peta Kuliner',
            'sub_title' => 'Provinsi Kalimantan Selatan',
            'logo' => "images/peta_provinsi_kalsel.jpeg",
            'banner' => "images/home/menara-pandang.jpeg",
            'color' => '#33FF57',
        ]);

        $menu3 = Menu::updateOrCreate([
            'name' => 'Transportasi',
            'slug' => 'transportasi',
            'icon' => 'fas fa-bus',
            'description' => 'Informasi transportasi di Kalimantan Selatan untuk memudahkan perjalanan Anda.',
            'title' => 'Peta Transportasi',
            'sub_title' => 'Provinsi Kalimantan Selatan',
            'logo' => "images/peta_provinsi_kalsel.jpeg",
            'banner' => "images/home/menara-pandang.jpeg",
            'color' => '#3357FF',
        ]);


        $category = $menu->categories()->updateOrCreate([
            'menu_id' => $menu->id,
            'name' => 'Wisata Alam',
            'color' => '#4CAF50',
            'icon' => 'fas fa-tree',
            'background' => "images/home/menara-pandang.jpeg",
        ]);

        $location = Location::updateOrCreate([
            'category_id' => $category->id,
            'name' => 'Bukit Bintang',
            'address' => 'Jl. Bukit Bintang, Kabupaten Banjar, Kalimantan Selatan',
            'description' => 'Bukit Bintang adalah destinasi wisata alam yang menawarkan pemandangan indah dan udara segar.',
            'media' => "images/locations/bukit-bintang.jpg",
            'source_media' => 'https://example.com/bukit-bintang',
            'x_location' => 20,
            'y_location' => 30,
        ]);

    }
}
