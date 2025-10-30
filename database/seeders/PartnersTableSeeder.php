<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PartnersTableSeeder extends Seeder
{
    /**
     * Jalankan database seeder.
     */
    public function run(): void
    {
        DB::table('partners')->insert([
            [
                'id'         => 2,
                'name'       => 'Github',
                'link'       => 'https://github.com',
                'image'      => 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1759885456/enotaris/partners/partner_20251008010413.png',
                'image_path' => 'enotaris/partners/partner_20251008010413',
                'created_at' => Carbon::parse('2025-10-07 18:04:15'),
                'updated_at' => Carbon::parse('2025-10-07 18:04:15'),
            ],
            [
                'id'         => 3,
                'name'       => 'Google',
                'link'       => 'https://google.com',
                'image'      => 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1759885484/enotaris/partners/partner_20251008010441.png',
                'image_path' => 'enotaris/partners/partner_20251008010441',
                'created_at' => Carbon::parse('2025-10-07 18:04:43'),
                'updated_at' => Carbon::parse('2025-10-07 18:04:43'),
            ],
            [
                'id'         => 4,
                'name'       => 'Docker',
                'link'       => 'https://docker.com',
                'image'      => 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1759885519/enotaris/partners/partner_20251008010515.png',
                'image_path' => 'enotaris/partners/partner_20251008010515',
                'created_at' => Carbon::parse('2025-10-07 18:05:18'),
                'updated_at' => Carbon::parse('2025-10-07 18:05:18'),
            ],
            [
                'id'         => 5,
                'name'       => 'Petrokimia Gresik',
                'link'       => 'https://petrokimiagresik.com',
                'image'      => 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1759886411/enotaris/partners/partner_20251008012009.png',
                'image_path' => 'enotaris/partners/partner_20251008012009',
                'created_at' => Carbon::parse('2025-10-07 18:20:11'),
                'updated_at' => Carbon::parse('2025-10-07 18:20:11'),
            ],
            [
                'id'         => 6,
                'name'       => 'Microsoft',
                'link'       => 'https://microsoft.com',
                'image'      => 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1759886438/enotaris/partners/partner_20251008012036.png',
                'image_path' => 'enotaris/partners/partner_20251008012036',
                'created_at' => Carbon::parse('2025-10-07 18:20:39'),
                'updated_at' => Carbon::parse('2025-10-07 18:20:39'),
            ],
            [
                'id'         => 7,
                'name'       => 'Vercel',
                'link'       => 'https://vercel.com',
                'image'      => 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1759886538/enotaris/partners/partner_20251008012215.png',
                'image_path' => 'enotaris/partners/partner_20251008012215',
                'created_at' => Carbon::parse('2025-10-07 18:22:19'),
                'updated_at' => Carbon::parse('2025-10-07 18:22:19'),
            ],
            [
                'id'         => 8,
                'name'       => 'Cloudinary',
                'link'       => 'https://cloudinary.com',
                'image'      => 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1759886571/enotaris/partners/partner_20251008012246.png',
                'image_path' => 'enotaris/partners/partner_20251008012246',
                'created_at' => Carbon::parse('2025-10-07 18:22:51'),
                'updated_at' => Carbon::parse('2025-10-07 18:22:51'),
            ],
        ]);
    }
}
