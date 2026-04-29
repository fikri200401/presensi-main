<?php

namespace Database\Seeders;

use App\Models\Office;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offices = [
            [
                'name' => 'Kantor Pusat Jakarta',
                'address' => 'Jl. Sudirman No. 1, Jakarta Pusat, DKI Jakarta',
                'latitude' => -6.200000,
                'longitude' => 106.816666,
                'radius' => 100,
            ],
            [
                'name' => 'Kantor Cabang Bandung',
                'address' => 'Jl. Asia Afrika No. 65, Bandung, Jawa Barat',
                'latitude' => -6.914744,
                'longitude' => 107.609810,
                'radius' => 150,
            ],
            [
                'name' => 'Kantor Cabang Surabaya',
                'address' => 'Jl. Pemuda No. 12, Surabaya, Jawa Timur',
                'latitude' => -7.250445,
                'longitude' => 112.768845,
                'radius' => 100,
            ],
            [
                'name' => 'Kantor Cabang Medan',
                'address' => 'Jl. Imam Bonjol No. 7, Medan, Sumatera Utara',
                'latitude' => 3.595196,
                'longitude' => 98.672226,
                'radius' => 120,
            ],
            [
                'name' => 'Kantor Cabang Semarang',
                'address' => 'Jl. Pandanaran No. 30, Semarang, Jawa Tengah',
                'latitude' => -7.005145,
                'longitude' => 110.438126,
                'radius' => 100,
            ],
            [
                'name' => 'Kantor Cabang Yogyakarta',
                'address' => 'Jl. Malioboro No. 52, Yogyakarta, DIY',
                'latitude' => -7.797068,
                'longitude' => 110.370529,
                'radius' => 100,
            ],
            [
                'name' => 'Kantor Cabang Bali',
                'address' => 'Jl. Teuku Umar No. 9, Denpasar, Bali',
                'latitude' => -8.670458,
                'longitude' => 115.212631,
                'radius' => 150,
            ],
            [
                'name' => 'Kantor Cabang Makassar',
                'address' => 'Jl. Sam Ratulangi No. 1, Makassar, Sulawesi Selatan',
                'latitude' => -5.147665,
                'longitude' => 119.432732,
                'radius' => 100,
            ],
        ];

        foreach ($offices as $office) {
            Office::create($office);
        }

        $this->command->info('Offices created successfully!');
        $this->command->info('Total Offices: ' . count($offices));
    }
}
