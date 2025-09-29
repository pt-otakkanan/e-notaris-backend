<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Identity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\TemplatesTableSeeder;

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
            $filePhoto      = $this->ph("PHOTO User {$user->id}");
            $fileKtpNotaris = $this->ph("KTP NOTARIS User {$user->id}");

            Identity::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'ktp'                   => $this->digits(16),
                    'npwp'                  => $this->digits(15),
                    'ktp_notaris'           => ($user->role_id == 3) ? $this->digits(16) : null,

                    'file_ktp'              => $fileKtp,
                    'file_ktp_path'         => "seed/users/{$user->id}/identity/ktp.jpg",

                    'file_kk'               => $fileKk,
                    'file_kk_path'          => "seed/users/{$user->id}/identity/kk.jpg",

                    'file_npwp'             => $fileNpwp,
                    'file_npwp_path'        => "seed/users/{$user->id}/identity/npwp.jpg",

                    'file_ktp_notaris'      => $fileKtpNotaris,
                    'file_ktp_notaris_path' => "seed/users/{$user->id}/identity/ktp_notaris.jpg",

                    'file_sign'             => $fileSign,
                    'file_sign_path'        => "seed/users/{$user->id}/identity/sign.png",

                    'file_photo'            => $filePhoto,
                    'file_photo_path'       => "seed/users/{$user->id}/identity/photo.jpg",
                ]
            );
            // --- Panggil seeder templates ---
            $this->call(TemplastesTableSeeder::class);
        }
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
