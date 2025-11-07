<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Identity;
use Illuminate\Database\Seeder;
use Database\Seeders\DeedSeeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\TemplatesTableSeeder;
use Database\Seeders\PartnersTableSeeder;
use Database\Seeders\SettingsTableSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Roles
        $roles = [
            ['id' => 1, 'name' => 'admin'],
            ['id' => 2, 'name' => 'penghadap'],
            ['id' => 3, 'name' => 'notaris'],
        ];
        foreach ($roles as $role) {
            Role::updateOrCreate(['id' => $role['id']], $role);
        }

        // 2) Users (lengkap dengan kolom profil)
        $usersToSeed = [
            [
                'role_id'             => 1,
                'name'                => 'admin',
                'email'               => 'admin@gmail.com',
                'password'            => 'rahasia123',
                'status_verification' => 'approved',
                'verify_key'          => '5HSAKDU',
                'email_verified_at'   => '2025-09-08 15:28:18',
                'gender'              => 'male',
                'telepon'             => '081200000000',
                'address'             => 'Jl. Merpati No. 10',
                'city'                => 'Jakarta',
                'province'            => 'DKI Jakarta',
                'postal_code'         => '10220',
            ],
            [
                'role_id'             => 3,
                'name'                => 'Adam Aditya',
                'email'               => 'adam@gmail.com',
                'password'            => 'rahasia123',
                'status_verification' => 'approved',
                'verify_key'          => 'QK4R08F',
                'email_verified_at'   => '2025-09-08 14:36:15',
                'gender'              => 'male',
                'telepon'             => '081200000001',
                'address'             => 'Jl. Melati No. 1',
                'city'                => 'Jakarta',
                'province'            => 'DKI Jakarta',
                'postal_code'         => '10110',
            ],
            [
                'role_id'             => 2,
                'name'                => 'DEVANO ALIF RAMADHAN',
                'email'               => 'devanorama123@gmail.com',
                'password'            => 'rahasia123',
                'status_verification' => 'approved',
                'verify_key'          => '7RZWDO0',
                'email_verified_at'   => '2025-09-08 14:51:53',
                'gender'              => 'male',
                'telepon'             => '081200000002',
                'address'             => 'Jl. Kenanga No. 2',
                'city'                => 'Bandung',
                'province'            => 'Jawa Barat',
                'postal_code'         => '40111',
            ],
            [
                'role_id'             => 2,
                'name'                => 'Iwang',
                'email'               => 'iwang@gmail.com',
                'password'            => 'rahasia123',
                'status_verification' => 'approved',
                'verify_key'          => 'TOCTPNP',
                'email_verified_at'   => '2025-09-08 00:00:00',
                'gender'              => 'male',
                'telepon'             => '081200000003',
                'address'             => 'Jl. Cendana No. 3',
                'city'                => 'Surabaya',
                'province'            => 'Jawa Timur',
                'postal_code'         => '60293',
            ],
            [
                'role_id'             => 2,
                'name'                => 'Yasmin Zakiyah Firmasyah',
                'email'               => 'yasmin@gmail.com',
                'password'            => 'rahasia123',
                'status_verification' => 'approved',
                'verify_key'          => 'A9QZJ9O',
                'email_verified_at'   => '2025-09-08 00:00:00',
                'gender'              => 'female',
                'telepon'             => '081200000004',
                'address'             => 'Jl. Flamboyan No. 4',
                'city'                => 'Yogyakarta',
                'province'            => 'DI Yogyakarta',
                'postal_code'         => '55281',
            ],
            [
                'role_id'             => 2,
                'name'                => 'Dhika',
                'email'               => 'dhika@gmail.com',
                'password'            => 'rahasia123',
                'status_verification' => 'approved',
                'verify_key'          => 'IDEZVZ5',
                'email_verified_at'   => '2025-09-08 00:00:00',
                'gender'              => 'male',
                'telepon'             => '081200000005',
                'address'             => 'Jl. Anggrek No. 5',
                'city'                => 'Semarang',
                'province'            => 'Jawa Tengah',
                'postal_code'         => '50135',
            ],
        ];

        foreach ($usersToSeed as $u) {
            $user = User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'role_id'             => $u['role_id'],
                    'name'                => $u['name'],
                    'password'            => Hash::make($u['password']),
                    'status_verification' => $u['status_verification'],
                    'verify_key'          => $u['verify_key'],
                    'email_verified_at'   => $u['email_verified_at'],
                    'gender'              => $u['gender'],
                    'telepon'             => $u['telepon'] ?? null,
                    'address'             => $u['address'],
                    'city'                => $u['city'],
                    'province'            => $u['province'],
                    'postal_code'         => $u['postal_code'],
                ]
            );

            // 3) Identity dummy (semua kolom file diisi placeholder)
            $fileKtp        = $this->ph("KTP User {$user->id}");
            $fileKk         = $this->ph("KK User {$user->id}");
            $fileNpwp       = $this->ph("NPWP User {$user->id}");
            $fileSign       = $this->ph("SIGN User {$user->id}");
            $fileInitial    = $this->ph("Initial User {$user->id}");
            $filePhoto      = $this->ph("PHOTO User {$user->id}");
            $fileKtpNotaris = $this->ph("KTP NOTARIS User {$user->id}");

            Identity::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'ktp'                   => $this->digits(16),
                    'npwp'                  => $this->digits(15),
                    'ktp_notaris'           => ($user->role_id == 3) ? $this->digits(16) : null,

                    'file_ktp'              => "https://res.cloudinary.com/dr1qyzdld/image/upload/v1762138252/enotaris/users/3/identity/ktp/ktp_1762138246_bExv1Axz.jpg",
                    'file_ktp_path'         => "seed/users/{$user->id}/identity/ktp.jpg",

                    'file_kk'               => "https://res.cloudinary.com/dr1qyzdld/image/upload/v1762138255/enotaris/users/3/identity/kk/kk_1762138253_N5TPlJTR.jpg",
                    'file_kk_path'          => "seed/users/{$user->id}/identity/kk.jpg",

                    // 'file_npwp'             => $fileNpwp,
                    // 'file_npwp_path'        => "seed/users/{$user->id}/identity/npwp.jpg",

                    // 'file_ktp_notaris'      => $fileKtpNotaris,
                    // 'file_ktp_notaris_path' => "seed/users/{$user->id}/identity/ktp_notaris.jpg",

                    'file_sign'             => "https://res.cloudinary.com/dr1qyzdld/image/upload/v1761623495/enotaris/users/2/identity/sign/sign_1761623492_fy7OdBy5.png",
                    'file_sign_path'        => "seed/users/{$user->id}/identity/sign.png",

                    'file_initial'             => "https://res.cloudinary.com/dr1qyzdld/image/upload/v1761623498/enotaris/users/2/identity/initial/initial_1761623497_sqZSieSh.png",
                    'file_initial_path'        => "seed/users/{$user->id}/identity/initial.png",

                    'file_photo'            => "https://res.cloudinary.com/dr1qyzdld/image/upload/v1762138260/enotaris/users/3/identity/photo/photo_1762138257_2L0TPUEJ.png",
                    'file_photo_path'       => "seed/users/{$user->id}/identity/photo.jpg",
                ]
            );
            // --- Panggil seeder templates ---
            $this->call(TemplatesTableSeeder::class);
        }

        $this->call(DeedSeeder::class);
        $this->call(PartnersTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
    }

    private function ph(string $label): string
    {
        return 'https://via.placeholder.com/800x500.png?text=' . rawurlencode($label);
    }

    private function digits(int $len): string
    {
        $s = '';
        for ($i = 0; $i < $len; $i++) $s .= random_int(0, 9);
        return $s;
    }
}
