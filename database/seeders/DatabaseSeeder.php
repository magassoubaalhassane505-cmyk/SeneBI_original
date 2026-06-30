<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'is_active' => true,
            ]
        );

        User::unguard();
        User::firstOrCreate(
            ['email' => 'mimi.manager@senebi.sn'],
            [
                'name' => 'Mimi Manager',
                'password' => 'manager123',
                'role' => 'admin',
                'saison' => '2024',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'sidi@sidi-agri.sn'],
            [
                'name' => 'Client Test',
                'password' => 'client123',
                'role' => 'client',
                'saison' => '2024',
                'is_active' => true,
            ]
        );

        $this->call(DemoUsersSeeder::class);
        User::reguard();
    }
}
