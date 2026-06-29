<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Parcelle;
use App\Models\Recolte;
use App\Models\Stock;
use Illuminate\Database\Seeder;

class BiDataSeeder extends Seeder
{
    public function run()
    {
        // Créer des agriculteurs de test avec localisations
        $locations = ['Bamako', 'Sikasso', 'Ségou', 'Kayes', 'Mopti', 'Koulikoro'];
        
        foreach ($locations as $i => $location) {
            User::firstOrCreate(
                ['email' => "agriculteur{$i}@test.com"],
                [
                    'name' => "Agriculteur {$location}",
                    'password' => bcrypt('password'),
                    'role' => 'client',
                    'location' => $location,
                    'is_active' => true,
                    'status' => 'approved',
                ]
            );
        }

        // Créer des parcelles avec différentes cultures
        $cultures = ['Riz', 'Maïs', 'Coton'];
        $users = User::where('role', 'client')->get();
        
        foreach ($users as $user) {
            foreach ($cultures as $culture) {
                Parcelle::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'culture' => $culture,
                        'nom' => "Parcelle {$culture} - {$user->name}",
                    ],
                    [
                        'surface' => rand(3, 15) + rand(0, 9) / 10, // Entre 3.0 et 15.9 ha
                        'region' => $user->location ?? 'Non spécifié',
                    ]
                );
            }
        }

        // Créer des récoltes sur plusieurs mois
        $months = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        $parcelles = Parcelle::all();
        
        foreach ($parcelles as $parcelle) {
            // 3 à 5 récoltes par parcelle sur différents mois
            $numRecoltes = rand(3, 5);
            $selectedMonths = collect($months)->shuffle()->take($numRecoltes)->sort()->values();
            
            foreach ($selectedMonths as $month) {
                $quantite = rand(2000, 15000); // Entre 2000 et 15000 kg
                $prixUnitaire = rand(300, 800); // Entre 300 et 800 FCFA/kg
                $couts = rand(500000, 2000000); // Entre 500k et 2M FCFA
                
                Recolte::create([
                    'parcelle_id' => $parcelle->id,
                    'user_id' => $parcelle->user_id,
                    'date_recolte' => "2024-{$month}-" . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                    'quantite' => $quantite,
                    'prix_unitaire' => $prixUnitaire,
                    'culture' => $parcelle->culture,
                    'saison' => '2024',
                    'couts_totaux' => $couts,
                    'revenu_total' => $quantite * $prixUnitaire,
                    'benefice_net' => ($quantite * $prixUnitaire) - $couts,
                ]);
            }
        }

        // Créer des stocks pour certains agriculteurs
        $intrants = ['Engrais NPK', 'Semences Riz', 'Semences Maïs', 'Pesticide', 'Herbicide'];
        
        foreach ($users as $user) {
            foreach ($intrants as $intrant) {
                $quantite = rand(50, 500);
                $seuil = rand(30, 100);
                
                Stock::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'nom' => $intrant,
                    ],
                    [
                        'quantite_actuelle' => $quantite,
                        'seuil_critique' => $seuil,
                        'cout_unitaire' => rand(5000, 15000),
                        'type' => 'intrant',
                    ]
                );
            }
        }

        $this->command->info('Données BI créées avec succès !');
        $this->command->info('- ' . $users->count() . ' agriculteurs créés');
        $this->command->info('- ' . $parcelles->count() . ' parcelles créées');
        $this->command->info('- ' . Recolte::count() . ' récoltes créées');
        $this->command->info('- ' . Stock::count() . ' stocks créés');
    }
}
