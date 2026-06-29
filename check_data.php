<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
echo "Recoltes: ".\App\Models\Recolte::count()."\n";
echo "Users clients: ".\App\Models\User::where('role','client')->count()."\n";
echo "Parcelles: ".\App\Models\Parcelle::count()."\n";
echo "Production totale: ".\App\Models\Recolte::sum('quantite')."\n";
echo "Revenus totaux: ".\App\Models\Recolte::sum('revenu_total')."\n";
echo "Couts totaux: ".\App\Models\Recolte::sum('couts_totaux')."\n";
