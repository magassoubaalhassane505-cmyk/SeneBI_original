<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <title>SeneBI — Stocks</title>
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/stocks.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/visual-harmony.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  </head>
  <body data-page="stocks">
    <div class="app">
      @include('header-client')  
     

      <main class="container">
        <div class="page-title">
          <div>
            <h1>Suivi des Stocks & Intrants</h1>
            <p>Inventaire en temps reel et alertes de seuil critique</p>
          </div>
        </div>

        <div id="stocksLocalAlert" class="alert-banner">
          <div class="alert-main">
            <div class="alert-icon">!</div>
            <div>
              <h3>Alerte Stock Critique</h3>
              <p id="stocksLocalAlertText">Aucun intrant critique pour le moment.</p>
              <div class="alert-chip" id="criticalChip">-</div>
            </div>
          </div>
        </div>

        <section class="grid cards-3" id="inventoryCards">
          @php
            $totalIntrants = $stocks->count();
            $totalValue = $stocks->sum(fn($s) => $s->quantite_actuelle * $s->cout_unitaire);
            $criticalCount = $stocks->filter(fn($s) => $s->quantite_actuelle <= $s->seuil_critique)->count();
          @endphp
          <article class="card kpi-card {{ $criticalCount > 0 ? 'critical-alert' : '' }}">
            <div class="card-header">
              <p class="card-title">Total Intrants</p>
              <div class="card-icon" aria-hidden="true"><i class="fas fa-boxes"></i></div>
            </div>
            <div class="kpi-value">{{ $totalIntrants }}</div>
            <div class="kpi-sub">
              <span>Nombre d'articles en stock</span>
              <span class="muted">Tous intrants confondus</span>
            </div>
          </article>
          <article class="card kpi-card">
            <div class="card-header">
              <p class="card-title">Valeur Totale du Stock</p>
              <div class="card-icon" aria-hidden="true"><i class="fas fa-coins"></i></div>
            </div>
            <div class="kpi-value">{{ number_format($totalValue, 0, ',', ' ') }} <span class="muted" style="font-size:14px;font-weight:700;">FCFA</span></div>
            <div class="kpi-sub">
              <span>Estimation financière</span>
              <span class="muted">Quantité × coût unitaire</span>
            </div>
          </article>
          <article class="card kpi-card {{ $criticalCount > 0 ? 'critical-alert' : '' }}">
            <div class="card-header">
              <p class="card-title">Alertes Critiques</p>
              <div class="card-icon icon-box red" aria-hidden="true"><i class="fas fa-bell"></i></div>
            </div>
            <div class="kpi-value">{{ $criticalCount }}</div>
            <div class="kpi-sub">
              <span>Articles sous seuil</span>
              <span class="muted">Nécessitent un réapprovisionnement</span>
            </div>
          </article>
        </section>

        <section class="grid cards-1" id="stocksConsumeSection">
          <article class="card">
            <div class="card-header">
              <div>
                <h3 class="section-title">Declarer une consommation</h3>
                <div class="small muted">Deduit automatiquement du stock</div>
              </div>
              <span class="tag warn">Logistique</span>
            </div>

            <form class="form" id="consumeForm">
              <div class="form-row form-row--3">
                <div class="field">
                  <label class="field-label" for="consumeRegion">Région</label>
                  <select id="consumeRegion" required>
                    <option value="">Sélectionner une région</option>
                    <option value="Kayes">Kayes</option>
                    <option value="Bamako">Bamako</option>
                    <option value="Koulikoro">Koulikoro</option>
                    <option value="Sikasso">Sikasso</option>
                    <option value="Ségou">Ségou</option>
                    <option value="Mopti">Mopti</option>
                  </select>
                </div>
                <div class="field">
                  <label class="field-label" for="consumeParcel">Parcelle</label>
                  <select id="consumeParcel" required>
                    <option value="">Choisir une parcelle</option>
                    @foreach($parcelles ?? [] as $parcelle)
                    <option value="{{ $parcelle->nom }}">{{ $parcelle->nom }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="field">
                  <label class="field-label" for="consumeDate">Date</label>
                  <input id="consumeDate" type="date" value="{{ date('Y-m-d') }}" required />
                </div>
              </div>
              <div class="form-row form-row--2">
<div class="field">
                   <label class="field-label" for="consumeItem">Intrant</label>
                   <select id="consumeItem" required>
                     <option value="">Sélectionner un intrant</option>
                     <option value="NPK">NPK</option>
                     <option value="Semence Riz">Semences de riz</option>
                     <option value="Semence Maïs">Semences de maïs</option>
                     <option value="Semence Coton">Semences de coton</option>
                     <option value="Pesticide">Pesticides</option>
                     <option value="Herbicide">Herbicides</option>
                   </select>
                 </div>
                <div class="field">
                  <label class="field-label" for="consumeQty">Quantité (kg)</label>
                  <input id="consumeQty" type="number" min="1" step="1" placeholder="ex: 50" required />
                </div>
              </div>
              <div class="form-actions">
                <button class="btn" type="submit">Enregistrer</button>
                <a class="btn secondary" href="{{ route('client.dashboard') }}">Retour Dashboard</a>
              </div>
              <div class="footer-note">Si un stock passe sous le seuil critique, une alerte rouge apparait.</div>
              <div class="form-feedback" id="consumeFeedback" aria-live="polite"></div>
            </form>
          </article>
        </section>

        <section class="grid cards-1" id="stocksReapproSection">
          <article class="card">
            <div class="card-header">
              <div>
                <h3 class="section-title">Réapprovisionner le stock</h3>
                <div class="small muted">Ajout de nouvelle quantité d'intrants</div>
              </div>
              <span class="tag good">Approvisionnement</span>
            </div>

<form class="form" id="reapproForm">
                <div class="form-row form-row--2">
                 <div class="field">
                   <label class="field-label" for="reapproItem">Intrant</label>
                   <select id="reapproItem" required>
                     <option value="">Sélectionner un intrant</option>
                     <option value="NPK">NPK</option>
                     <option value="Semence Riz">Semences de riz</option>
                     <option value="Semence Maïs">Semences de maïs</option>
                     <option value="Semence Coton">Semences de coton</option>
                     <option value="Pesticide">Pesticides</option>
                     <option value="Herbicide">Herbicides</option>
                   </select>
                 </div>
                </div>
                <div class="form-row form-row--3">
                 <div class="field">
                   <label class="field-label" for="reapproQty">Quantité ajoutée (kg)</label>
                   <input id="reapproQty" type="number" min="0.01" step="1" placeholder="ex: 200" required />
                 </div>
                 <div class="field">
                   <label class="field-label" for="reapproSeuil">Seuil critique (kg)</label>
                   <input id="reapproSeuil" type="number" min="0" step="1" placeholder="ex: 500" value="100" />
                 </div>
                 <div class="field">
                   <label class="field-label" for="reapproUnitCost">Coût unitaire (FCFA / kg)</label>
                   <input id="reapproUnitCost" type="number" min="0" step="1" placeholder="ex: 15000" />
                 </div>
               </div>
               <div class="form-row form-row--2">
                 <div class="field">
                   <label class="field-label" for="reapproTotalCost">Coût total (FCFA)</label>
                   <input id="reapproTotalCost" type="number" min="0" step="1" placeholder="ex: 3000000" />
                   <span style="font-size:11px;color:#64748b;margin-top:2px;">Se calcule automatiquement si le coût unitaire est renseigné.</span>
                 </div>
                 <div class="field">
                   <label class="field-label" for="reapproDate">Date</label>
                   <input id="reapproDate" type="date" value="{{ date('Y-m-d') }}" required />
                 </div>
               </div>
               <div class="form-row">
                 <div class="field">
                   <label class="field-label" for="reapproObs">Observation / Référence</label>
                   <input id="reapproObs" type="text" placeholder="ex: Facture N°2026-001, Fournisseur X" />
                 </div>
               </div>
               <div class="form-actions">
                 <button class="btn" type="submit">Enregistrer le réapprovisionnement</button>
               </div>
               <div class="footer-note">La quantité ajoutée est immédiatement intégrée au stock et l'opération est enregistrée dans l'historique.</div>
               <div class="form-feedback" id="reapproFeedback" aria-live="polite"></div>
             </form>
          </article>
        </section>

        <section class="grid cards-2 stocks-extra-row">
          <article class="card stock-gauge-card">
            <h3 class="section-title">Remplissage global (capacite)</h3>
            <p class="small muted">Vue jauge : stock actuel vs capacite totale (engrais + semences)</p>
            <div class="gauge-chart-wrap">
              <canvas id="stockGaugeChart" aria-label="Jauge de remplissage du stock"></canvas>
              <div class="gauge-center-label" id="stockGaugePct">0%</div>
            </div>
          </article>
          <article class="card">
            <h3 class="section-title">Top 3 consommateurs d'intrants (mois en cours)</h3>
            <p class="small muted" id="topConsumersNote">Classement par parcelle sur le mois civil actuel.</p>
            <table class="table table-compact top-consumers-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Parcelle</th>
                  <th>Volume (kg)</th>
                </tr>
              </thead>
              <tbody id="topConsumersBody">
                @forelse($topConsumers as $index => $consumer)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $consumer['parcelle'] ?? $consumer->parcelle ?? 'Inconnue' }}</strong></td>
                    <td>{{ number_format($consumer['volume'] ?? $consumer->volume ?? 0, 0, ',', ' ') }} kg</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3" style="text-align:center;padding:12px;color:#64748b;font-weight:700;">Aucune consommation ce mois-ci</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
            <p class="small muted top-consumers-fallback" id="topConsumersFallback" {{ $topConsumers && $topConsumers->count() ? 'hidden' : '' }}>
              Pas de consommation ce mois-ci — affichage du classement sur toutes les donnees disponibles.
            </p>
          </article>
        </section>

        <section class="card">
          <h3 class="section-title">Niveau des Stocks par Intrant</h3>
          <div class="chart-wrap">
            <canvas id="stocksChart"></canvas>
          </div>
        </section>

        <section class="card">
          <div style="overflow:auto;">
            <table class="table table-large">
              <thead>
                <tr>
                  <th>Nom</th>
                  <th>Type</th>
                  <th>Stock Actuel</th>
                  <th>Seuil Critique</th>
                  <th>Cout Unitaire</th>
                  <th>Statut</th>
                </tr>
              </thead>
              <tbody id="stockTableBody">
                @foreach($stocks as $stock)
