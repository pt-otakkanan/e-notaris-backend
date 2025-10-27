<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Deed;
use App\Models\User;

class DeedSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role_id', 1)->first();

        if (!$admin) {
            $this->command->warn('⚠️ Admin user not found. Skipping deed seeding.');
            return;
        }

        $deeds = [
            [
                'name' => 'Pendirian CV',
                'description' => 'Pembuatan akta pendirian Commanditaire Vennootschap (CV) untuk kegiatan usaha bersama.',
            ],
            [
                'name' => 'Pendirian PT',
                'description' => 'Pembuatan akta pendirian Perseroan Terbatas (PT) untuk badan usaha berbadan hukum.',
            ],
            [
                'name' => 'Jual Beli',
                'description' => 'Akta jual beli antara dua pihak dengan dasar hukum yang sah.',
            ],
        ];

        foreach ($deeds as $deed) {
            Deed::updateOrCreate(
                ['name' => $deed['name']],
                [
                    'description' => $deed['description'],
                    'user_notaris_id' => $admin->id,
                ]
            );
        }

        $this->command->info('✅ 3 Deeds seeded successfully for admin user.');
    }
}
