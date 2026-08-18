<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Listing;

class ListingSeeder extends Seeder
{
    public function run()
    {
        $samples = [
            [
                'title' => '4 xonali uy - Sugdiyona mahallasi',
                'description' => 'Keng va qulay 4 xonali uy. Yaxshi ta\'mirlash.',
                'price' => 187000,
                'currency' => 'USD',
                'type' => 'sale',
                'address' => 'Sugdiyona mahallasi, Toshkent',
                'city' => 'Toshkent',
                'photos' => [],
                'is_vip' => true,
            ],
            [
                'title' => '2 xonali kvartira - Olmazor',
                'description' => '8-qavat, markaziy joy',
                'price' => 650,
                'currency' => 'USD',
                'type' => 'rent',
                'address' => 'Olmazor tumani, Medgorodok',
                'city' => 'Toshkent',
                'photos' => [],
                'is_vip' => true,
            ],
            [
                'title' => 'Kunlik ijaraga uy - Chilonzor',
                'description' => 'Kunlik ijaraga mos, qulay joylashuv',
                'price' => 45,
                'currency' => 'USD',
                'type' => 'daily',
                'address' => 'Chilonzor',
                'city' => 'Toshkent',
                'photos' => [],
                'is_vip' => false,
            ],
        ];

        foreach ($samples as $s) {
            Listing::create($s);
        }
    }
}
