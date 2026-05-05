<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SeneBI - Espace client</title>
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />
    <style>
      /* Force les styles pour les KPIs du client */
      #kpiTotalHarvest, #kpiCA, #kpiHa, #kpiRend {
        font-weight: bold !important;
        color: inherit !important;
      }
      .kpi-sub span:first-child {
        color: #10b981 !important;
        font-weight: 600 !important;
      }
      
      /* Styles pour la fenêtre modale de commande */
      .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(5px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.3s ease;
      }
      
      .modal-overlay.show {
        opacity: 1;
      }
      
      .modal-content {
        background: white;
        border-radius: 15px;
        padding: 24px;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        transform: translateY(-20px);
        transition: transform 0.3s ease;
      }
      
      .modal-overlay.show .modal-content {
        transform: translateY(0);
      }
      
      .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
      }
      
      .modal-header h2 {
        margin: 0;
        font-size: 20px;
        color: #1a1d23;
      }
      
      .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #64748b;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: background-color 0.2s;
      }
      
      .modal-close:hover {
        background: #f1f5f9;
      }
      
      .modal-body {
        margin-bottom: 24px;
      }
      
      .form-group {
        margin-bottom: 16px;
      }
      
      .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #374151;
      }
      
      .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.2s;
      }
      
      .form-control:focus {
        outline: none;
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
      }
      
      .modal-footer {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
      }
      
      /* Styles pour l'impression */
      @media print {
        .head-actions {
          display: none !important;
        }
        
        .modal-overlay {
          display: none !important;
        }
        
        .footer-note {
          display: none !important;
        }
        
        body {
          background: white !important;
        }
        
        .card {
          box-shadow: none !important;
          border: 1px solid #000 !important;
          break-inside: avoid;
        }
        
        .kpi-value {
          color: #000 !important;
        }
        
        .kpi-sub span {
          color: #000 !important;
        }
      }
    </style>
  </head>
  <body data-page="client-dashboard">
    <div class="app">
      @include('header-client')

      <main class="container">
        <div class="page-title">
          <div>
            <h1>Tableau de Bord Client</h1>
            <p>Suivi de vos performances agricoles et indicateurs clés.</p>
          </div>
          <div class="head-actions" style="display: flex; gap: 10px; align-items: center;">
            <button class="btn" id="orderBtn" type="button" style="background: #2d5016; color: white; border: none; border-radius: 15px; padding: 12px 20px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 2l6 6-6 6"/>
                <path d="M3 12h12"/>
                <circle cx="12" cy="12" r="10"/>
              </svg>
              Passer une commande
            </button>
            <button class="btn" id="clientExportBtn" type="button" style="background: #111827; color: white; border: none; border-radius: 15px; padding: 12px 20px; font-weight: 600; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#1a1a1a'" onmouseout="this.style.backgroundColor='#111827'">Exporter le Rapport PDF</button>
          </div>
        </div>

        <div id="stockAlert" class="alert-banner">
          <div id="stockAlertText">Alerte Stock</div>
          <a class="btn danger" href="{{ route('manager.stocks') }}">Voir le stock</a>
        </div>

        <section class="grid kpis">
          <article class="card" style="cursor: pointer;" onclick="window.location.href='{{ route('client.parcelles') }}'">
            <div class="card-header">
              <p class="card-title">Total Récolté</p>
              <div class="card-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 2v20"/><path d="M7 6c2 2 2 4 0 6"/><path d="M17 6c-2 2-2 4 0 6"/><path d="M7 12c2 2 2 4 0 6"/><path d="M17 12c-2 2-2 4 0 6"/>
                </svg>
              </div>
            </div>
            <div class="kpi-value"><span id="kpiTotalHarvest" style="font-weight: bold !important;">2,450</span> <span class="muted" style="font-size:14px;font-weight:700;">kg</span></div>
            <div class="kpi-sub">
              <span style="color: #10b981 !important; font-weight: 600 !important;">+12.5% vs. période précédente</span>
              <span class="muted">Données certifiées SeneBI</span>
            </div>
          </article>

          <article class="card">
            <div class="card-header">
              <p class="card-title">Chiffre d'Affaires estimé</p>
              <div class="card-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/><path d="M12 7v10"/><path d="M9.5 9.5c.6-1 4.4-1 5 0"/><path d="M9.5 14.5c.6 1 4.4-1 5 0"/>
                </svg>
              </div>
            </div>
            <div class="kpi-value"><span id="kpiCA" style="font-weight: bold !important;">1,225,000</span> <span class="muted" style="font-size:14px;font-weight:700;">FCFA</span></div>
            <div class="kpi-sub">
              <span style="color: #10b981 !important; font-weight: 600 !important;">+8.3% vs. période précédente</span>
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
            <div class="kpi-value"><span id="kpiHa" style="font-weight: bold !important;">3</span> <span class="muted" style="font-size:14px;font-weight:700;">ha</span></div>
            <div class="kpi-sub">
              <span>+5.2% vs. période précédente</span>
              <span class="muted">Parcelles hors jachère</span>
            </div>
          </article>

          
          <article class="card">
            <div class="card-header">
              <p class="card-title">Rendement Moyen</p>
              <div class="card-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 17l6-6 4 4 7-7"/><path d="M14 8h6v6"/>
                </svg>
              </div>
            </div>
            <div class="kpi-value"><span id="kpiRend" style="font-weight: bold !important;">0.82</span> <span class="muted" style="font-size:14px;font-weight:700;">t/ha</span></div>
            <div class="kpi-sub">
              <span>+3.1% vs. période précédente</span>
              <span class="muted">Moyenne sur parcelles récoltées</span>
            </div>
          </article>
        </section>

        <div class="weather-widget">
            <div class="weather-content">
                <div class="weather-icon">☀️</div>
                <div class="weather-info">
                    <div class="weather-location">Sikasso</div>
                    <div class="weather-temp">32°C</div>
                    <div class="weather-condition">Temps sec</div>
                    <div style="font-weight: 600; margin-top: 4px; color: #10b981;">Conseil SeneBI : Conditions optimales pour le traitement des sols.</div>
                </div>
            </div>
        </div>

        <p id="dashboardInsight" class="dashboard-insight" role="status"></p>

        <section class="grid cards-2">
          <article class="card" style="min-height: 320px;">
            <div class="card-header">
              <div>
                <h3 style="margin:0; font-size:16px;">Évolution du Prix des Céréales</h3>
                <div class="small muted">Courbe dynamique (Chart.js)</div>
              </div>
              <span class="tag muted">FCFA/kg</span>
            </div>
            <div class="cereal-price-toolbar">
              <label class="cereal-price-label" for="cerealPriceSelect">Culture affichée</label>
              <select id="cerealPriceSelect" class="cereal-price-select" aria-label="Choisir la culture pour la courbe de prix">
                <option value="Riz">Riz</option>
                <option value="Maïs">Maïs</option>
                <option value="Coton">Coton</option>
              </select>
            </div>
            <div style="height: 260px;">
              <canvas id="priceChart"></canvas>
            </div>
          </article>

          <article class="card" style="min-height: 320px;">
            <div class="card-header">
              <div>
                <h3 style="margin:0; font-size:16px;">Distribution des Cultures</h3>
                <div class="small muted">Riz / Maïs / Coton</div>
              </div>
              <span class="tag good" id="dominantCulture">Riz</span>
            </div>
            <div style="height: 260px;">
              <canvas id="cultureChart"></canvas>
            </div>
          </article>

        </section>

        <div class="footer-note">Astuce : Les prix affichés sont basés sur les cours actuels des marchés de Bamako et Sikasso.</div>
      </main>
      <div data-layout="footer"></div>
    </div>

    <!-- Fenêtre Modale de Commande -->
    <div id="orderModal" class="modal-overlay" style="display: none;">
      <div class="modal-content">
        <div class="modal-header">
          <h2>Nouvelle Commande SeneBI</h2>
          <button class="modal-close" id="closeModal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="cultureSelect">Culture</label>
            <select id="cultureSelect" class="form-control">
              <option value="Riz">Riz</option>
              <option value="Maïs">Maïs</option>
              <option value="Coton">Coton</option>
            </select>
          </div>
          <div class="form-group">
            <label for="quantityInput">Quantité (kg)</label>
            <input type="number" id="quantityInput" class="form-control" placeholder="Entrez la quantité" min="1">
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn" id="confirmOrder" style="background: #2d5016; color: white; border: none; border-radius: 15px;">Confirmer l'achat</button>
          <button class="btn secondary" id="cancelOrder">Annuler</button>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <script src="{{ asset('assets/js/core.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    
    <!-- DÉTECTION DES PARAMÈTRES URL POUR OUVERTURE AUTO MODAL -->
    <script>
      // Vérifier si on doit ouvrir le modal de commande (depuis stocks)
      window.addEventListener('load', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const openModal = urlParams.get('openModal');
        const reason = urlParams.get('reason');
        
        if (openModal === 'true' && reason === 'stock_critical') {
          console.log("🔗 Ouverture auto du modal de commande (stock critique)");
          
          // Attendre un peu que la page soit complètement chargée
          setTimeout(function() {
            const modal = document.querySelector('#orderModal');
            if (modal) {
              // Utiliser la même méthode que le bouton "Passer commande"
              modal.style.display = 'flex';
              modal.setAttribute('aria-hidden', 'false');
              setTimeout(() => {
                modal.classList.add('show');
              }, 10);
              console.log("✅ Modal de commande ouvert et centré automatiquement pour stock critique");
            } else {
              console.log("❌ Modal #orderModal non trouvé sur client-dashboard");
            }
          }, 1000);
        }
      });
    </script>
    
    <!-- Script pour forcer les styles avec MutationObserver -->
    <script>
      console.log("🚀 Script KPI Styles chargé !");
      
      function applyKPIStyles() {
        console.log("⚡ Application des styles KPI...");
        
        // Forcer les styles des chiffres en gras
        const kpiElements = ['#kpiTotalHarvest', '#kpiCA', '#kpiHa', '#kpiRend'];
        kpiElements.forEach(id => {
          const el = document.querySelector(id);
          if (el) {
            el.style.cssText = 'font-weight: bold !important; color: inherit !important;';
            console.log(`✅ Style gras FORCÉ sur ${id}: ${el.textContent}`);
          } else {
            console.log(`❌ Élément ${id} non trouvé`);
          }
        });
        
        // Forcer les styles des badges verts
        const kpiSubElements = document.querySelectorAll('.kpi-sub span:first-child');
        kpiSubElements.forEach((el, index) => {
          el.style.cssText = 'color: #10b981 !important; font-weight: 600 !important;';
          console.log(`✅ Style vert FORCÉ sur badge ${index + 1}: ${el.textContent}`);
        });
        
        console.log("🎯 Styles KPI appliqués avec succès !");
      }
      
      // Attendre que la page soit complètement chargée
      window.addEventListener('load', function() {
        console.log("📄 Page complètement chargée !");
        setTimeout(applyKPIStyles, 100);
      });
      
      // Surveiller les changements sur les KPIs
      const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
          if (mutation.type === 'childList' || mutation.type === 'characterData') {
            const target = mutation.target;
            if (target.id && ['kpiTotalHarvest', 'kpiCA', 'kpiHa', 'kpiRend'].includes(target.id)) {
              console.log(`🔄 Changement détecté sur ${target.id}, réapplication des styles...`);
              setTimeout(applyKPIStyles, 50);
            }
          }
        });
      });
      
      // Démarrer l'observation après le chargement
      setTimeout(() => {
        const kpiContainer = document.querySelector('.kpis');
        if (kpiContainer) {
          observer.observe(kpiContainer, {
            childList: true,
            subtree: true,
            characterData: true
          });
          console.log("👁️ MutationObserver démarré sur les KPIs !");
        }
        
        // Application initiale
        applyKPIStyles();
      }, 200);
      
      // Logique pour la fenêtre modale de commande
      const orderModal = document.getElementById('orderModal');
      const clientExportBtn = document.getElementById('clientExportBtn');
      const caEl = document.querySelector("#kpiCA");
      const haEl = document.querySelector("#kpiHa");
      const rendEl = document.querySelector("#kpiRend");
      
      // Fonction d'export PDF avec window.print()
      clientExportBtn.addEventListener('click', function() {
        window.print();
      });
      const orderBtn = document.getElementById('orderBtn');
      const closeModal = document.getElementById('closeModal');
      const cancelOrder = document.getElementById('cancelOrder');
      const confirmOrder = document.getElementById('confirmOrder');
      const cultureSelect = document.getElementById('cultureSelect');
      const quantityInput = document.getElementById('quantityInput');
      
      // Ouvrir la modale
      orderBtn.addEventListener('click', function() {
        orderModal.style.display = 'flex';
        setTimeout(() => {
          orderModal.classList.add('show');
        }, 10);
      });
      
      // Fermer la modale
      function closeModalFunc() {
        orderModal.classList.remove('show');
        setTimeout(() => {
          orderModal.style.display = 'none';
        }, 300);
      }
      
      closeModal.addEventListener('click', closeModalFunc);
      cancelOrder.addEventListener('click', closeModalFunc);
      
      // Confirmer la commande
      confirmOrder.addEventListener('click', function() {
        const culture = cultureSelect.value;
        const quantity = quantityInput.value;
        
        if (!culture || !quantity || quantity < 1) {
          alert('Veuillez remplir tous les champs correctement.');
          return;
        }
        
        // Afficher le message de succès
        const successMessage = document.createElement('div');
        successMessage.textContent = `Commande envoyée avec succès à Sidi !`;
        successMessage.style.cssText = `
          position: fixed;
          top: 20px;
          right: 20px;
          background: #10b981;
          color: white;
          padding: 12px 20px;
          border-radius: 12px;
          font-size: 14px;
          font-weight: 500;
          z-index: 1001;
          opacity: 0;
          transform: translateY(20px);
          transition: all 0.3s ease;
        `;
        
        document.body.appendChild(successMessage);
        
        // Animation d'apparition
        setTimeout(() => {
          successMessage.style.opacity = '1';
          successMessage.style.transform = 'translateY(0)';
        }, 100);
        
        // Fermer la modale
        closeModalFunc();
        
        // Masquer le message après 3 secondes
        setTimeout(() => {
          successMessage.style.opacity = '0';
          successMessage.style.transform = 'translateY(20px)';
          setTimeout(() => {
            document.body.removeChild(successMessage);
          }, 300);
        }, 3000);
        
        // Réinitialiser le formulaire
        cultureSelect.value = 'Riz';
        quantityInput.value = '';
      });
      
      // Fermer en cliquant sur le fond
      orderModal.addEventListener('click', function(e) {
        if (e.target === orderModal) {
          closeModalFunc();
        }
      });
    </script>
  </body>
</html>
