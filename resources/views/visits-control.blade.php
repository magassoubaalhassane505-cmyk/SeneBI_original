<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Planning des Visites - SeneBI</title>
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />
    <script src="{{ asset('assets/js/visits-control.js') }}"></script>
    
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

        <!-- Zone Supérieure : Consultation -->
        <div class="consultation-zone">
          <!-- Section Gauche : Calendrier/Liste des visites -->
          <section class="visits-calendar">
            <article class="card">
              <div class="card-header">
                <div>
                  <h3 style="margin:0; font-size:16px;">Visites Prévues</h3>
                  <div class="small muted">Planning de la semaine</div>
                </div>
                <span class="tag muted">Semaine</span>
              </div>
              
              <div class="visits-list" id="visitsList">
                <!-- Données initiales des visites prévues -->
                <div class="visit-item" style="display: flex; padding: 16px; border-bottom: 1px solid var(--border); align-items: center;">
                  <div style="min-width: 60px; text-align: center; margin-right: 16px;">
                    <div style="font-size: 24px; font-weight: bold; color: var(--primary);">4</div>
                    <div style="font-size: 12px; color: var(--muted);">Mai</div>
                  </div>
                  <div style="flex: 1;">
                    <div style="font-weight: 600; margin-bottom: 4px;">Mamadou Diallo</div>
                    <div style="font-size: 14px; color: var(--muted); margin-bottom: 2px;">Bamako</div>
                    <div style="font-size: 13px; color: var(--muted);">00:52</div>
                    <div style="font-size: 13px; margin-top: 4px;">Contrôle stock Urée</div>
                  </div>
                  <div>
                    <span style="background: var(--success); color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Planifié</span>
                  </div>
                </div>
                
                <div class="visit-item" style="display: flex; padding: 16px; border-bottom: 1px solid var(--border); align-items: center;">
                  <div style="min-width: 60px; text-align: center; margin-right: 16px;">
                    <div style="font-size: 24px; font-weight: bold; color: var(--primary);">6</div>
                    <div style="font-size: 12px; color: var(--muted);">Mai</div>
                  </div>
                  <div style="flex: 1;">
                    <div style="font-weight: 600; margin-bottom: 4px;">Aminata Touré</div>
                    <div style="font-size: 14px; color: var(--muted); margin-bottom: 2px;">Sikasso</div>
                    <div style="font-size: 13px; color: var(--muted);">00:52</div>
                    <div style="font-size: 13px; margin-top: 4px;">Alerte rendement Riz</div>
                  </div>
                  <div>
                    <span style="background: var(--success); color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Planifié</span>
                  </div>
                </div>
                
                <div class="visit-item" style="display: flex; padding: 16px; border-bottom: 1px solid var(--border); align-items: center;">
                  <div style="min-width: 60px; text-align: center; margin-right: 16px;">
                    <div style="font-size: 24px; font-weight: bold; color: var(--primary);">8</div>
                    <div style="font-size: 12px; color: var(--muted);">Mai</div>
                  </div>
                  <div style="flex: 1;">
                    <div style="font-weight: 600; margin-bottom: 4px;">Bakary Camara</div>
                    <div style="font-size: 14px; color: var(--muted); margin-bottom: 2px;">Kayes</div>
                    <div style="font-size: 13px; color: var(--muted);">00:52</div>
                    <div style="font-size: 13px; margin-top: 4px;">Conseil semis Coton</div>
                  </div>
                  <div>
                    <span style="background: var(--success); color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Planifié</span>
                  </div>
                </div>
              </div>
            </article>
          </section>

          <!-- Section Droite : Urgences -->
          <section class="visits-urgent-section">
            <article class="card urgent-visits">
              <div class="card-header">
                <div>
                  <h3 style="margin:0; font-size:16px;">
                    <span style="color: var(--danger); margin-right: 6px;">⚠️</span>
                    Visites Urgentes
                  </h3>
                  <div class="small muted">Agriculteurs en situation critique</div>
                </div>
                <span class="tag danger">Urgence</span>
              </div>
              
              <div class="urgent-list" id="urgentList">
                <!-- Données initiales des visites urgentes -->
                <div style="display: flex; align-items: center; padding: 12px; border-bottom: 1px solid var(--border);">
                  <div style="width: 8px; height: 8px; background: var(--danger); border-radius: 50%; margin-right: 12px;"></div>
                  <div style="flex: 1;">
                    <div style="font-weight: 600; margin-bottom: 2px;">Mamadou Diallo</div>
                    <div style="font-size: 14px; color: var(--muted); margin-bottom: 2px;">Bamako</div>
                    <div style="font-size: 13px; color: var(--danger);">Stock Urée critique (15%)</div>
                  </div>
                  <button class="btn" style="background: var(--danger); color: white; padding: 6px 12px; border: none; border-radius: 4px; font-size: 12px;" onclick="planUrgentVisit(this, 'Mamadou Diallo', 'Bamako', 'Contrôle stock Urée')">
                    Planifier visite
                  </button>
                </div>
                
                <div style="display: flex; align-items: center; padding: 12px; border-bottom: 1px solid var(--border);">
                  <div style="width: 8px; height: 8px; background: var(--danger); border-radius: 50%; margin-right: 12px;"></div>
                  <div style="flex: 1;">
                    <div style="font-weight: 600; margin-bottom: 2px;">Fatoumata Konaté</div>
                    <div style="font-size: 14px; color: var(--muted); margin-bottom: 2px;">Koulikoro</div>
                    <div style="font-size: 13px; color: var(--danger);">Stock Urée critique (12%)</div>
                  </div>
                  <button class="btn" style="background: var(--danger); color: white; padding: 6px 12px; border: none; border-radius: 4px; font-size: 12px;" onclick="planUrgentVisit(this, 'Fatoumata Konaté', 'Sikasso', 'Alerte rendement Riz')">
                    Planifier visite
                  </button>
                </div>
                
                <div style="display: flex; align-items: center; padding: 12px; border-bottom: 1px solid var(--border);">
                  <div style="width: 8px; height: 8px; background: var(--danger); border-radius: 50%; margin-right: 12px;"></div>
                  <div style="flex: 1;">
                    <div style="font-weight: 600; margin-bottom: 2px;">Mariam Traoré</div>
                    <div style="font-size: 14px; color: var(--muted); margin-bottom: 2px;">Ségou</div>
                    <div style="font-size: 13px; color: var(--danger);">Stock Urée critique (10%)</div>
                  </div>
                  <button class="btn" style="background: var(--danger); color: white; padding: 6px 12px; border: none; border-radius: 4px; font-size: 12px;" onclick="planUrgentVisit(this, 'Mariam Traoré', 'Kayes', 'Conseil semis Coton')">
                    Planifier visite
                  </button>
                </div>
              </div>

              <!-- Widget de Statut -->
              <div class="status-widget">
                <div class="status-summary">
                  <div class="status-number">
                    <span class="status-count" id="urgentCount">3</span>
                    <span class="status-label">Total Urgences</span>
                  </div>
                  <div class="status-tip">
                    Priorité aux stocks d'Urée ce mois-ci
                  </div>
                </div>
              </div>
            </article>
          </section>
        </div>

        <!-- Zone Inférieure : Action -->
        <section class="action-zone">
          <article class="card">
            <div class="card-header">
              <div>
                <h3 style="margin:0; font-size:16px;">Planifier une nouvelle visite</h3>
                <div class="small muted">Ajouter une visite au planning</div>
              </div>
              <span class="tag good">Nouveau</span>
            </div>
            
            <form class="visit-form visit-form-horizontal" id="visitForm">
              <div class="form-row">
                <div class="form-group">
                  <label for="farmerSelect">Agriculteur</label>
                  <select id="farmerSelect" class="form-control" required>
                    <option value="">Sélectionner un agriculteur</option>
                    <option value="mamadou-diallo">Mamadou Diallo (Bamako)</option>
                    <option value="aminata-toure">Aminata Touré (Sikasso)</option>
                    <option value="bakary-camara">Bakary Camara (Kayes)</option>
                    <option value="fatoumata-konate">Fatoumata Konaté (Koulikoro)</option>
                    <option value="ibrahim-bamba">Ibrahim Bamba (Mopti)</option>
                    <option value="mariam-traore">Mariam Traoré (Ségou)</option>
                    <option value="ousmane-konate">Ousmane Konaté (Tombouctou)</option>
                    <option value="aissata-cisse">Aissata Cissé (Gao)</option>
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
                  <div style="display: flex; align-items: center; margin-top: 8px;">
                    <input type="checkbox" id="includeDelivery" name="includeDelivery" style="margin-right: 8px;">
                    <label for="includeDelivery" style="margin: 0; font-weight: normal; color: var(--text-muted);">Inclure une livraison d'intrants</label>
                  </div>
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
      </main>
      <script src="{{ asset('assets/js/layout.js') }}"></script>
    <script src="{{ asset('assets/js/core.js') }}"></script>
    <script src="{{ asset('assets/js/auth.js') }}"></script>
    <script src="{{ asset('assets/js/visits-control.js') }}"></script>
    
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