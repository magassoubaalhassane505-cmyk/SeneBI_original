<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SeneBI — Dashboard</title>
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/region-filter.css') }}" />
    
    <!-- Styles pour les sections restaurées du portail manager -->
    <style>
      /* Statistiques Nationales */
      .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-top: 16px;
      }
      
      .stat-item {
        text-align: center;
        padding: 16px;
        background: #f8fafc;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
      }
      
      .stat-number {
        font-size: 24px;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 4px;
      }
      
      .stat-label {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
      }
      
      /* Alertes Critiques */
      .alerts-list {
        margin-top: 16px;
      }
      
      .alert-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 8px;
        border-left: 4px solid;
      }
      
      .alert-item.critical {
        background: #fef2f2;
        border-left-color: #ef4444;
      }
      
      .alert-item.warning {
        background: #fffbeb;
        border-left-color: #f59e0b;
      }
      
      .alert-item.info {
        background: #f0f9ff;
        border-left-color: #3b82f6;
      }
      
      .alert-icon {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 12px;
        flex-shrink: 0;
      }
      
      .alert-item.critical .alert-icon {
        background: #ef4444;
        color: white;
      }
      
      .alert-item.warning .alert-icon {
        background: #f59e0b;
        color: white;
      }
      
      .alert-item.info .alert-icon {
        background: #3b82f6;
        color: white;
      }
      
      .alert-content {
        flex: 1;
      }
      
      .alert-title {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 2px;
      }
      
      .alert-desc {
        font-size: 13px;
        color: #64748b;
        margin-bottom: 2px;
      }
      
      .alert-time {
        font-size: 11px;
        color: #94a3b8;
      }
      
      /* Tendances et Projections */
      .trends-section {
        margin-top: 24px;
      }
      
      .trends-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-top: 16px;
      }
      
      .trend-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
      }
      
      .trend-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
      }
      
      .trend-header h4 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
      }
      
      .trend-value {
        font-size: 12px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 12px;
      }
      
      .trend-value.positive {
        background: #dcfce7;
        color: #166534;
      }
      
      .trend-chart {
        display: flex;
        align-items: end;
        gap: 4px;
        height: 60px;
        margin-bottom: 8px;
      }
      
      .trend-bar {
        flex: 1;
        background: linear-gradient(to top, #10b981, #34d399);
        border-radius: 2px 2px 0 0;
        min-height: 4px;
      }
      
      .trend-labels {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        color: #64748b;
      }
      
      /* Responsive */
      @media (max-width: 768px) {
        .stats-grid {
          grid-template-columns: 1fr;
        }
        
        .trends-grid {
          grid-template-columns: 1fr;
        }
      }
      
      /* Styles pour la navigation active */
      .manager-nav a.active {
        background: #dcfce7;
        color: #14532d;
        font-weight: 600;
        border-left: 3px solid #10b981;
        border-radius: 0 8px 8px 0;
        transition: all 0.2s ease;
      }
      
      .manager-nav a.active:hover {
        background: #bbf7d0;
        border-left-color: #059669;
      }
    </style>
  </head>
  <body data-page="dashboard">
    <div class="app">
      
    @include('header-manager')

      <main class="container">
        <div class="page-title">
          <div>
            <h1>Tableau de Bord Analytique</h1>
            <p>Vue d'ensemble des performances agricoles, avec analyse des tendances et alertes opérationnelles.</p>
          </div>
          <div class="region-selector">
            <label for="regionSelect" class="region-label">Région</label>
            <select id="regionSelect" class="region-dropdown">
              <option value="all">Toutes les régions</option>
              <option value="bko">Bamako</option>
              <option value="kay">Kayes</option>
              <option value="kou">Koulikoro</option>
              <option value="seg">Ségou</option>
              <option value="sik">Sikasso</option>
              <option value="mop">Mopti</option>
              <option value="tom">Tombouctou</option>
              <option value="gao">Gao</option>
              <option value="kid">Kidal</option>
            </select>
          </div>
        </div>

        <div id="stockAlert" class="alert-banner">
          <div id="stockAlertText">Alerte Stock</div>
          <a class="btn danger" href="{{ route('manager.stocks') }}">Voir le stock</a>
        </div>

        <section class="grid kpis">
          <article class="card">
            <div class="card-header">
              <p class="card-title">Total Récolté</p>
              <div class="card-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 2v20"/><path d="M7 6c2 2 2 4 0 6"/><path d="M17 6c-2 2-2 4 0 6"/><path d="M7 12c2 2 2 4 0 6"/><path d="M17 12c-2 2-2 4 0 6"/>
                </svg>
              </div>
            </div>
            <div class="kpi-value"><span id="total-production">0</span> <span class="muted" style="font-size:14px;font-weight:700;">kg</span></div>
            <div class="kpi-sub">
              <span>+12.5% vs. période précédente</span>
              <span class="muted">Basé sur les saisies de récoltes</span>
            </div>
          </article>

          <article class="card">
            <div class="card-header">
              <p class="card-title">Chiffre d'Affaires estimé</p>
              <div class="card-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/><path d="M12 7v10"/><path d="M9.5 9.5c.6-1 4.4-1 5 0"/><path d="M9.5 14.5c.6 1 4.4 1 5 0"/>
                </svg>
              </div>
            </div>
            <div class="kpi-value"><span id="ca-estime">0</span> <span class="muted" style="font-size:14px;font-weight:700;">M FCFA</span></div>
            <div class="kpi-sub">
              <span>+8.3% vs. période précédente</span>
              <span class="muted">Prix moyen × quantité récoltée</span>
            </div>
          </article>

          <article class="card">
            <div class="card-header">
              <p class="card-title">Hectares Actifs</p>
              <div class="card-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 21s7-4.5 7-11a7 7 0 0 0-14 0c0 6.5 7 11 7 11z"/><path d="M12 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                </svg>
              </div>
            </div>
            <div class="kpi-value"><span id="kpiHa">0</span> <span class="muted" style="font-size:14px;font-weight:700;">ha</span></div>
            <div class="kpi-sub">
              <span>+5.2% vs. période précédente</span>
              <span class="muted">Parcelles hors jachère</span>
            </div>
          </article>

          
          <article class="card">
            <div class="card-header">
              <p class="card-title">Agriculteurs Actifs</p>
              <div class="card-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                  <circle cx="9" cy="7" r="4"/>
                  <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
              </div>
            </div>
            <div class="kpi-value"><span id="nombre-agriculteurs">0</span> <span class="muted" style="font-size:14px;font-weight:700;">actifs</span></div>
            <div class="kpi-sub">
              <span>+15.2% vs. période précédente</span>
              <span class="muted">Clients ayant saisi une récolte</span>
            </div>
          </article>
        </section>

        <div class="weather-widget">
            <div class="weather-content">
                <div class="weather-icon">☀️</div>
                <div class="weather-info">
                    <div class="weather-location">Bamako</div>
                    <div class="weather-temp">35°C</div>
                    <div class="weather-condition">Temps sec</div>
                </div>
            </div>
        </div>

        <p id="dashboardInsight" class="dashboard-insight" role="status"></p>

        <!-- Section 1 : Cartographie -->
        <section class="dashboard-layout">
          <article class="card map-card">
            <div class="card-header">
              <div>
                <h3 style="margin:0; font-size:16px;">Cartographie des Récoltes</h3>
                <div class="small muted">Vue stratégique des zones agricoles</div>
              </div>
            </div>
            <div class="map-container">
              <div class="mali-map">
                <!-- Carte simplifiée du Mali avec zones -->
                <div class="map-region" data-region="bamako" style="top: 45%; left: 30%;">
                  <div class="map-point green"></div>
                  <span class="map-label">Bamako</span>
                </div>
                <div class="map-region" data-region="sikasso" style="top: 70%; left: 55%;">
                  <div class="map-point green"></div>
                  <span class="map-label">Sikasso</span>
                </div>
                <div class="map-region" data-region="segou" style="top: 50%; left: 45%;">
                  <div class="map-point red"></div>
                  <span class="map-label">Ségou</span>
                </div>
                <div class="map-region" data-region="kayes" style="top: 25%; left: 15%;">
                  <div class="map-point red"></div>
                  <span class="map-label">Kayes</span>
                </div>
                <div class="map-region" data-region="mopti" style="top: 35%; left: 60%;">
                  <div class="map-point green"></div>
                  <span class="map-label">Mopti</span>
                </div>
                <div class="map-region" data-region="tombouctou" style="top: 15%; left: 40%;">
                  <div class="map-point green"></div>
                  <span class="map-label">Tombouctou</span>
                </div>
                <div class="map-region" data-region="gao" style="top: 20%; left: 75%;">
                  <div class="map-point red"></div>
                  <span class="map-label">Gao</span>
                </div>
              </div>
              <div class="map-legend">
                <div class="legend-item">
                  <div class="legend-dot green"></div>
                  <span>Bonne récolte</span>
                </div>
                <div class="legend-item">
                  <div class="legend-dot red"></div>
                  <span>Zone à risque</span>
                </div>
              </div>
            </div>
          </article>

          <!-- Section 2 : Graphiques Comparatifs -->
          <div class="charts-section">
            <article class="card">
              <div class="card-header">
                <div>
                  <h3 style="margin:0; font-size:16px;">Production Totale par Culture</h3>
                  <div class="small muted">Riz vs Maïs vs Coton</div>
                </div>
                <span class="tag good">Tonnes</span>
              </div>
              <div style="height: 240px;">
                <canvas id="productionChart"></canvas>
              </div>
            </article>

            <article class="card">
              <div class="card-header">
                <div>
                  <h3 style="margin:0; font-size:16px;">Répartition des Alertes</h3>
                  <div class="small muted">Causes principales</div>
                </div>
                <span class="tag danger">Alertes</span>
              </div>
              <div style="height: 240px;">
                <canvas id="alertsChart"></canvas>
              </div>
            </article>
          </div>
        </section>

        <!-- Section Top Agriculteurs -->
        <section class="top-farmers-section">
          <article class="card">
            <div class="card-header">
              <div>
                <h3 style="margin:0; font-size:16px;">🏆 Top Performance</h3>
                <div class="small muted">Les 3 meilleurs rendements ce mois-ci</div>
              </div>
              <span class="tag good">Elite</span>
            </div>
            <div class="top-farmers-table">
              <div class="table-header">
                <div class="table-rank">Rang</div>
                <div class="table-name">Agriculteur</div>
                <div class="table-location">Localisation</div>
                <div class="table-performance">Rendement</div>
                <div class="table-culture">Culture</div>
              </div>
              <div class="table-body" id="topFarmersBody">
                <!-- Sera rempli dynamiquement -->
              </div>
            </div>
          </article>
        </section>

        <section style="margin-top: 24px;">
          
          <!-- Première ligne : Activités Récentes + Conseils SeneBI -->
          <div class="dashboard-row-2" style="display: grid; grid-template-columns: 65fr 35fr; gap: 16px; margin-bottom: 16px; align-items: stretch;">
            
            <!-- Activités Récentes -->
            <article class="card" style="display: flex; flex-direction: column; border-top: 3px solid #3b82f6; transition: all 0.3s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 20px 40px rgba(0,0,0,0.08)';"
                     onmouseout="this.style.transform=''; this.style.boxShadow='';">
              @php
                $activities = \App\Models\Notification::latest()->limit(8)->get();
              @endphp
              <div class="card-header">
                <div style="display: flex; align-items: center; gap: 12px;">
                  <div style="width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #eff6ff; border: 1px solid #dbeafe;">
                    <i class="fas fa-list-check" style="font-size: 14px; color: #1e40af;"></i>
                  </div>
                  <div>
                    <h3 style="margin:0; font-size:16px; font-weight: 700; color: #111827;">Activites Recentes</h3>
                    <div class="small muted">Dernieres actions des agriculteurs</div>
                  </div>
                </div>
                <span class="badge" style="background: #eff6ff; color: #1e40af; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700;">{{ $activities->count() }}</span>
              </div>
              <div style="flex: 1; padding: 16px;">
                @if($activities->count() > 0)
                  <div class="timeline-list" style="max-height: 340px; overflow-y: auto; padding-left: 22px; position: relative;">
                    <div style="position: absolute; left: 6px; top: 6px; bottom: 6px; width: 2px; background: #e5e7eb; border-radius: 1px;"></div>
                    @foreach($activities as $activity)
                      <div class="timeline-item" style="position: relative; padding-bottom: 14px; display: flex; gap: 12px; align-items: flex-start;">
                        <div style="position: absolute; left: -22px; top: 4px; width: 14px; height: 14px; border-radius: 50%; background: {{ $activity->level === 'danger' ? '#ef4444' : ($activity->level === 'warning' ? '#f59e0b' : '#10b981') }}; border: 3px solid #fff; box-shadow: 0 0 0 1px {{ $activity->level === 'danger' ? '#ef4444' : ($activity->level === 'warning' ? '#f59e0b' : '#10b981') }}; flex-shrink: 0;"></div>
                        <div style="flex: 1; min-width: 0;">
                          <div style="font-size: 13px; font-weight: 600; color: #111827; margin-bottom: 2px;">{{ $activity->title }}</div>
                          <div style="font-size: 12px; color: #6b7280; line-height: 1.4;">{{ $activity->message }}</div>
                          <div style="font-size: 11px; color: #9ca3af; margin-top: 2px;"><i class="fas fa-clock" style="margin-right: 4px;"></i>{{ $activity->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                @else
                  <div style="text-align: center; color: #9ca3af; padding: 24px; font-size: 14px;">Aucune activite recente.</div>
                @endif
              </div>
            </article>

            <!-- Conseils SeneBI -->
            <article class="card" style="display: flex; flex-direction: column; border-top: 3px solid #f59e0b; transition: all 0.3s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 20px 40px rgba(0,0,0,0.08)';"
                     onmouseout="this.style.transform=''; this.style.boxShadow='';">
              <div class="card-header">
                <div style="display: flex; align-items: center; gap: 12px;">
                  <div style="width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #fffbeb; border: 1px solid #fef3c7;">
                    <i class="fas fa-robot" style="font-size: 14px; color: #92400e;"></i>
                  </div>
                  <div>
                    <h3 style="margin:0; font-size:16px; font-weight: 700; color: #111827;">Conseils SeneBI</h3>
                    <div class="small muted">Recommandations automatiques</div>
                  </div>
                </div>
                <span class="badge" style="background: #fffbeb; color: #92400e; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700;">{{ !empty($recommendations) ? count($recommendations) : 0 }}</span>
              </div>
              <div style="flex: 1; padding: 16px;">
                @if(!empty($recommendations) && count($recommendations) > 0)
                  <div style="display: flex; flex-direction: column; gap: 10px; max-height: 380px; overflow-y: auto;">
                    @foreach($recommendations as $rec)
                      <div class="advice-chip" style="display: flex; gap: 10px; align-items: flex-start; padding: 12px; border-radius: 10px; border: 1px solid {{ $rec['type'] === 'danger' ? '#fecaca' : ($rec['type'] === 'warning' ? '#fef3c7' : '#bbf7d0') }}; background: {{ $rec['type'] === 'danger' ? '#fef2f2' : ($rec['type'] === 'warning' ? '#fffbeb' : '#f0fdf4') }}; transition: all 0.2s ease; cursor: default;"
                           onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.06)';"
                           onmouseout="this.style.transform=''; this.style.boxShadow='';">
                        <i class="fas fa-robot" style="color: {{ $rec['type'] === 'danger' ? '#ef4444' : ($rec['type'] === 'warning' ? '#f59e0b' : '#10b981') }}; margin-top: 2px; font-size: 13px;"></i>
                        <span style="font-size: 13px; color: #374151; line-height: 1.5;">{{ $rec['message'] }}</span>
                      </div>
                    @endforeach
                  </div>
                @else
                  <div style="text-align: center; color: #9ca3af; padding: 24px; font-size: 14px;">
                    <i class="fas fa-check-circle" style="font-size: 32px; color: #10b981; margin-bottom: 8px; display: block;"></i>
                    Tous les indicateurs sont bons.
                  </div>
                @endif
              </div>
            </article>
          </div>

          <!-- Deuxième ligne : Agriculteurs à Risque -->
          <section class="grid cards-3">
            <article class="card" style="display: flex; flex-direction: column; border-top: 3px solid #ef4444; transition: all 0.3s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 20px 40px rgba(0,0,0,0.08)';"
                     onmouseout="this.style.transform=''; this.style.boxShadow='';">
              <div class="card-header">
                <div style="display: flex; align-items: center; gap: 12px;">
                  <div style="width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #fef2f2; border: 1px solid #fecaca;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 14px; color: #991b1b;"></i>
                  </div>
                  <div>
                    <h3 style="margin:0; font-size:16px; font-weight: 700; color: #111827;">Agriculteurs a Risque</h3>
                    <div class="small muted">Necessitent une attention particuliere</div>
                  </div>
                </div>
                <span class="badge" style="background: #fef2f2; color: #991b1b; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700;">{{ $atRiskFarmers->count() }}</span>
              </div>
              <div style="flex: 1; padding: 16px;">
                @if($atRiskFarmers->count() > 0)
                  <div class="risk-farmers-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px;">
                    @foreach($atRiskFarmers as $farmer)
                      <div class="risk-card" style="background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 16px; display: flex; flex-direction: column; gap: 12px; transition: all 0.3s ease; animation: fadeUpSoft 0.4s ease both;"
                           onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 16px 32px rgba(0,0,0,0.08)'; this.style.borderColor='#d1d5db';"
                           onmouseout="this.style.transform=''; this.style.boxShadow=''; this.style.borderColor='#e5e7eb';">
                        <div style="display: flex; align-items: center; gap: 12px;">
                          <div style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 14px; font-weight: 800; flex-shrink: 0;">
                            {{ substr($farmer['name'], 0, 2) }}
                          </div>
                          <div style="min-width: 0;">
                            <div style="font-size: 14px; font-weight: 700; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $farmer['name'] }}</div>
                            <div style="font-size: 12px; color: #6b7280; display: flex; align-items: center; gap: 4px; margin-top: 2px;">
                              <i class="fas fa-map-marker-alt" style="font-size: 10px; color: #ef4444;"></i> {{ $farmer['location'] }}
                            </div>
                          </div>
                        </div>
                        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                          @foreach($farmer['risks'] as $risk)
                            @php
                              $riskLabels = [
                                'stock_critique' => 'Stock critique',
                                'faible_rentabilite' => 'Faible rentabilite',
                                'faible_activite' => 'Faible activite',
                              ];
                              $riskColors = [
                                'stock_critique' => 'critical',
                                'faible_rentabilite' => 'warning',
                                'faible_activite' => 'muted',
                              ];
                            @endphp
                            <span class="badge {{ $riskColors[$risk] ?? 'muted' }}">{{ $riskLabels[$risk] ?? $risk }}</span>
                          @endforeach
                        </div>
                        <div style="font-size: 12px; color: #64748b; display: flex; align-items: center; gap: 6px;">
                          <i class="fas fa-clock" style="font-size: 10px;"></i> Derniere visite : {{ $farmer['last_visit'] }}
                        </div>
                        <div style="display: flex; gap: 8px; margin-top: auto; padding-top: 10px; border-top: 1px solid #f3f4f6;">
                          <a href="#" class="btn btn-sm" style="font-size: 12px; padding: 6px 10px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;"><i class="fas fa-eye"></i> Details</a>
                          <a href="{{ url('/manager/visites') }}" class="btn" style="font-size: 12px; padding: 6px 10px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; background: #10b981; color: #fff; border: none;"><i class="fas fa-calendar-check"></i> Planifier visite</a>
                        </div>
                      </div>
                    @endforeach
                  </div>
                @else
                  <div style="text-align: center; color: #9ca3af; padding: 24px; font-size: 14px;">Aucun agriculteur a risque detecte.</div>
                @endif
              </div>
            </article>
          </section>
        </section>

        <style>
          @media (max-width: 1024px) {
            .dashboard-row-2 { grid-template-columns: 1fr !important; }
            .risk-farmers-grid { grid-template-columns: repeat(2, 1fr) !important; }
          }
          @media (max-width: 768px) {
            .dashboard-row-2 { grid-template-columns: 1fr !important; }
            .risk-farmers-grid { grid-template-columns: 1fr !important; }
          }
          @keyframes fadeUpSoft {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
          }
          .timeline-list::-webkit-scrollbar { width: 6px; }
          .timeline-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
          .btn-sm { font-size: 12px; padding: 6px 10px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; background: #f8fafc; color: #1e293b; border: 1px solid #e2e8f0; }
          .btn-sm:hover { background: #e2e8f0 !important; border-color: #cbd5e1 !important; }
        </style>

      </main>
      @include('partials.footer-manager')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <script src="{{ asset('assets/js/core.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard-global.js') }}"></script>
    <script src="{{ asset('assets/js/region-filter.js') }}"></script>
    <script src="{{ asset('assets/js/notifications-simple.js') }}"></script>

    <script>
      // Passer les top clients au JavaScript
      window.SeneBI = window.SeneBI || {};
      window.SeneBI.topClients = {{ \Illuminate\Support\Js::from($topClients) }};
    </script>
    
    <!-- Script d'automatisation du Dashboard Manager -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // SCRIPT DE CALCUL GLOBAL - AUTOMATISATION DES INDICATEURS
        function calculateDashboardMetrics() {
          try {
            console.log('🔄 Début du calcul des métriques du dashboard...');
            
            // 1. Récupérer la liste complète des récoltes dans le localStorage
            const allHarvests = JSON.parse(localStorage.getItem('total_recolte_senebi') || '[]');
            console.log('📊 Nombre de récoltes trouvées:', allHarvests.length);
            
            // 2. Calculer la somme de toutes les quantités récoltées
            const totalProduction = allHarvests.reduce((sum, harvest) => {
              return sum + (parseFloat(harvest.quantite) || 0);
            }, 0);
            
            // 3. Récupérer les prix du catalogue pour calculer le CA
            const prixCatalogue = {
              riz: parseFloat(localStorage.getItem('prix_riz')) || 1500,
              mais: parseFloat(localStorage.getItem('prix_mais')) || 1200,
              coton: parseFloat(localStorage.getItem('prix_coton')) || 2000
            };
            
            // 4. Calculer le Chiffre d'Affaires total
            let totalCA = 0;
            allHarvests.forEach(harvest => {
              const culture = harvest.culture || 'Non spécifiée';
              const quantite = parseFloat(harvest.quantite) || 0;
              let prixUnitaire = 0;
              
              switch(culture.toLowerCase()) {
                case 'riz':
                  prixUnitaire = prixCatalogue.riz;
                  break;
                case 'mais':
                  prixUnitaire = prixCatalogue.mais;
                  break;
                case 'coton':
                  prixUnitaire = prixCatalogue.coton;
                  break;
                default:
                  prixUnitaire = 1500; // Prix par défaut
              }
              
              totalCA += quantite * prixUnitaire;
            });
            
            // 5. Compter le nombre unique de noms de clients
            const uniqueClients = new Set();
            allHarvests.forEach(harvest => {
              if (harvest.client && harvest.client.trim()) {
                uniqueClients.add(harvest.client.trim());
              }
            });
            const nombreAgriculteurs = uniqueClients.size;
            
            // 6. Mettre à jour visuelle les indicateurs
            updateKPIDisplay('total-production', totalProduction.toLocaleString('fr-FR'));
            updateKPIDisplay('ca-estime', (totalCA / 1000000).toFixed(2)); // En millions FCFA
            updateKPIDisplay('nombre-agriculteurs', nombreAgriculteurs);
            
            console.log('✅ Métriques calculées avec succès:');
            console.log('   🌾 Production totale:', totalProduction.toLocaleString('fr-FR') + ' kg');
            console.log('   💰 Chiffre d\'Affaires:', (totalCA / 1000000).toFixed(2) + ' M FCFA');
            console.log('   👥 Agriculteurs actifs:', nombreAgriculteurs);
            
            // 7. Mettre à jour les sous-titres avec les données réelles
            updateKPISubtitles(allHarvests.length, totalProduction, totalCA);
            
          } catch (error) {
            console.error('❌ Erreur lors du calcul des métriques:', error);
          }
        }
        
        // Fonction pour mettre à jour l'affichage d'un KPI
        function updateKPIDisplay(elementId, value) {
          const element = document.getElementById(elementId);
          if (element) {
            // Animation de comptage si la valeur change
            const currentValue = element.textContent;
            if (currentValue !== value.toString()) {
              animateCounter(element, currentValue, value);
            }
          }
        }
        
        // Animation de compteur pour les KPI
        function animateCounter(element, start, end) {
          const duration = 1000; // 1 seconde
          const startTime = performance.now();
          const startValue = parseFloat(start) || 0;
          const endValue = parseFloat(end) || 0;
          
          function updateCounter(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const currentValue = startValue + (endValue - startValue) * progress;
            
            if (elementId === 'ca-estime') {
              element.textContent = currentValue.toFixed(2);
            } else {
              element.textContent = Math.round(currentValue).toLocaleString('fr-FR');
            }
            
            if (progress < 1) {
              requestAnimationFrame(updateCounter);
            }
          }
          
          requestAnimationFrame(updateCounter);
        }
        
        // Mettre à jour les sous-titres des KPI
        function updateKPISubtitles(harvestCount, totalProduction, totalCA) {
          // Mettre à jour le sous-titre de production
          const productionSubtitle = document.querySelector('#total-production').closest('.card').querySelector('.kpi-sub span:last-child');
          if (productionSubtitle) {
            productionSubtitle.textContent = `${harvestCount} récolte${harvestCount > 1 ? 's' : ''} enregistrée${harvestCount > 1 ? 's' : ''}`;
          }
          
          // Mettre à jour le sous-titre du CA
          const caSubtitle = document.querySelector('#ca-estime').closest('.card').querySelector('.kpi-sub span:last-child');
          if (caSubtitle) {
            const prixMoyen = totalProduction > 0 ? (totalCA / totalProduction).toFixed(0) : 0;
            caSubtitle.textContent = `Prix moyen: ${parseInt(prixMoyen).toLocaleString('fr-FR')} FCFA/kg`;
          }
          
          // Mettre à jour le sous-titre des agriculteurs
          const farmersSubtitle = document.querySelector('#nombre-agriculteurs').closest('.card').querySelector('.kpi-sub span:last-child');
          if (farmersSubtitle) {
            farmersSubtitle.textContent = `Actifs cette saison`;
          }
        }
        
        // Lancer le calcul au chargement de la page
        calculateDashboardMetrics();
        
        // Recalculer toutes les 30 secondes pour avoir des données à jour
        setInterval(calculateDashboardMetrics, 30000);
        
        // Écouter les changements dans le localStorage (si d'autres onglets modifient les données)
        window.addEventListener('storage', function(e) {
          if (e.key === 'total_recolte_senebi' || e.key.startsWith('prix_')) {
            console.log('🔄 Changement détecté dans localStorage, recalcul des métriques...');
            calculateDashboardMetrics();
          }
        });
        
        console.log('🚀 Script d\'automatisation du Dashboard Manager chargé avec succès');
      });
    </script>
    
    <!-- Fonctions JavaScript pour les sections restaurées -->
    <script>
      // Initialisation des données dynamiques au chargement
      window.addEventListener('load', function() {
        console.log("🚀 Initialisation du dashboard manager avec données complètes");
        
        // Remplir le tableau Top Performance avec les clients réels
        const topFarmersBody = document.getElementById('topFarmersBody');
        if (topFarmersBody) {
          const topClients = window.SeneBI?.topClients || [];

          if (topClients.length === 0) {
            topFarmersBody.innerHTML = `
              <div class="table-row" style="text-align: center; padding: 24px; color: #6b7280;">
                Aucun agriculteur avec des données de performance
              </div>
            `;
          } else {
            topFarmersBody.innerHTML = topClients.map((client, index) => `
              <div class="table-row">
                <div class="table-rank">${index + 1}</div>
                <div class="table-name">${client.name}</div>
                <div class="table-location">${client.location}</div>
                <div class="table-performance">${client.rendement.toFixed(1)} t/ha</div>
                <div class="table-culture">${client.culture}</div>
              </div>
            `).join('');
          }
          console.log("✅ Top Performance rempli avec les clients réels");
        }
        
        // Animation des barres de tendances
        const trendBars = document.querySelectorAll('.trend-bar');
        trendBars.forEach((bar, index) => {
          const height = bar.style.height;
          bar.style.height = '0';
          setTimeout(() => {
            bar.style.transition = 'height 0.8s ease';
            bar.style.height = height;
          }, 100 * (index + 1));
        });
        
        console.log("✅ Dashboard manager entièrement initialisé");
        
        // Détecter la page active dans la navigation
        const navLinks = document.querySelectorAll('.manager-nav a');
        const currentPath = window.location.pathname;

        navLinks.forEach(link => {
          const linkPath = new URL(link.href, window.location.origin).pathname;
          if (currentPath === linkPath) {
            link.classList.add('active');
          }
        });
      });
    </script>
  </body>
 </html>