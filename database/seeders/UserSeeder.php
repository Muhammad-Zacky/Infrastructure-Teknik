<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator Pusat',
            'email' => 'admin@pelindo.co.id',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Operator Tanjung Priok',
            'email' => 'operator.ptp@pelindo.co.id',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'entity_id' => \App\Models\Entity::where('code', 'PTP')->first()->id ?? null,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Operator TPS',
            'email' => 'operator.tps@pelindo.co.id',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'entity_id' => \App\Models\Entity::where('code', 'TPS')->first()->id ?? null,
            'email_verified_at' => now(),
        ]);
    }
}
