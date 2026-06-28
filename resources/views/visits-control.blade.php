<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Planning des Visites - SeneBI</title>
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />

    <!-- Styles pour la navigation active -->
    <style>
      .manager-nav a.active {
        background: #dcfce7;
        color: #14532d;
        font-weight: 600;
        border-left: 3px solid #10b981;
        border-radius: 0 8px 8px 0;
        transition: all 0.2s ease;
      }

      .visits-list {
        max-height: 300px;
        overflow-y: auto;
        padding-right: 8px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
      }

      .visits-list::-webkit-scrollbar {
        width: 12px;
      }

      .visits-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 6px;
      }

      .visits-list::-webkit-scrollbar-thumb {
        background: #10b981;
        border-radius: 6px;
        border: 2px solid #f1f1f1;
      }

      .visits-list::-webkit-scrollbar-thumb:hover {
        background: #059669;
      }
      
      .manager-nav a.active:hover {
        background: #bbf7d0;
        border-left-color: #059669;
      }

      .visits-list {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid rgba(15, 23, 42, 0.08);
        background: #ffffff;
      }

      .visit-item {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        gap: 16px;
        align-items: center;
        padding: 18px 20px;
      }

      .visit-item:not(:last-child) {
        border-bottom: 1px solid rgba(15, 23, 42, 0.08);
      }

      .visit-date {
        text-align: center;
        min-width: 70px;
      }

      .visit-day {
        display: block;
        font-size: 28px;
        font-weight: 900;
        color: var(--primary);
        line-height: 1;
      }

      .visit-month {
        display: block;
        font-size: 12px;
        color: var(--muted);
        text-transform: uppercase;
        margin-top: 2px;
      }

      .visit-time {
        display: block;
        margin-top: 6px;
        font-size: 12px;
        color: var(--muted);
      }

      .visit-details {
        display: grid;
        gap: 4px;
      }

      .visit-person {
        font-weight: 700;
        color: #0f172a;
      }

      .visit-location {
        font-size: 14px;
        color: #475569;
      }

      .visit-purpose {
        font-size: 13px;
        color: #64748b;
        margin-top: 6px;
      }

      .visit-status {
        display: flex;
        justify-content: flex-end;
      }

      .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
      }

      .status-badge.planifie {
        background: #dcfce7;
        color: #166534;
      }

      .status-badge.en-retard {
        background: #fee2e2;
        color: #991b1b;
      }

      .status-badge.urgent {
        background: #fef3c7;
        color: #92400e;
      }

      @media (max-width: 1024px) {
        .visits-row-1 { grid-template-columns: 1fr !important; }
        .visits-row-2 { grid-template-columns: 1fr !important; }
      }

      @media (max-width: 768px) {
        .visit-form-horizontal .form-row { grid-template-columns: 1fr !important; }
        .visits-row-1 { grid-template-columns: 1fr !important; }
        .visits-row-2 { grid-template-columns: 1fr !important; }
        .visit-card-modern { flex-direction: column !important; align-items: flex-start !important; gap: 10px !important; }
        .visit-calendar-badge { width: 100% !important; height: auto !important; flex-direction: row !important; gap: 10px !important; align-items: center !important; padding: 10px !important; }
      }

      @keyframes fadeUpSoft {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
      }

      .visit-card-modern { animation: fadeUpSoft .45s ease both; }
      .urgent-card { animation: fadeUpSoft .45s ease both; }
    </style>
  </head>
  <body data-page="visits">
    <div class="app">
      @include('header-manager')
      
      <main class="container">
        <div class="page-title">
          <div>
            <h1>Planning des Visites</h1>
            <p>Organisation des déplacements chez les agriculteurs et gestion des urgences</p>
          </div>
        </div>

        <!-- Première ligne : Visites prévues + Visites urgentes -->
        <div class="visits-row-1" style="display: grid; grid-template-columns: 65fr 35fr; gap: 16px; margin-bottom: 16px; align-items: stretch;">
          
          <!-- Visites prévues -->
          <article class="card" style="display: flex; flex-direction: column; border-top: 3px solid #3b82f6; transition: all 0.3s ease;"
                   onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 20px 40px rgba(0,0,0,0.08)';"
                   onmouseout="this.style.transform='';this.style.boxShadow='';">
            <div class="card-header">
              <div>
                <h3 style="margin:0; font-size:16px; font-weight:700; color:#111827;">Visites Prevues</h3>
                <div class="small muted">Planning de la semaine</div>
              </div>
              <span class="badge" style="background:#eff6ff; color:#1e40af; padding:4px 12px; border-radius:999px; font-size:12px; font-weight:700;">{{ $visites->count() }}</span>
            </div>
            <div style="flex:1; padding:0;">
              @foreach($visites as $visite)
                <div class="visit-card-modern" style="display:flex; align-items:center; gap:14px; padding:14px 16px; border-bottom:1px solid #f3f4f6; transition:background .2s ease; animation: fadeUpSoft .45s ease both;"
                     onmouseover="this.style.background='#f8fafc';"
                     onmouseout="this.style.background='transparent';">
                  <div class="visit-calendar-badge" style="min-width:64px; height:68px; border-radius:12px; background:linear-gradient(135deg,#eff6ff,#dbeafe); border:1px solid #bfdbfe; display:flex; flex-direction:column; align-items:center; justify-content:center; box-shadow:0 4px 10px rgba(59,130,246,0.08);">
                    <span style="font-size:20px; font-weight:900; color:#1e40af; line-height:1;">{{ $visite->date_visite->format('d') }}</span>
                    <span style="font-size:11px; font-weight:700; color:#3b82f6; text-transform:uppercase; margin-top:2px;">{{ $visite->date_visite->format('M') }}</span>
                    <span style="font-size:10px; color:#64748b; margin-top:2px;"><i class="fas fa-clock" style="margin-right:3px;"></i>{{ $visite->date_visite->format('H:i') }}</span>
                  </div>
                  <div style="flex:1; min-width:0;">
                    <div style="font-size:14px; font-weight:700; color:#111827; margin-bottom:2px;">{{ $visite->user->name ?? 'N/A' }}</div>
                    <div style="font-size:12px; color:#6b7280; display:flex; align-items:center; gap:4px; margin-bottom:2px;"><i class="fas fa-map-marker-alt" style="font-size:10px; color:#ef4444;"></i> {{ $visite->user->location ?? 'Non spécifié' }}</div>
                    <div style="font-size:12px; color:#475569;">Motif : {{ $visite->action_effectuee }}</div>
                  </div>
                  <div>
                    @php
                      $statusClass = match(true) {
                        $visite->date_visite->lt(now()) => 'muted',
                        default => 'planifie',
                      };
                      $statusLabel = match(true) {
                        $visite->date_visite->lt(now()) => 'Terminée',
                        default => 'Planifiée',
                      };
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                  </div>
                </div>
              @endforeach

              @if($visites->isEmpty())
                <div style="text-align:center; padding:32px; color:#9ca3af; font-size:14px;">Aucune visite planifiée.</div>
              @endif
            </div>
          </article>

          <!-- Visites urgentes -->
          <article class="card" style="display: flex; flex-direction: column; border-top: 3px solid #ef4444; transition: all 0.3s ease;"
                   onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 20px 40px rgba(0,0,0,0.08)';"
                   onmouseout="this.style.transform='';this.style.boxShadow='';">
            <div class="card-header">
              <div>
                <h3 style="margin:0; font-size:16px; font-weight:700; color:#111827;"><i class="fas fa-triangle-exclamation" style="color:#ef4444; margin-right:6px; font-size:13px;"></i>Visites Urgentes</h3>
                <div class="small muted">Agriculteurs en situation critique</div>
              </div>
              <span class="badge" style="background:#fef2f2; color:#991b1b; padding:4px 12px; border-radius:999px; font-size:12px; font-weight:700;">{{ $urgentClients->count() }}</span>
            </div>
            <div style="flex:1; padding:12px;">
              @if ($urgentClients->count() > 0)
                @foreach ($urgentClients as $urgentClient)
                  <div class="urgent-card" style="display:flex; align-items:flex-start; gap:10px; padding:12px; border-radius:12px; border:1px solid #fecaca; background:#fff; margin-bottom:10px; transition:all .2s ease; cursor:default;"
                       onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 10px 20px rgba(239,68,68,0.08)';this.style.borderColor='#fca5a5';"
                       onmouseout="this.style.transform='';this.style.boxShadow='';this.style.borderColor='#fecaca';">
                    <div style="width:32px; height:32px; border-radius:10px; background:#fef2f2; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                      <i class="fas fa-circle-exclamation" style="color:#ef4444; font-size:14px;"></i>
                    </div>
                    <div style="flex:1; min-width:0;">
                      <div style="font-size:13px; font-weight:700; color:#111827; margin-bottom:2px;">{{ $urgentClient['name'] }}</div>
                      <div style="font-size:12px; color:#6b7280; display:flex; align-items:center; gap:4px; margin-bottom:2px;"><i class="fas fa-map-marker-alt" style="font-size:10px; color:#ef4444;"></i> {{ $urgentClient['location'] }}</div>
                      <div style="font-size:12px; color:#991b1b; font-weight:600;">Stock {{ $urgentClient['intrant'] }} <span style="background:#fef2f2; padding:2px 6px; border-radius:999px; font-size:11px; border:1px solid #fecaca;">{{ $urgentClient['percentage'] }}%</span></div>
                    </div>
                    <button class="btn" style="font-size:11px; padding:6px 10px; border-radius:8px; background:#ef4444; color:#fff; border:none; cursor:pointer; white-space:nowrap;" onclick="planUrgentVisit(this, '{{ $urgentClient['name'] }}', '{{ $urgentClient['location'] }}', 'Contrôle stock {{ $urgentClient['intrant'] }}')">Planifier</button>
                  </div>
                @endforeach
              @else
                <div style="text-align:center; color:#9ca3af; padding:20px; font-size:14px;">
                  <i class="fas fa-check-circle" style="font-size:32px; color:#10b981; margin-bottom:8px; display:block;"></i>
                  Aucune urgence détectée.
                </div>
              @endif
            </div>
            <div style="padding:12px 16px; border-top:1px solid #f3f4f6; background:#fafafa; border-radius:0 0 12px 12px; display:flex; align-items:center; justify-content:space-between;">
              <span style="font-size:12px; color:#64748b;">Total urgences</span>
              <span style="font-size:14px; font-weight:800; color:#111827;">{{ $urgentClients->count() }}</span>
            </div>
          </article>
        </div>

        <!-- Troisième ligne : Historique des visites -->
        <div class="visits-row-2" style="display: grid; grid-template-columns: 1fr; gap: 16px; margin-bottom: 24px; align-items: stretch;">
          
          <!-- Historique des visites -->
          <article class="card" style="display: flex; flex-direction: column; border-top: 3px solid #3b82f6; transition: all 0.3s ease;"
                   onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 20px 40px rgba(0,0,0,0.08)';"
                   onmouseout="this.style.transform='';this.style.boxShadow='';">
            <div class="card-header">
              <div>
                <h3 style="margin:0; font-size:16px; font-weight:700; color:#111827;">Historique des Visites</h3>
                <div class="small muted">Toutes les visites enregistrées</div>
              </div>
              <span class="badge" style="background:#eff6ff; color:#1e40af; padding:4px 12px; border-radius:999px; font-size:12px; font-weight:700;">{{ $allVisites->count() }}</span>
            </div>
            <div style="flex:1; padding:0; overflow:auto; max-height: 420px;">
              <table class="table table-compact" style="width:100%; border-collapse:collapse; font-size:13px;">
                <thead>
                  <tr style="background:#f8fafc; position:sticky; top:0;">
                    <th style="padding:12px 14px; text-align:left; font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.3px; border-bottom:1px solid #e5e7eb;">Date</th>
                    <th style="padding:12px 14px; text-align:left; font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.3px; border-bottom:1px solid #e5e7eb;">Agriculteur</th>
                    <th style="padding:12px 14px; text-align:left; font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.3px; border-bottom:1px solid #e5e7eb;">Localisation</th>
                    <th style="padding:12px 14px; text-align:left; font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.3px; border-bottom:1px solid #e5e7eb;">Motif</th>
                    <th style="padding:12px 14px; text-align:left; font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.3px; border-bottom:1px solid #e5e7eb;">Statut</th>
                    <th style="padding:12px 14px; text-align:left; font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.3px; border-bottom:1px solid #e5e7eb;">Compte rendu</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($allVisites as $visite)
                    @php
                      $statusClass = match(true) {
                        $visite->date_visite->lt(now()) => 'muted',
                        default => 'ok',
                      };
                      $statusLabel = match(true) {
                        $visite->date_visite->lt(now()) => 'Effectuée',
                        default => 'Planifiée',
                      };
                    @endphp
                    <tr style="border-bottom:1px solid #f1f5f9; transition:background .2s ease; cursor:default;"
                        onmouseover="this.style.background='#f8fafc';"
                        onmouseout="this.style.background='transparent';">
                      <td style="padding:12px 14px; color:#374151; font-weight:500; white-space:nowrap;">{{ $visite->date_visite->format('d/m/Y H:i') }}</td>
                      <td style="padding:12px 14px; color:#111827; font-weight:600;">{{ $visite->user->name ?? 'N/A' }}</td>
                      <td style="padding:12px 14px; color:#475569;">{{ $visite->user->location ?? 'Non spécifié' }}</td>
                      <td style="padding:12px 14px; color:#374151;">{{ $visite->action_effectuee }}</td>
                      <td style="padding:12px 14px;"><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                      <td style="padding:12px 14px; color:#64748b;">{{ Str::limit($visite->recommandation ?? 'Aucun', 40) }}</td>
                    </tr>
                  @endforeach
                  @if($allVisites->isEmpty())
                    <tr>
                      <td colspan="6" style="text-align:center; color:#9ca3af; padding:24px;">Aucune visite enregistrée.</td>
                    </tr>
                  @endif
                </tbody>
              </table>
            </div>
          </article>
        </div>

        <!-- Zone Inférieure : Action -->
        <section class="action-zone">
          <article class="card">
            <div class="card-header">
              <div>
                <h3 style="margin:0; font-size:16px;">Planifier une nouvelle visite</h3>
                <div class="small muted">Ajouter une visite au planning</div>
              </div>
            </div>
            
            <form class="visit-form visit-form-horizontal" id="visitForm">
              @csrf
              <div class="form-row">
                <div class="form-group">
                  <label for="farmerSelect">Agriculteur</label>
                  <select id="farmerSelect" name="user_id" class="form-control" required>
                    <option value="">Sélectionner un agriculteur</option>
                    @foreach($clients as $client)
                      <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->location ?? 'Non spécifié' }})</option>
                    @endforeach
                  </select>
                </div>

                <div class="form-group">
                  <label for="dateTime">Date et Heure</label>
                  <input type="datetime-local" id="dateTime" class="form-control" required>
                </div>

                <div class="form-group">
                  <label for="reason">Motif</label>
                  <select id="reason" class="form-control" required>
                    <option value="">Sélectionner un motif</option>
                    <option value="controle-stock-uree">Contrôle stock Urée</option>
                    <option value="alerte-rendement-riz">Alerte rendement Riz</option>
                    <option value="conseil-semis-coton">Conseil semis Coton</option>
                    <option value="controle-stock-npk">Contrôle stock NPK</option>
                    <option value="suivi-recolte-mais">Suivi récolte Maïs</option>
                    <option value="evaluation-performance">Évaluation performance</option>
                    <option value="conseil-technique">Conseil technique</option>
                    <option value="livraison-urgente">Livraison Urgente (Urée/NPK)</option>
                  </select>
                </div>

                <div class="form-group">
                  <label for="recommandation">Recommandation</label>
                  <textarea id="recommandation" name="recommandation" class="form-control" rows="3" placeholder="Conseils et recommandations pour l'agriculteur..."></textarea>
                </div>

                <div class="form-group form-button-group">
                  <label>&​nbsp;</label>
                  <button type="submit" class="btn btn-primary btn-confirm">
                    Confirmer la visite
                  </button>
                </div>
              </div>
            </form>
          </article>
        </section>

        <section class="grid cards-2">
          <article class="card">
            <div class="card-header">
              <div>
                <h3 style="margin:0; font-size:16px;">Historique des Visites</h3>
                <div class="small muted">Toutes les visites enregistrees</div>
              </div>
              <span class="tag muted">Archives</span>
            </div>
            <div style="overflow:auto; max-height: 420px;">
              <table class="table table-compact">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Agriculteur</th>
                    <th>Localisation</th>
                    <th>Motif</th>
                    <th>Statut</th>
                    <th>Compte rendu</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($allVisites as $visite)
                    @php
                      $statusClass = match(true) {
                        $visite->date_visite->lt(now()) => 'muted',
                        default => 'ok',
                      };
                      $statusLabel = match(true) {
                        $visite->date_visite->lt(now()) => 'Effectuée',
                        default => 'Planifiée',
                      };
                    @endphp
                    <tr>
                      <td>{{ $visite->date_visite->format('d/m/Y H:i') }}</td>
                      <td>{{ $visite->user->name ?? 'N/A' }}</td>
                      <td>{{ $visite->user->location ?? 'Non spécifie' }}</td>
                      <td>{{ $visite->action_effectuee }}</td>
                      <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                      <td>{{ Str::limit($visite->recommandation ?? 'Aucun', 40) }}</td>
                    </tr>
                  @endforeach
                  @if($allVisites->isEmpty())
                    <tr>
                      <td colspan="6" style="text-align:center; color:#9ca3af; padding: 20px;">Aucune visite enregistree.</td>
                    </tr>
                  @endif
                </tbody>
              </table>
            </div>
          </article>

          <article class="card">
            <div class="card-header">
              <div>
                <h3 style="margin:0; font-size:16px;">Rapports Urgents</h3>
                <div class="small muted">Visites urgentes et alertes</div>
              </div>
              <span class="tag danger">Urgences</span>
            </div>
            <div style="padding: 16px;">
              @if($urgentClients->count() > 0)
                @foreach($urgentClients as $urgentClient)
                  <div style="display: flex; gap: 10px; align-items: flex-start; margin-bottom: 12px; padding: 12px; background: #fef2f2; border-radius: 8px; border-left: 4px solid #ef4444;">
                    <i class="fas fa-exclamation-triangle" style="color: #ef4444; margin-top: 2px;"></i>
                    <div style="flex: 1; min-width: 0;">
                      <div style="font-weight: 600; font-size: 14px; color: #111827; margin-bottom: 2px;">{{ $urgentClient['name'] }}</div>
                      <div style="font-size: 12px; color: #6b7280;">Stock {{ $urgentClient['intrant'] }} à {{ $urgentClient['percentage'] }}%</div>
                      <div style="font-size: 11px; color: #9ca3af; margin-top: 2px;">Dernière visite : {{ $urgentClient['last_visit'] ?? 'Jamais' }}</div>
                    </div>
                    <a href="#visitForm" class="btn" style="font-size:12px; padding:6px 12px; background:#ef4444; color:white; border:none; border-radius:8px; text-decoration:none;">Planifier</a>
                  </div>
                @endforeach
              @else
                <div style="text-align: center; color: #9ca3af; padding: 20px; font-size: 14px;">
                  <i class="fas fa-check-circle" style="font-size: 32px; color: #10b981; margin-bottom: 8px; display: block;"></i>
                  Aucune urgence detectee.
                </div>
              @endif
            </div>
          </article>
        </section>

      </main>
      @include('partials.footer-manager')
    </div>

    <script src="{{ asset('assets/js/layout.js') }}"></script>
      <script src="{{ asset('assets/js/core.js') }}"></script>
      <script src="{{ asset('assets/js/auth.js') }}"></script>
      <script src="{{ asset('assets/js/visits-control.js') }}"></script>
      
      <!-- JavaScript pour la navigation active et notifications -->
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          // Détecter la page active dans la navigation
          const navLinks = document.querySelectorAll('.manager-nav a');
          const currentPath = window.location.pathname;

          navLinks.forEach(link => {
            const linkPath = new URL(link.href, window.location.origin).pathname;
            if (currentPath === linkPath) {
              link.classList.add('active');
            }
          });
          
          // Gestion de la cloche de notification
          const notificationBell = document.getElementById('notificationBell');
          const notificationDropdown = document.getElementById('notificationDropdown');

          if (notificationBell && notificationDropdown) {
            // Au clic sur la cloche
            notificationBell.addEventListener('click', function(e) {
              e.stopPropagation();
              notificationDropdown.classList.toggle('show');
            });

            // Fermeture au clic ailleurs
            document.addEventListener('click', function(e) {
              if (!notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.remove('show');
              }
            });

            // Fermeture au clic sur le dropdown lui-même
            notificationDropdown.addEventListener('click', function(e) {
              e.stopPropagation();
            });
          }
        });
      </script>
  </body>
</html>