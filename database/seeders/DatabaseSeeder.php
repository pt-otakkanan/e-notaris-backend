<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
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

        // 2. Buat user admin (khusus)
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('rahasia123'),
            'role_id' => 1, // admin
        ]);

        // 3. Buat 10 user acak dengan role_id 2 atau 3
        User::factory(10)->create([
            'role_id' => fake()->randomElement([2, 3]),
        ]);
    }
}
