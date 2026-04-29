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
            'name' => 'Administrator',
            'email' => 'admin@pelindo.co.id',
            'password' => Hash::make('password'), // Password default: password
            'role' => 'superadmin',
            'email_verified_at' => now(),
        ]);
    }
}
