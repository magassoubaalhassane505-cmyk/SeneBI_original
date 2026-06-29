<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test des requêtes BI ===\n\n";

// Test 1: Production par mois
echo "1. Production par mois:\n";
$productionByMonth = \App\Models\Recolte::selectRaw('MONTH(date_recolte) as mois, SUM(quantite) as total')
    ->groupBy('mois')
    ->orderBy('mois')
    ->get();
echo "   Résultats: " . $productionByMonth->count() . "\n";
foreach ($productionByMonth as $row) {
    echo "   Mois {$row->mois}: {$row->total} kg\n";
}

// Test 2: Cumulative harvests
echo "\n2. Cumulative harvests:\n";
$cumulativeTotal = 0;
$cumulativeHarvests = $productionByMonth->map(function ($row) use (&$cumulativeTotal) {
    $cumulativeTotal += (float) $row->total;
    return (object)[
        'mois' => $row->mois,
        'total' => (float) $row->total,
        'cumul' => $cumulativeTotal,
    ];
});
echo "   Résultats: " . $cumulativeHarvests->count() . "\n";

// Test 3: Revenus vs Coûts
echo "\n3. Revenus vs Coûts:\n";
$revenusTotaux = \App\Models\Recolte::sum('revenu_total') ?? 0;
$coutsTotaux = \App\Models\Recolte::sum('couts_totaux') ?? 0;
echo "   Revenus totaux: {$revenusTotaux} FCFA\n";
echo "   Coûts totaux: {$coutsTotaux} FCFA\n";
echo "   Données disponibles: " . (($revenusTotaux > 0 || $coutsTotaux > 0) ? 'OUI' : 'NON') . "\n";

// Test 4: Performance par culture
echo "\n4. Performance par culture:\n";
$performanceByCulture = \App\Models\Recolte::selectRaw('culture, SUM(quantite) as total_qte, AVG(benefice_net) as avg_benefice')
    ->groupBy('culture')
    ->get();
echo "   Résultats: " . $performanceByCulture->count() . "\n";
foreach ($performanceByCulture as $row) {
    echo "   {$row->culture}: {$row->total_qte} kg, bénéfice moyen: {$row->avg_benefice}\n";
}

// Test 5: Agriculteurs par région
echo "\n5. Agriculteurs par région:\n";
$farmersByRegion = \App\Models\User::where('role', 'client')
    ->where('is_active', true)
    ->whereNotNull('location')
    ->where('location', '!=', '')
    ->selectRaw('location, COUNT(*) as total')
    ->groupBy('location')
    ->get();
echo "   Résultats: " . $farmersByRegion->count() . "\n";
foreach ($farmersByRegion as $row) {
    echo "   {$row->location}: {$row->total} agriculteurs\n";
}

// Test 6: Rendement par mois
echo "\n6. Rendement par mois:\n";
$rendementByMonth = \App\Models\Recolte::selectRaw('MONTH(recoltes.date_recolte) as mois, CAST(SUM(recoltes.quantite) / NULLIF(SUM(parcelles.surface), 0) AS DECIMAL(10,2)) as avg_rendement')
    ->join('parcelles', 'recoltes.parcelle_id', '=', 'parcelles.id')
    ->where('parcelles.surface', '>', 0)
    ->groupBy('mois')
    ->orderBy('mois')
    ->get();
echo "   Résultats: " . $rendementByMonth->count() . "\n";
foreach ($rendementByMonth as $row) {
    echo "   Mois {$row->mois}: {$row->avg_rendement} kg/ha\n";
}

echo "\n=== Fin du test ===\n";
