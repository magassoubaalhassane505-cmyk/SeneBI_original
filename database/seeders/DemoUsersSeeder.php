<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DemoUsersSeeder extends Seeder
{
    /**
     * Comptes de démonstration alignés sur les identifiants historiques de l'application (.ml).
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'mimi.manager@senebi.ml'],
            [
                'name' => 'Mimi Manager',
                'password' => bcrypt('manager123'),
                'role' => 'manager',
                'saison' => '2026',
                'is_active' => true,
                'status' => 'approved',
            ]
        );

        User::updateOrCreate(
            ['email' => 'sidi@sidi-agri.ml'],
            [
                'name' => 'Sidi',
                'password' => 'client123',
                'role' => 'client',
                'saison' => '2026',
                'is_active' => true,
                'status' => 'approved',
            ]
        );
    }
}
