<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SeneBI — Parcelles</title>
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/parcelles.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/region-filter.css') }}" />
    
    <!-- CSS pour Widget Météo -->
    <style>
        /* Widget Météo - Style intégré SeneBI */
        .weather-widget {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            margin-right: 12px;
        }

        .weather-widget:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .weather-content {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .weather-icon {
            font-size: 16px;
            color: #1a1d23; /* Couleur sombre comme le menu */
        }

        .weather-text {
            font-size: 12px;
            font-weight: 500;
            color: #475569; /* Gris moderne */
            white-space: nowrap;
        }

        /* Conseil du jour - Style vert émeraude */
        .weather-advice {
            margin-top: 8px;
            font-size: 13px;
            font-weight: bold;
            color: #10b981; /* Vert émeraude comme le badge 'En culture' */
            display: flex;
            align-items: center;
            gap: 6px;
            line-height: 1.4;
        }

        .weather-advice .advice-icon {
            color: #10b981; /* Vert émeraude comme le texte */
            font-size: 14px;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Bouton Appliquer Intrant - Style SeneBI moderne */
        .apply-intrant-btn {
            background: transparent;
            border: 1.5px solid #00a65a;
            color: #00a65a;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 6px 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: absolute;
            bottom: 12px;
            right: 12px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            white-space: nowrap;
        }

        .apply-intrant-btn:hover {
            background: #00a65a;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 166, 90, 0.3);
        }

        .apply-intrant-btn:active {
            transform: translateY(0);
        }

        /* Conteneur pour le bouton */
        .parcel-actions {
            position: absolute;
            bottom: 12px;
            right: 12px;
            z-index: 10;
        }

        /* CORRECTION : Position relative pour les cartes parcelles */
        .parcel-card {
            position: relative !important;
        }
        
        /* Responsive pour petits écrans */
        @media (max-width: 768px) {
            .weather-widget {
                margin-left: 0;
                margin-top: 12px;
                padding: 6px 12px;
            }

            .weather-text {
                font-size: 12px;
            }

            .weather-icon {
                font-size: 16px;
            }

            .weather-advice {
                font-size: 13px;
                margin-top: 8px;
            }
        }

        /* Variations selon la météo */
        .weather-widget.cloudy {
            background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
            border-color: #64748b;
        }

        .weather-widget.cloudy .weather-text {
            color: #1e293b;
        }

        .weather-widget.rainy {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            border-color: #0891b2;
        }

        .weather-widget.rainy .weather-text {
            color: #164e63;
        }

        /* Barre de progression de croissance */
        .parcel-growth {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 8px;
            margin-bottom: 12px;
        }

        .growth-bar {
            flex: 1;
            height: 6px;
            background: #f1f5f9; /* Fond gris très clair */
            border-radius: 3px;
            overflow: hidden;
        }

        .growth-fill {
            height: 100%;
            background: #10b981; /* Vert 'En culture' */
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        .growth-text {
            font-size: 12px;
            color: #64748b; /* Gris */
            font-weight: 500;
            white-space: nowrap;
        }

        /* Indicateur de performance */
        .performance-indicator {
            margin-left: 6px;
            cursor: help;
            transition: transform 0.2s ease;
        }

        .performance-indicator:hover {
            transform: scale(1.1);
        }

        .performance-indicator svg {
            width: 16px;
            height: 16px;
        }

        /* Date de semis */
        .planting-date {
            font-size: 11px;
            color: #64748b; /* Gris discret */
            font-weight: 400;
            margin-top: 2px;
            line-height: 1.3;
        }
    </style>
  </head>
  <body data-page="parcels">
    <div class="app">
     @include('header-client')  

      <main class="container">
        <div class="page-hero">
          <div>
            <h1 class="hero-title">Gestion des Parcelles & Récoltes</h1>
            <p class="hero-subtitle">Suivi de la production et des rendements</p>
            <p class="weather-advice" id="weatherAdvice">
              <span class="advice-icon">💡</span>
              <span>Conseil du jour : C'est le moment idéal pour l'arrosage de la Parcelle Nord.</span>
            </p>
          </div>
          <div class="hero-actions">
            <div class="region-selector">
              <label for="regionSelectParcel" class="region-label">Région</label>
              <select id="regionSelectParcel" class="region-dropdown">
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
          <div class="hero-actions">
            <!-- Widget Météo Dynamique -->
            <div class="weather-widget" id="weatherWidget">
              <div class="weather-content">
                <span class="weather-icon">☀️</span>
                <span class="weather-text">Météo Bamako : 34°C - Ensoleillé</span>
              </div>
            </div>
            
            <button class="action-btn" id="openHarvestBtn" type="button">
              <span class="action-plus">+</span>
              <span>Saisir une récolte</span>
            </button>
          </div>
        </div>

        <section class="card harvest-panel" id="harvestPanel" aria-hidden="true">
          <h2 class="panel-title">Nouvelle Récolte</h2>

          <form class="panel-form" id="harvestForm">
            <div class="panel-field">
                <label for="client-name">Nom du client</label>
                <input id="client-name" type="text" placeholder="Ex: Sidi, Aminata, Mamadou" required />
              </div>
              
              <div class="panel-field">
                <label for="parcelle-recoltee">Parcelle</label>
                <select id="parcelle-recoltee" required>
                  <option value="">Sélectionner une parcelle</option>
                </select>
              </div>

            <div class="panel-grid-2">
              <div class="panel-field">
                <label for="date-recolte">Date de récolte</label>
                <div class="input-icon">
                  <input id="date-recolte" type="date" required />
                  <span class="icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="3" y="4" width="18" height="18" rx="4"></rect>
                      <path d="M16 2v4M8 2v4"></path>
                      <path d="M3 10h18"></path>
                    </svg>
                  </span>
                </div>
              </div>
              <div class="panel-field">
                <label for="quantite-recoltee">Poids récolté (kg)</label>
                <input id="quantite-recoltee" type="number" min="1" step="1" placeholder="Ex: 15000" required />
              </div>
            </div>

            <div class="panel-actions">
              <button class="btn-primary" type="submit">Enregistrer</button>
              <button class="btn-ghost" id="cancelHarvestBtn" type="button">Annuler</button>
            </div>
          </form>
        </section>

        <section class="parcels-list" id="parcelsList"></section>
      </main>
      <div data-layout="footer"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <script src="{{ asset('assets/js/core.js') }}"></script>
    <script src="{{ asset('assets/js/parcelles.js') }}"></script>
    <script src="{{ asset('assets/js/region-filter.js') }}"></script>
    <script src="{{ asset('assets/js/region-rentabilite.js') }}"></script>
    <script src="{{ asset('assets/js/region-parcelles-fix.js') }}"></script>
    
    <!-- Script pour sauvegarder les récoltes dans le localStorage partagé -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // FONCTION DE SAUVEGARDE INTELLIGENTE MULTI-CLIENTS
        function saveHarvestToSharedStorage(harvestData) {
          try {
            // Récupérer la liste des récoltes déjà existante (si elle existe)
            let existingHarvests = JSON.parse(localStorage.getItem('total_recolte_senebi') || '[]');
            
            // Créer la nouvelle récolte avec timestamp et infos complètes
            const newHarvest = {
              ...harvestData,
              timestamp: new Date().toISOString(),
              saison: '2026', // Saison actuelle
              id: Date.now() // ID unique pour cette récolte
            };
            
            // Ajouter la nouvelle récolte à la liste existante (au lieu d'écraser)
            existingHarvests.push(newHarvest);
            
            // Sauvegarder la liste mise à jour
            localStorage.setItem('total_recolte_senebi', JSON.stringify(existingHarvests));
            
            // Calculer et sauvegarder les totaux par client
            const totalsByClient = {};
            existingHarvests.forEach(harvest => {
              const client = harvest.client || 'Inconnu';
              if (!totalsByClient[client]) {
                totalsByClient[client] = 0;
              }
              totalsByClient[client] += parseFloat(harvest.quantite || 0);
            });
            
            // Sauvegarder les totaux par client
            localStorage.setItem('totaux_par_client', JSON.stringify(totalsByClient));
            
            // Calculer et sauvegarder le total général
            const totalQuantity = existingHarvests.reduce((sum, harvest) => sum + parseFloat(harvest.quantite || 0), 0);
            localStorage.setItem('total_quantite_recoltee', totalQuantity.toString());
            
            console.log('✅ Récolte sauvegardée pour le client:', harvestData.client);
            console.log('📊 Total cumulé des récoltes:', totalQuantity + ' kg');
            console.log('👥 Totaux par client:', totalsByClient);
            
            return true;
          } catch (error) {
            console.error('❌ Erreur lors de la sauvegarde de la récolte:', error);
            return false;
          }
        }
        
        // INTERCEPTER LA SOUMISSION DU FORMULAIRE DE RÉCOLTE
        const harvestForm = document.getElementById('harvestForm');
        if (harvestForm) {
          // Remplacer l'événement submit par notre fonction personnalisée
          harvestForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Empêcher la soumission normale
            
            // Récupérer les valeurs du formulaire avec les nouveaux IDs
            const clientName = document.getElementById('client-name')?.value || '';
            const parcelle = document.getElementById('parcelle-recoltee')?.value || '';
            const date = document.getElementById('date-recolte')?.value || '';
            const quantite = document.getElementById('quantite-recoltee')?.value || '';
            
            // Validation
            if (!clientName || !parcelle || !date || !quantite || quantite <= 0) {
              alert('Veuillez remplir tous les champs correctement.');
              return;
            }
            
            // Créer l'objet de données de récolte
            const harvestData = {
              client: clientName, // Nom du client dynamique
              parcelle: parcelle,
              date: date,
              quantite: parseFloat(quantite),
              culture: getCultureFromParcelle(parcelle), // Fonction helper
              rendementEstime: calculateRendementEstime(parcelle, parseFloat(quantite)) // Fonction helper
            };
            
            // Sauvegarder dans le localStorage partagé
            const success = saveHarvestToSharedStorage(harvestData);
            
            if (success) {
              // Afficher un message de succès
              showSuccessMessage('Récolte enregistrée avec succès ! Données transmises au manager SeneBI.');
              
              // Réinitialiser le formulaire
              harvestForm.reset();
              
              // Fermer le panneau de récolte
              const harvestPanel = document.getElementById('harvestPanel');
              if (harvestPanel) {
                harvestPanel.setAttribute('aria-hidden', 'true');
                harvestPanel.style.display = 'none';
              }
              
              // Mettre à jour l'affichage des parcelles si nécessaire
              setTimeout(() => {
                if (window.refreshParcelsList) {
                  window.refreshParcelsList();
                }
              }, 500);
            } else {
              alert('Erreur lors de l\'enregistrement de la récolte. Veuillez réessayer.');
            }
          });
        }
        
        // FONCTION HELPER : Obtenir la culture depuis la parcelle
        function getCultureFromParcelle(parcelleName) {
          // Logique pour déterminer la culture basée sur le nom de la parcelle
          if (parcelleName.toLowerCase().includes('riz') || parcelleName.toLowerCase().includes('nord')) {
            return 'Riz';
          } else if (parcelleName.toLowerCase().includes('mais') || parcelleName.toLowerCase().includes('sud')) {
            return 'Maïs';
          } else if (parcelleName.toLowerCase().includes('coton') || parcelleName.toLowerCase().includes('centre')) {
            return 'Coton';
          }
          return 'Non spécifiée';
        }
        
        // FONCTION HELPER : Calculer le rendement estimé
        function calculateRendementEstime(parcelleName, quantite) {
          // Surface estimée par parcelle (à adapter selon vos données)
          const surfaceEstimee = {
            'Parcelle Nord': 5.5,
            'Parcelle Centre': 6.0,
            'Parcelle Sud': 3.2
          };
          
          const surface = surfaceEstimee[parcelleName] || 5.0; // Valeur par défaut
          return surface > 0 ? (quantite / surface).toFixed(2) : 0;
        }
        
        // FONCTION D'AFFICHAGE DU MESSAGE DE SUCCÈS
        function showSuccessMessage(message) {
          // Créer l'élément de message
          const successDiv = document.createElement('div');
          successDiv.textContent = message;
          successDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 16px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            z-index: 1002;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            max-width: 400px;
            line-height: 1.4;
          `;
          
          document.body.appendChild(successDiv);
          
          // Animation d'apparition
          setTimeout(() => {
            successDiv.style.opacity = '1';
            successDiv.style.transform = 'translateY(0)';
          }, 100);
          
          // Masquer après 4 secondes
          setTimeout(() => {
            successDiv.style.opacity = '0';
            successDiv.style.transform = 'translateY(-20px)';
            setTimeout(() => {
              if (document.body.contains(successDiv)) {
                document.body.removeChild(successDiv);
              }
            }, 300);
          }, 4000);
        }
        
        console.log('🔄 Script de sauvegarde des récoltes client chargé avec succès');
      });
    </script>
  </body>
</html>
