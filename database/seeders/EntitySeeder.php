<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Entity;

class EntitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entities = [
            ['name' => 'PT Pelabuhan Tanjung Priok', 'code' => 'PTP'],
            ['name' => 'PT Terminal Petikemas Surabaya', 'code' => 'TPS'],
            ['name' => 'PT Berlian Jasa Terminal Indonesia', 'code' => 'BJTI'],
            ['name' => 'PT Pelindo Jasa Maritim', 'code' => 'PJM'],
            ['name' => 'PT Pelindo Multi Terminal', 'code' => 'SPMT'],
            ['name' => 'Pelabuhan Belawan', 'code' => 'BLW'],
            ['name' => 'Pelabuhan Makassar', 'code' => 'MKS'],
        ];

        foreach ($entities as $entity) {
            Entity::create($entity);
        }
    }
}
