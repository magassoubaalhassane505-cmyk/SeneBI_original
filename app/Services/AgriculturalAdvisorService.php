<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\Parcelle;
use App\Models\Recolte;
use App\Models\IntrantConsomme;
use Illuminate\Support\Facades\Auth;

class AgriculturalAdvisorService
{
    /**
     * Calculate widget summaries for client dashboard
     */
    public function getDashboardWidgets()
    {
        $user = Auth::user();
        
        // Total revenues from harvests
        $totalRevenues = Recolte::where('user_id', $user->id)->sum('revenu_total') ?? 0;
        
        // Total expenses from consumed inputs
        $totalDepenses = IntrantConsomme::where('user_id', $user->id)
            ->with('stock')
            ->get()
            ->sum(fn($ic) => $ic->quantite_consommee * ($ic->stock->cout_unitaire ?? 0));
        
        // Net benefit
        $beneficeNet = $totalRevenues - $totalDepenses;
        
        // Active parcels (with harvests or visits in last 90 days)
        $activeParcelles = Parcelle::where('user_id', $user->id)
            ->where(function($q) {
                $q->whereHas('recoltes', fn($sq) => $sq->where('date_recolte', '>=', now()->subDays(90)))
                  ->orWhereHas('visites', fn($sq) => $sq->where('date_visite', '>=', now()->subDays(90)));
            })
            ->count();
        
        // Total stock available
        $totalStock = Stock::where('user_id', $user->id)->sum('quantite_actuelle') ?? 0;
        
        return [
            'totalRevenues' => $totalRevenues,
            'totalDepenses' => $totalDepenses,
            'beneficeNet' => $beneficeNet,
            'activeParcelles' => $activeParcelles,
            'totalStock' => $totalStock,
        ];
    }

    /**
     * Generate automatic alerts for the client
     */
    public function generateAlerts()
    {
        $user = Auth::user();
        $alerts = [];
        
        // 1. Stock alerts - below threshold
        $criticalStocks = Stock::where('user_id', $user->id)
            ->whereColumn('quantite_actuelle', '<=', 'seuil_critique')
            ->get();
        
        foreach ($criticalStocks as $stock) {
            $alerts[] = [
                'type' => 'stock_faible',
                'severity' => 'critique',
                'message' => "Stock {$stock->nom} critique ({$stock->quantite_actuelle} kg restant)",
                'icon' => 'fas fa-exclamation-triangle',
            ];
        }
        
        // 2. Inactive parcels (no activity in 60 days)
        $inactiveParcelles = Parcelle::where('user_id', $user->id)
            ->whereDoesntHave('recoltes', fn($q) => $q->where('date_recolte', '>=', now()->subDays(60)))
            ->whereDoesntHave('visites', fn($q) => $q->where('date_visite', '>=', now()->subDays(60)))
            ->get();
        
        foreach ($inactiveParcelles as $parcelle) {
            $alerts[] = [
                'type' => 'parcelle_inactive',
                'severity' => 'avertissement',
                'message' => "Parcelle {$parcelle->nom} inactive depuis plus de 60 jours",
                'icon' => 'fas fa-seedling',
            ];
        }
        
        // 3. Expenses exceed revenues
        $depenses = IntrantConsomme::where('user_id', $user->id)
            ->with('stock')
            ->get()
            ->sum(fn($ic) => $ic->quantite_consommee * ($ic->stock->cout_unitaire ?? 0));
        $revenus = Recolte::where('user_id', $user->id)->sum('revenu_total') ?? 0;
        
        if ($depenses > $revenus && $revenus > 0) {
            $alerts[] = [
                'type' => 'depenses_superieures',
                'severity' => 'critique',
                'message' => "Dépenses ({$depenses} FCFA) supérieures aux revenus ({$revenus} FCFA)",
                'icon' => 'fas fa-money-bill-wave',
            ];
        }
        
        // 4. Crop delay alert (no harvest for current season culture)
        $parcellesEnCultures = Parcelle::where('user_id', $user->id)
            ->where('culture', '!=', null)
            ->whereDoesntHave('recoltes', fn($q) => $q->whereYear('date_recolte', now()->year))
            ->get();
        
        foreach ($parcellesEnCultures as $parcelle) {
            $alerts[] = [
                'type' => 'culture_retard',
                'severity' => 'avertissement',
                'message' => "Culture {$parcelle->culture} sur {$parcelle->nom} peut être en retard",
                'icon' => 'fas fa-clock',
            ];
        }
        
        return $alerts;
    }

