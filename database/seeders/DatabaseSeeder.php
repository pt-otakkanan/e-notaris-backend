<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Role; // pastikan model Role ada

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat 3 roles
        $roles = [
            ['id' => 1, 'name' => 'admin'],
            ['id' => 2, 'name' => 'penghadap'],
            ['id' => 3, 'name' => 'notaris'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['id' => $role['id']], $role);
        }

        DB::table('users')->insert([
            [
                'role_id'            => 3,
                'name'               => 'Adam Aditya',
                'email'              => 'adam@gmail.com',
                'password'           => Hash::make('rahasia123'),
                'status_verification' => 'approved',
                'verify_key'         => 'QK4R08F',
                'email_verified_at'  => '2025-09-08 14:36:15',
            ],
            [
                'role_id'            => 2,
                'name'               => 'DEVANO ALIF RAMADHAN',
                'email'              => 'devanorama123@gmail.com',
                'password'           => Hash::make('rahasia123'),
                'status_verification' => 'approved',
                'verify_key'         => '7RZWDO0',
                'email_verified_at'  => '2025-09-08 14:51:53',
            ],
            [
                'role_id'            => 2,
                'name'               => 'Iwang',
                'email'              => 'iwang@gmail.com',
                'password'           => Hash::make('rahasia123'),
                'status_verification' => 'approved',
                'verify_key'         => 'TOCTPNP',
                'email_verified_at'  => '2025-09-08 00:00:00',
            ],
            [
                'role_id'            => 2,
                'name'               => 'Yasmin Zakiyah Firmasyah',
                'email'              => 'yasmin@gmail.com',
                'password'           => Hash::make('rahasia123'),
                'status_verification' => 'approved',
                'verify_key'         => 'A9QZJ9O',
                'email_verified_at'  => '2025-09-08 00:00:00',
            ],
            [
                'role_id'            => 2,
                'name'               => 'Dhika',
                'email'              => 'dhika@gmail.com',
                'password'           => Hash::make('rahasia123'),
                'status_verification' => 'pending',
                'verify_key'         => 'IDEZVZ5',
                'email_verified_at'  => '2025-09-08 00:00:00',
            ],
            [
                'role_id'            => 1,
                'name'               => 'admin',
                'email'              => 'admin@gmail.com',
                'password'           => Hash::make('rahasia123'),
                'status_verification' => 'pending',
                'verify_key'         => '5HSAKDU',
                'email_verified_at'  => '2025-09-08 15:28:18',
            ],
        ]);
    }
}
