<?php

namespace Database\Seeders;

use App\Models\Regency;
use Illuminate\Database\Seeder;

class RegencySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'balangan' => 'Kabupaten Balangan',
            'banjar' => 'Kabupaten Banjar',
            'barito-kuala' => 'Kabupaten Barito Kuala',
            'hulu-sungai-selatan' => 'Kabupaten Hulu Sungai Selatan',
            'hulu-sungai-tengah' => 'Kabupaten Hulu Sungai Tengah',
            'hulu-sungai-utara' => 'Kabupaten Hulu Sungai Utara',
            'kotabaru' => 'Kabupaten Kotabaru',
            'tabalong' => 'Kabupaten Tabalong',
            'tanah-bumbu' => 'Kabupaten Tanah Bumbu',
            'tanah-laut' => 'Kabupaten Tanah Laut',
            'tapin' => 'Kabupaten Tapin',
            'banjarbaru' => 'Kota Banjarbaru',
            'banjarmasin' => 'Kota Banjarmasin',
        ] as $slug => $name) {
            Regency::firstOrCreate(['slug' => $slug], [
                'name' => $name,
                'sort_order' => (int) Regency::max('sort_order') + 1,
            ]);
        }
    }
}
