<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Infrastructure;
use App\Models\Entity;
use Faker\Factory as Faker;

class InfrastructureSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $entities = Entity::all();

        $categories = [
            'equipment' => [
                'Gantry Luffing Crane', 'Rubber Tired Gantry', 'Reach Stacker',
                'Head Truck', 'Chassis Trailer', 'Forklift', 'Excavator',
                'Wheel Loader', 'Mobile Crane', 'Straddle Carrier',
                'Quay Crane', 'Harbour Mobile Crane', 'Towing Tractor'
            ],
            'facility' => [
                'Gudang Penumpukan', 'Silo', 'Tangki Timbun', 'Lapangan Penumpukan',
                'Area Konsolidasi', 'Gedung Operasional', 'Pos Jaga', 'Workshop Alat Berat',
                'Dermaga', 'Fasilitas Air Bersih'
            ],
            'utility' => [
                'Genset Utama', 'Genset Cadangan', 'Sistem Pompa Air', 'Gardu Listrik',
                'Panel Distribusi', 'Sistem Kompresor', 'Penerangan Jalan', 'Sistem Pemadam Kebakaran'
            ]
        ];

        $infrastructures = [];

        foreach ($entities as $entity) {
            // Berikan 10-25 aset per entitas
            $assetCount = rand(10, 25);

            for ($i = 0; $i < $assetCount; $i++) {
                $category = $faker->randomElement(array_keys($categories));
                $type = $faker->randomElement($categories[$category]);

                // Buat kode unik
                $prefix = strtoupper(substr(str_replace(' ', '', $type), 0, 3));
                $codeName = $prefix . '-' . $entity->code . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

                // 15% kemungkinan rusak
                $status = (rand(1, 100) <= 15) ? 'breakdown' : 'available';

                // Generate Dummy Image URL
                // Format parameter text: "Nama Alat | Kode Unik"
                $imageText = urlencode($type . ' | ' . $codeName);
                // Menggunakan background biru laut dengan text putih
                $imageUrl = "https://placehold.co/600x400/0284c7/ffffff?text=" . $imageText;

                $infrastructures[] = [
                    'entity_id'  => $entity->id,
                    'category'   => $category,
                    'type'       => $type,
                    'code_name'  => $codeName,
                    'status'     => $status,
                    'image'      => $imageUrl, // Data gambar ditambahkan di sini
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert in chunks to be safe
        foreach (array_chunk($infrastructures, 50) as $chunk) {
            Infrastructure::insert($chunk);
        }
    }
}
