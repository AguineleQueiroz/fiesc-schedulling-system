<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@fiesc.local'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin1234'),
                'role' => UserRole::Admin,
            ]
        );
    }
}