@php
                      $isCritical = $stock->quantite_actuelle <= $stock->seuil_critique;
                      $isLow = $stock->quantite_actuelle > $stock->seuil_critique && $stock->quantite_actuelle <= ($stock->seuil_critique * 2);
                      $ratio = $stock->seuil_critique > 0 ? ($stock->quantite_actuelle / ($stock->seuil_critique * 4)) * 100 : 100;
                      $progressPercent = min(100, $ratio);
                      $statusClass = $isCritical ? 'critical' : ($isLow ? 'warning' : 'ok');
                      $statusText = $isCritical 
                        ? 'Critique (' . round(($stock->quantite_actuelle / $stock->seuil_critique) * 100) . '%)' 
                        : ($isLow 
                            ? 'Faible (' . round(($stock->quantite_actuelle / ($stock->seuil_critique * 4)) * 100) . '%)' 
                            : 'Normal (' . round(($stock->quantite_actuelle / ($stock->seuil_critique * 4)) * 100) . '%)');
                    @endphp
                    <tr>
                      <td><strong>{{ $stock->nom }}</strong></td>
                      <td><span class="stock-type">{{ $stock->type }}</span></td>
                      <td>
                        <div>{{ number_format($stock->quantite_actuelle, 0, ',', ' ') }} kg</div>
                        <div class="stock-progress-bar">
                          <div class="stock-progress-fill {{ $statusClass }}" style="width: {{ $progressPercent }}%;"></div>
                        </div>
                      </td>
                      <td>{{ number_format($stock->seuil_critique, 0, ',', ' ') }} kg</td>
                      <td>{{ number_format($stock->cout_unitaire, 0, ',', ' ') }} FCFA</td>
                      <td class="{{ $isCritical ? 'status-bad' : ($isLow ? 'status-warning' : 'status-ok') }}">
                        {{ $statusText }}
                      </td>
                    </tr>
                @endforeach
