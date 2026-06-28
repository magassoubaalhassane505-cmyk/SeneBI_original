<?php

namespace App\Http\Controllers;

use App\Models\Parcelle;
use App\Models\Stock;
use App\Models\Recolte;
use App\Models\Intrant;
use App\Models\IntrantConsomme;
use App\Models\Visite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    // Affiche le tableau de bord de l'agriculteur (Sidi)
    public function clientDashboard() {
        $user = Auth::user();
        $derniereVisite = Visite::where('user_id', $user->id)->latest()->first();
        
        $advisor = new \App\Services\AgriculturalAdvisorService();
        $widgets = $advisor->getDashboardWidgets();
        $alerts = $advisor->generateAlerts();
        $health = $advisor->getExploitationHealth();
        $recommendations = $advisor->getRecommendations();

        $totalRecolteQte = Recolte::where('user_id', $user->id)->sum('quantite') ?? 0;
        $totalCA = Recolte::where('user_id', $user->id)->sum('revenu_total') ?? 0;
        $totalCouts = IntrantConsomme::where('user_id', $user->id)
            ->with('stock')
            ->get()
            ->sum(fn($ic) => $ic->quantite_consommee * ($ic->stock->cout_unitaire ?? 0));
        $beneficeNet = $totalCA - $totalCouts;

        $hectaresActifs = Parcelle::where('user_id', $user->id)->sum('surface') ?? 0;
        $surfaceRecoltee = Recolte::where('user_id', $user->id)
            ->with('parcelle')
            ->get()
            ->sum(fn($r) => $r->parcelle->surface ?? 0);
        $rendementMoyen = $surfaceRecoltee > 0 ? ($totalRecolteQte / $surfaceRecoltee) : 0;

        $now = now();
        $debutMoisActuel = $now->copy()->startOfMonth();
        $debutMoisDernier = $now->copy()->subMonth()->startOfMonth();
        $finMoisDernier = $now->copy()->subMonth()->endOfMonth();

        $caMoisActuel = Recolte::where('user_id', $user->id)
            ->where('date_recolte', '>=', $debutMoisActuel)
            ->sum('revenu_total') ?? 0;
        $caMoisDernier = Recolte::where('user_id', $user->id)
            ->whereBetween('date_recolte', [$debutMoisDernier, $finMoisDernier])
            ->sum('revenu_total') ?? 0;
        $caEvolution = $caMoisDernier > 0 ? (($caMoisActuel - $caMoisDernier) / $caMoisDernier) * 100 : 0;

        $haMoisActuel = Parcelle::where('user_id', $user->id)
            ->where('created_at', '>=', $debutMoisActuel)
            ->sum('surface') ?? 0;
        $haMoisDernier = Parcelle::where('user_id', $user->id)
            ->whereBetween('created_at', [$debutMoisDernier, $finMoisDernier])
            ->sum('surface') ?? 0;
        $haEvolution = $haMoisDernier > 0 ? (($haMoisActuel - $haMoisDernier) / $haMoisDernier) * 100 : 0;

        $rendementMoisActuel = $surfaceRecoltee > 0 ? ($totalRecolteQte / $surfaceRecoltee) : 0;
        $rendementMoisDernier = 0;
        $recoltesMoisDernier = Recolte::where('user_id', $user->id)
            ->whereBetween('date_recolte', [$debutMoisDernier, $finMoisDernier])
            ->get();
        $surfaceMoisDernier = $recoltesMoisDernier->sum(fn($r) => $r->parcelle->surface ?? 0);
        $qteMoisDernier = $recoltesMoisDernier->sum('quantite');
        if ($surfaceMoisDernier > 0) {
            $rendementMoisDernier = $qteMoisDernier / $surfaceMoisDernier;
        }
        $rendementEvolution = $rendementMoisDernier > 0 ? (($rendementMoisActuel - $rendementMoisDernier) / $rendementMoisDernier) * 100 : 0;

        // Graphiques réels
        $prixCultures = \App\Models\Intrant::selectRaw('LOWER(nom) as culture_lower, prix')
            ->whereIn('nom', ['Riz', 'Maïs', 'Coton', 'Urée', 'NPK 15-15-15', 'Semences Maïs', 'Semences Riz'])
            ->get()
            ->groupBy('culture_lower')
            ->map(fn($items) => $items->avg('prix'));

        if (!$prixCultures->has('riz')) {
            $prixCultures['riz'] = 0;
        }
        if (!$prixCultures->has('maïs') && !$prixCultures->has('mais')) {
            $prixCultures['maïs'] = 0;
        }
        if (!$prixCultures->has('coton')) {
            $prixCultures['coton'] = 0;
        }

        if (empty($culturesLabels)) {
            $culturesLabels = [];
            $culturesData = [];
        }
        if (!$prixCultures->has('maïs') && !$prixCultures->has('mais')) {
            $prixCultures['maïs'] = 0;
        }
        if (!$prixCultures->has('coton')) {
            $prixCultures['coton'] = 0;
        }

        $culturesLabels = [];
        $culturesData = [];
        $userRecoltes = Recolte::where('user_id', $user->id)
            ->selectRaw('culture, SUM(quantite) as total_qte')
            ->groupBy('culture')
            ->get();
        foreach ($userRecoltes as $rc) {
            $culturesLabels[] = $rc->culture;
            $culturesData[] = $rc->total_qte;
        }
        if (empty($culturesLabels)) {
            $culturesLabels = [];
            $culturesData = [];
        }

        $stockCritical = \App\Models\Stock::where('user_id', $user->id)
            ->whereColumn('quantite_actuelle', '<=', 'seuil_critique')
            ->get(['nom', 'quantite_actuelle', 'seuil_critique']);

        return view('client-dashboard', compact(
            'derniereVisite',
            'widgets',
            'alerts',
            'health',
            'recommendations',
            'totalRecolteQte',
            'totalCA',
            'totalCouts',
            'beneficeNet',
            'hectaresActifs',
            'rendementMoyen',
            'caEvolution',
            'haEvolution',
            'rendementEvolution',
            'prixCultures',
            'culturesLabels',
            'culturesData',
            'stockCritical'
        ));
    }

    // Affiche le calculateur de rentabilité
    public function rentabilite() {
        $user = Auth::user();
        
        // Récupérer les récoltes du client
        $recoltes = $user->recoltes()->with('parcelle')->latest()->get();
        
        // Calculer les statistiques de rentabilité
        $totalCA = $recoltes->sum('revenu_total');
        $totalCouts = $recoltes->sum('couts_totaux');
        $totalBenefice = $recoltes->sum('benefice_net');
        
        // Calculer la marge moyenne
        $margeMoyenne = $totalCA > 0 ? ($totalBenefice / $totalCA) * 100 : 0;
        
        // Récupérer les parcelles pour le sélecteur
        $parcelles = $user->parcelles()->orderBy('nom')->get();
        
        $rentabiliteHarvests = $recoltes->map(fn($r) => [
          'date' => $r->date_recolte->format('d/m/Y'),
          'parcel' => $r->parcelle->nom ?? 'N/A',
          'qtyKg' => $r->quantite,
          'unitPrice' => $r->prix_unitaire,
          'costs' => $r->couts_totaux,
          'revenue' => $r->revenu_total,
          'profit' => $r->benefice_net,
          'culture' => $r->culture,
        ]);
        
        return view('rentabilite', compact(
            'recoltes',
            'totalCA',
            'totalCouts',
            'totalBenefice',
            'margeMoyenne',
            'parcelles',
            'rentabiliteHarvests'
        ));
    }

    // Affiche la page de notifications client
    public function notifications()
    {
        return view('client.notifications');
    }

    // Affiche la gestion des parcelles
    public function parcelles() {
        $user = Auth::user();
        $parcelles = $user->parcelles()->orderBy('nom')->get();

        $parcelleStats = $parcelles->map(function ($p) use ($user) {
            $recoltes = $p->recoltes()->sum('quantite');
            $surface = (float) $p->surface;
            $rendement = $surface > 0 ? ($recoltes / $surface) : 0;
            $benefice = $p->recoltes()->sum('benefice_net');
            $visites = $p->visites()->count();
            $stockScore = 0;

            $stocks = Stock::where('user_id', $user->id)->get();
            foreach ($stocks as $stock) {
                $ratio = $stock->seuil_critique > 0 ? ($stock->quantite_actuelle / $stock->seuil_critique) : 1;
                $stockScore += $ratio;
            }
            $stockScore = $stocks->count() > 0 ? $stockScore / $stocks->count() : 0.5;

            $score = 0;
            if ($rendement >= 1.0) $score += 40;
            elseif ($rendement >= 0.5) $score += 25;
            else $score += 10;

            if ($benefice >= 500000) $score += 30;
            elseif ($benefice >= 0) $score += 15;
            else $score += 0;

            $score += $stockScore * 30;

            if ($visites >= 2) $score += 10;

            if ($score >= 75) {
                $badge = 'Excellent';
                $badgeClass = 'perf-badge excellent';
            } elseif ($score >= 40) {
                $badge = 'Moyen';
                $badgeClass = 'perf-badge moyen';
            } else {
                $badge = 'Risque';
                $badgeClass = 'perf-badge risque';
            }

            return [
                'id' => $p->id,
                'nom' => $p->nom,
                'culture' => $p->culture,
                'surface' => $surface,
                'rendement' => round($rendement, 2),
                'benefice' => round($benefice, 0),
                'badge' => $badge,
                'badgeClass' => $badgeClass,
            ];
        });

        return view('parcelles', compact('parcelles', 'parcelleStats'));
    }

    // Affiche la gestion des stocks du client
    public function stocks() {
        app(\App\Http\Controllers\ClientApiController::class)->stocksIndex();
        $stocks = Stock::where('user_id', Auth::id())->orderBy('nom')->get();
        $mouvements = \App\Models\StockMouvement::whereHas('stock', fn($q) => $q->where('user_id', Auth::id()))
            ->with('stock')
            ->orderByDesc('date_mouvement')
            ->limit(50)
            ->get();

        // Récupérer les prix depuis la table intrants
        $intrants = Intrant::all()->keyBy('nom');

        // Créer des stocks par défaut si l'utilisateur n'en a pas ou ajouter les manquants
        $defaults = [
            ['nom' => 'Urée', 'type' => 'Engrais', 'quantite_actuelle' => 520, 'seuil_critique' => 500, 'cout_unitaire' => $intrants['Urée']->prix ?? 15000],
            ['nom' => 'NPK', 'type' => 'Engrais', 'quantite_actuelle' => 900, 'seuil_critique' => 450, 'cout_unitaire' => $intrants['NPK 15-15-15']->prix ?? 18000],
            ['nom' => 'Semence Maïs', 'type' => 'Semence', 'quantite_actuelle' => 240, 'seuil_critique' => 100, 'cout_unitaire' => $intrants['Semences Maïs']->prix ?? 800],
            ['nom' => 'Semence Coton', 'type' => 'Semence', 'quantite_actuelle' => 1250, 'seuil_critique' => 500, 'cout_unitaire' => 1200],
            ['nom' => 'Semence Riz', 'type' => 'Semence', 'quantite_actuelle' => 600, 'seuil_critique' => 300, 'cout_unitaire' => $intrants['Semences Riz']->prix ?? 1000],
        ];
        
        foreach ($defaults as $default) {
            $existingStock = $stocks->firstWhere('nom', $default['nom']);
            if (!$existingStock) {
                Stock::create([...$default, 'user_id' => Auth::id()]);
            }
        }
        
        // Recharger les stocks après création
        $stocks = Stock::where('user_id', Auth::id())->orderBy('nom')->get();

        // Calculer les prévisions d'épuisement
        $consumptionLast30 = \App\Models\IntrantConsomme::where('user_id', Auth::id())
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('quantite_consommee');
        $dailyConsumption = $consumptionLast30 > 0 ? $consumptionLast30 / 30 : 1;
        $stockForecasts = $stocks->map(fn($s) => [
            'nom' => $s->nom,
            'quantite' => $s->quantite_actuelle,
            'jours_restants' => max(0, (int) floor($s->quantite_actuelle / $dailyConsumption)),
            'est_critique' => $s->quantite_actuelle <= $s->seuil_critique,
        ]);

        return view('stocks', compact('stocks', 'intrants', 'mouvements', 'stockForecasts'));
    }

    // Affiche le profil et les informations du compte
    public function compte() {
        $user = Auth::user();
        
        return view('compte-client', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password'
        ], [
            'current_password.required' => 'Le mot de passe actuel est obligatoire',
            'new_password.required' => 'Le nouveau mot de passe est obligatoire',
            'new_password.min' => 'Le nouveau mot de passe doit contenir au moins 6 caractères',
            'confirm_password.required' => 'La confirmation est obligatoire',
            'confirm_password.same' => 'La confirmation ne correspond pas au nouveau mot de passe'
        ]);

        $user = Auth::user();

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'error' => 'Mot de passe actuel incorrect'
            ], 400);
        }

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe mis à jour avec succès'
        ]);
    }
}