    /**
     * Calculate exploitation health status
     */
    public function getExploitationHealth()
    {
        $widgets = $this->getDashboardWidgets();
        $alerts = $this->generateAlerts();
        
        $score = 0;
        
        // +1 for positive benefit
        if ($widgets['beneficeNet'] > 0) $score += 1;
        
        // +1 for active parcels
        if ($widgets['activeParcelles'] > 0) $score += 1;
        
        // +1 for stock > 50% average
        $avgStock = Stock::where('user_id', Auth::id())->avg('quantite_actuelle') ?? 0;
        $threshold = 500;
        if ($widgets['totalStock'] > $threshold) $score += 1;
        
        // -1 for critical alerts
        $criticalCount = collect($alerts)->where('severity', 'critique')->count();
        $score -= $criticalCount;
        
        if ($score >= 2) return 'bon';
        if ($score <= 0) return 'critique';
        return 'moyen';
    }

    /**
     * Get stock movement history
     */
    public function getStockMovements($limit = 20)
    {
        return StockMouvement::whereHas('stock', fn($q) => $q->where('user_id', Auth::id()))
            ->with('stock')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn($m) => [
                'date' => $m->created_at->format('d/m/Y H:i'),
                'type' => $m->type,
                'quantity' => $m->quantite,
                'stock_nom' => $m->stock->nom,
                'user' => $m->user->name ?? 'Système',
            ]);
    }

    /**
     * Calculate stock duration estimate
     */
    public function getStockDurationEstimate()
    {
        $user = Auth::user();
        
        // Average daily consumption
        $consumptionLast30 = IntrantConsomme::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('quantite');
        
        $dailyConsumption = $consumptionLast30 > 0 ? $consumptionLast30 / 30 : 1;
        
        // Days remaining
        $totalStock = Stock::where('user_id', $user->id)->sum('quantite_actuelle') ?? 0;
        $daysRemaining = $dailyConsumption > 0 ? floor($totalStock / $dailyConsumption) : 0;
        
        return [
            'daily_consumption' => $dailyConsumption,
            'days_remaining' => $daysRemaining,
            'critical_stock_count' => Stock::where('user_id', $user->id)
                ->whereColumn('quantite_actuelle', '<=', 'seuil_critique')
                ->count(),
        ];
    }

    /**
     * Analyze profitability by crop
     */
    public function getProfitabilityByCulture()
    {
        $user = Auth::user();
        
        return Recolte::where('user_id', $user->id)
            ->selectRaw('culture, SUM(revenu_total) as revenu_total, SUM(couts_totaux) as couts_totaux, SUM(benefice_net) as benefice_net')
            ->groupBy('culture')
            ->orderByDesc('benefice_net')
            ->get();
    }

    /**
     * Get best performing crop
     */
    public function getBestCulture()
    {
        $profitability = $this->getProfitabilityByCulture();
        
        return $profitability->first()?->culture ?? null;
    }

    /**
     * Get agricultural recommendations
     */
    public function getRecommendations()
    {
        $alerts = $this->generateAlerts();
        $widgets = $this->getDashboardWidgets();
        $recommendations = [];
        
        foreach ($alerts as $alert) {
            switch ($alert['type']) {
                case 'stock_faible':
                    $recommendations[] = "Réapprovisionnement recommandé: {$alert['message']}";
                    break;
                case 'parcelle_inactive':
                    $recommendations[] = "Activité recommandée sur {$alert['message']}";
                    break;
                case 'depenses_superieures':
                    $recommendations[] = "Revoyez votre stratégie de prix ou réduisez les coûts";
                    break;
                case 'culture_retard':
                    $recommendations[] = "Vérifiez le calendrier de récolte pour {$alert['message']}";
                    break;
            }
        }
        
        // Add positive recommendation if no issues
        if (empty($recommendations) && $widgets['beneficeNet'] > 0) {
            $recommendations[] = "Continuer la stratégie actuelle - performance optimale";
        }
        
        return $recommendations;
    }
}