@if($stocks->isEmpty())
                   <tr>
                     <td colspan="6" style="text-align:center; color:#9ca3af; padding: 20px;">Aucun stock disponible.</td>
                   </tr>
                 @endif
              </tbody>
            </table>
          </div>
        </section>

        <section class="grid cards-2" style="margin-top: 20px;">
          <article class="card">
            <h3 class="section-title">Historique des Mouvements</h3>
            <div style="overflow:auto; max-height: 360px;">
              <table class="table table-compact">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Intrant</th>
                    <th>Type</th>
                    <th>Quantite</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($mouvements as $m)
                    <tr>
                      <td>{{ \Carbon\Carbon::parse($m->date_mouvement)->format('d/m/Y H:i') }}</td>
                      <td>{{ $m->stock->nom ?? 'N/A' }}</td>
                      <td>
                        @php
                          $typeClass = match($m->type) {
                            'entree' => 'ok',
                            'ajustement' => 'muted',
                            'utilisation' => 'warning',
                            default => 'muted',
                          };
                          $typeLabel = match($m->type) {
                            'entree' => 'Entree',
                            'ajustement' => 'Ajustement',
                            'utilisation' => 'Consommation',
                            default => ucfirst($m->type),
                          };
                        @endphp
                        <span class="badge {{ $typeClass }}">{{ $typeLabel }}</span>
                      </td>
                      <td>{{ number_format($m->quantite, 0, ',', ' ') }} kg</td>
                    </tr>
                  @endforeach
