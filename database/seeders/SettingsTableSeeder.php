<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SettingsTableSeeder extends Seeder
{
    /**
     * Jalankan database seeder.
     */
    public function run(): void
    {
        DB::table('settings')->insert([
            [
                'id'            => 1,
                'logo'          => 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1761621911/enotaris/settings/logo_20251028032508.png',
                'logo_path'     => 'enotaris/settings/logo_20251028032508',
                'favicon'       => 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1761621913/enotaris/settings/favicon_20251028032510.png',
                'favicon_path'  => 'enotaris/settings/favicon_20251028032510',
                'telepon'       => null,
                'facebook'      => null,
                'instagram'     => null,
                'twitter'       => null,
                'linkedin'      => null,
                'title_hero'    => 'Praktik Kenotariatan Dalam Satu Platform',
                'desc_hero'     => 'E-Notaris membantu Anda mengelola praktik notaris secara efisien. Mulai dari pembuatan akta, penyimpanan dokumen, hingga pelacakan aktivitas. Semua dalam satu platform digital yang aman dan terpercaya.',
                'desc_footer'   => 'Platform digital untuk memudahkan notaris dalam mengelola proyek, akta, hingga pelacakan aktivitas secara aman dan terpercaya.',
                'created_at'    => Carbon::parse('2025-10-27 20:23:49'),
                'updated_at'    => Carbon::parse('2025-10-27 20:25:12'),
            ],
        ]);
    }
}
