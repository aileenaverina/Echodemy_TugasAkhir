<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'role'        => UserRole::Admin,
            'nama_lengkap' => 'Administrator',
            'kode_user'   => 'ADMIN',
            'email'       => 'admin@echodemy.test',
            'password'    => Hash::make('password'),
        ]);
    }
}