@if($mouvements->isEmpty())
                     <tr>
                       <td colspan="4" style="text-align:center; color:#9ca3af; padding: 20px;">Aucun mouvement disponible.</td>
                     </tr>
                   @endif
                </tbody>
              </table>
            </div>
          </article>

          <article class="card">
            <h3 class="section-title">Prevision d'Epuisement des Stocks</h3>
            <p class="small muted" style="margin-bottom: 12px;">Estimation basee sur la consommation moyenne des 30 derniers jours.</p>
            <div style="display: flex; flex-direction: column; gap: 10px;">
              @foreach($stockForecasts as $forecast)
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; background: #f9fafb; border-radius: 8px; border-left: 4px solid {{ $forecast['est_critique'] ? '#ef4444' : '#10b981' }};">
                  <div>
                    <div style="font-weight: 600; font-size: 14px; color: #111827;">{{ $forecast['nom'] }}</div>
                    <div class="small muted">Stock restant : {{ number_format($forecast['quantite'], 0, ',', ' ') }} kg</div>
                  </div>
                  <div style="text-align: right;">
                    <div style="font-weight: 700; font-size: 16px; color: {{ $forecast['jours_restants'] <= 7 ? '#ef4444' : ($forecast['jours_restants'] <= 30 ? '#f59e0b' : '#10b981') }};">
                      {{ $forecast['jours_restants'] <= 0 ? 'Epuise' : $forecast['jours_restants'] . ' j' }}
                    </div>
                    <div class="small muted">restants</div>
                  </div>
                </div>
              @endforeach
            </div>
          </article>
        </section>

        <section class="card" style="margin-top: 20px;">
          <h3 class="section-title">Statistiques de Consommation Mensuelle</h3>
          <div class="chart-wrap" style="height: 280px;">
            <canvas id="monthlyConsumptionChart"></canvas>
          </div>
        </section>
        
        <section class="card" style="margin-top: 20px;">
          <h3 class="section-title">Timeline des Mouvements Récents</h3>
          <div id="stocksTimeline" style="max-height: 400px; overflow-y: auto;"></div>
        </section>

      </main>
      @include('partials.footer-client')
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
      window.SeneBI_SERVER = {
        useDb: true,
        csrf: @json(csrf_token()),
        apiBase: @json(url('/client/api')),
        stocks: @json($stocks),
        intrants: @json($intrants),
        mouvements: @json($mouvements),
        stockForecasts: @json($stockForecasts),
        topConsumers: @json($topConsumers),
        monthlyConsumption: @json($monthlyConsumption),
      };
    </script>
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <script src="{{ asset('assets/js/core.js') }}"></script>
    <script src="{{ asset('assets/js/stocks-db-sync.js') }}"></script>
    <script src="{{ asset('assets/js/stocks-consumption.js') }}"></script>
    <script src="{{ asset('assets/js/stocks-reapprovisionnement.js') }}"></script>
    <script src="{{ asset('assets/js/stocks-render.js') }}"></script>
    
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const nav = document.querySelector('[data-senebi-nav]');
        if (nav && !nav.innerHTML.trim()) {
          nav.innerHTML = `
            <a href="{{ route('client.dashboard') }}" class="nav-link">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 13h8V3H3v10z"/>
                <path d="M13 21h8V11h-1v10z"/>
                <path d="M13 3h8v6h-8V3z"/>
                <path d="M3 17h8v4H3v-4z"/>
              </svg>
              <span>Dashboard</span>
            </a>
            <a href="{{ route('client.parcelles') }}" class="nav-link">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 6l9-3 9 3v12l-9 3-9-3V6z"/>
                <path d="M12 3v18"/>
                <path d="M3 6l9 3 9-3"/>
              </svg>
              <span>Parcelles</span>
            </a>
            <a href="{{ route('client.stocks') }}" class="nav-link active">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4a2 2 0 0 0 1-1.73z"/>
                <path d="M3.3 7l8.7 5 8.7-5"/>
                <path d="M12 22V12"/>
              </svg>
              <span>Stocks</span>
            </a>
            <a href="{{ route('client.rentabilite') }}" class="nav-link">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 1v22"/>
                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/>
              </svg>
              <span>Rentabilité</span>
            </a>
          `;
        }
        
        const authPills = document.getElementById('authPills');
        if (authPills) {
          authPills.removeAttribute('hidden');
          authPills.innerHTML = `
            <a class="pill user-pill user-pill--link" id="authUserName" href="{{ route('client.compte') }}">{{ Auth::user()->name ?? 'Utilisateur' }}</a>
            <button class="pill auth-logout" id="globalLogoutBtn" type="button">Deconnexion</button>
          `;
        }
      });
    </script>
  </body>
</html>
