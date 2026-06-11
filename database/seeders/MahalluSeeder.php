<?php

namespace Database\Seeders;

use App\Models\Place;
use Illuminate\Database\Seeder;

class MahalluSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mahallus = [
            'JUMA MASJID',
            'THEKKUMBAD JUMA MASJID',
            'FAROOQ MASJID',
            'USMAN MASJID',
            'SIDHEEQ MASJID',
            'BADAR MASJID',
            'MUHAMMED MASJID',
            'SHADULI MASJID',
            'MUHYUDHEEN MASJID',
            'RIFAYI MASJID',
            'BILAL MASJID',
            'HILAL MASJID',
            'THAKHVA MASJID',
        ];

        foreach ($mahallus as $mahallu) {
            Place::withTrashed()->updateOrCreate(
                ['name' => $mahallu],
                [
                    'description' => null,
                    'status' => 'active',
                    'deleted_at' => null,
                ]
            );
        }
    }
}
