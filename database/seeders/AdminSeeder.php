<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@marabahospital.test'],
            [
                'name' => 'Hospital Admin',
                'phone' => null,
                'role' => 'admin',
                'password' => 'AdminPass123!', // hashed automatically via the 'hashed' cast on User
                'is_approved' => true,
            ]
        );
    }
}