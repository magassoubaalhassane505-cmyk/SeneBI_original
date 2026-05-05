<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Supervision - SeneBI</title>
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />
    
    <!-- Styles pour la cloche de notification -->
    <style>
      .notification-bell {
        position: relative;
        cursor: pointer;
        margin-right: 16px;
        padding: 8px;
        border: 2px solid #10b981;
        border-radius: 50%;
        transition: all 0.2s ease;
      }
      
      .notification-bell:hover {
        border-color: #059669;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
      }
      
      .bell-icon {
        color: #10b981;
        transition: color 0.2s ease;
      }
      
      .notification-bell:hover .bell-icon {
        color: #059669;
      }
      
      .notification-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        background: #ef4444;
        color: white;
        border-radius: 50%;
        width: 16px;
        height: 16px;
        font-size: 10px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      
      .notification-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        margin-top: 8px;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        min-width: 320px;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
      }
      
      .notification-dropdown.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
      }
      
      .notification-content {
        padding: 16px;
      }
      
      .notification-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 0;
      }
      
      .notification-icon {
        font-size: 16px;
        flex-shrink: 0;
      }
      
      .notification-text strong {
        color: #1e293b;
        font-size: 14px;
        display: block;
        margin-bottom: 4px;
      }
      
      .notification-text p {
        color: #64748b;
        font-size: 13px;
        margin: 0;
        line-height: 1.4;
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
      
      /* Risk status badges */
      .risk-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        white-space: nowrap;
      }
      
      .risk-badge.risk-high {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.2);
      }
      
      .risk-badge.risk-medium {
        background: rgba(245, 158, 11, 0.1);
        color: #d97706;
        border: 1px solid rgba(245, 158, 11, 0.2);
      }
      
      .risk-badge.risk-low {
        background: rgba(34, 197, 94, 0.1);
        color: #16a34a;
        border: 1px solid rgba(34, 197, 94, 0.2);
      }
      
      .risk-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
      }
      
      .risk-badge.risk-high::before {
        background: #dc2626;
      }
      
      .risk-badge.risk-medium::before {
        background: #d97706;
      }
      
      .risk-badge.risk-low::before {
        background: #16a34a;
      }
    </style>
  </head>
  <body data-page="supervision">
    <div class="app">
      @include('header-manager')
      
      <main class="container">
        <div class="page-title">
          <div>
            <h1>Supervision</h1>
            <p>Vue d'ensemble des activités et contrôle opérationnel</p>
          </div>
        </div>

        <section class="grid kpis">
          <article class="card">
            <div class="card-header">
              <p class="card-title">Utilisateurs Actifs</p>
              <div class="card-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
              </div>
            </div>
            <div class="kpi-value"><span id="activeUsers">0</span></div>
            <div class="kpi-sub">
              <span>En ligne maintenant</span>
              <span class="muted">Dernières 24 heures</span>
            </div>
          </article>

          <article class="card">
            <div class="card-header">
              <p class="card-title">Activités du Jour</p>
              <div class="card-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
              </div>
            </div>
            <div class="kpi-value"><span id="dailyActivities">0</span></div>
            <div class="kpi-sub">
              <span>Actions enregistrées</span>
              <span class="muted">Toutes catégories</span>
            </div>
          </article>

          <article class="card">
            <div class="card-header">
              <p class="card-title">Alertes Système</p>
              <div class="card-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><path d="M12 9v4"/><path d="M12 17h.01"/>
                </svg>
              </div>
            </div>
            <div class="kpi-value"><span id="systemAlerts">0</span></div>
            <div class="kpi-sub">
              <span>Alertes actives</span>
              <span class="muted">Nécessitent attention</span>
            </div>
          </article>

          <article class="card">
            <div class="card-header">
              <p class="card-title">Performance</p>
              <div class="card-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 17l6-6 4 4 7-7"/><path d="M14 8h6v6"/>
                </svg>
              </div>
            </div>
            <div class="kpi-value"><span id="performanceScore">98</span>%</div>
            <div class="kpi-sub">
              <span>Score système</span>
              <span class="muted">Basé sur metrics</span>
            </div>
          </article>
        </section>

        <section class="farmers-directory">
          <article class="card">
            <div class="card-header">
              <div>
                <h3 style="margin:0; font-size:16px;">Répertoire des Agriculteurs</h3>
                <div class="small muted">Vue d'ensemble de tous les agriculteurs et leur statut</div>
              </div>
              <span class="tag muted">Agriculteurs</span>
            </div>
            
            <div class="table-container">
              <table class="farmers-table">
                <thead>
                  <tr>
                    <th class="th-name">Nom de l'Agriculteur</th>
                    <th class="th-location">Localisation</th>
                    <th class="th-stock-status">Statut des Stocks</th>
                    <th class="th-risk">Risque</th>
                    <th class="th-last-activity">Dernière Activité</th>
                    <th class="th-actions">Actions</th>
                  </tr>
                </thead>
                <tbody id="farmersTableBody">
                  <!-- Les données des agriculteurs seront injectées ici par JavaScript -->
                </tbody>
              </table>
            </div>
          </article>
        </section>
      </main>
      <div data-layout="footer"></div>
    </div>

    <!-- Modal pour les détails de l'agriculteur -->
    <div id="farmerModal" class="modal-overlay" hidden>
      <div class="modal-content">
        <!-- En-tête de la modal -->
        <div class="modal-header">
          <div class="modal-title">
            <h2 id="modalFarmerName">Mamadou Diallo</h2>
            <span id="modalFarmerLocation" class="modal-location">Bamako</span>
          </div>
          <button class="modal-close" onclick="closeFarmerModal()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        <!-- Corps de la modal avec deux colonnes -->
        <div class="modal-body">
          <!-- Colonne gauche : Jauge de Stock -->
          <div class="modal-left">
            <div class="modal-section">
              <h3>Jauge de Stock</h3>
              <div class="stock-chart-container">
                <canvas id="stockChart" width="200" height="200"></canvas>
              </div>
              <div class="stock-details">
                <h4>État des stocks détaillés</h4>
                <div class="stock-list" id="stockList">
                  <!-- Sera rempli dynamiquement -->
                </div>
              </div>
              <div class="chart-description">
                <p class="chart-text">Analyse basée sur les dernières données saisies par l'agriculteur le <span id="stockDate">15 avril 2026</span></p>
              </div>
            </div>
          </div>

          <!-- Colonne droite : Performance de Récolte -->
          <div class="modal-right">
            <div class="modal-section">
              <h3>Performance de Récolte</h3>
              <div class="performance-chart-container">
                <canvas id="performanceChart" width="300" height="200"></canvas>
              </div>
              <div class="chart-description">
                <p class="chart-text">Comparaison : Récolte Réelle vs Prévisions pour la saison 2026</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <script src="{{ asset('assets/js/core.js') }}"></script>
    <script src="{{ asset('assets/js/auth.js') }}"></script>
    <script src="{{ asset('assets/js/supervision.js') }}"></script>
    
    <!-- JavaScript pour la navigation active -->
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