// SeneBI - Correction du bug bouton "Appliquer Intrant" lors du filtrage par région
(function() {
    'use strict';
    
    class RegionParcellesFilter {
        constructor() {
            this.currentRegion = 'all';
            this.originalCards = [];
            this.init();
        }

        init() {
            this.bindEvents();
            this.preserveOriginalCards();
        }

        preserveOriginalCards() {
            // Sauvegarder toutes les cartes originales avec leur HTML complet
            const cards = document.querySelectorAll('.parcel-card');
            this.originalCards = Array.from(cards).map(card => ({
                element: card,
                html: card.outerHTML,
                region: this.getCardRegion(card)
            }));
        }

        getCardRegion(card) {
            // Déterminer la région de la carte basée sur son nom
            const cardName = card.querySelector('.parcel-name')?.textContent || '';
            const regionMapping = {
                // RÉGION BAMAKO
                'Parcelle Bamako Nord-1': 'bko',
                'Parcelle Bamako Sud-1': 'bko',
                'Parcelle Bamako Centre-1': 'bko',
                
                // RÉGION KAYES
                'Parcelle Kayes Nord-1': 'kay',
                'Parcelle Kayes Sud-1': 'kay',
                'Parcelle Kayes Est-1': 'kay',
                
                // RÉGION KOULIKORO
                'Parcelle Koulikoro Nord-1': 'kou',
                'Parcelle Koulikoro Sud-1': 'kou',
                'Parcelle Koulikoro Centre-1': 'kou',
                
                // RÉGION SÉGOU
                'Parcelle Ségou Nord-1': 'seg',
                'Parcelle Ségou Sud-1': 'seg',
                'Parcelle Ségou Est-1': 'seg',
                
                // RÉGION SIKASSO
                'Parcelle Sikasso Nord-1': 'sik',
                'Parcelle Sikasso Sud-1': 'sik',
                'Parcelle Sikasso Centre-1': 'sik',
                
                // RÉGION Mopti
                'Parcelle Mopti Nord-1': 'mop',
                'Parcelle Mopti Sud-1': 'mop',
                'Parcelle Mopti Est-1': 'mop',
                
                // RÉGION TOMBOUCTOU
                'Parcelle Tombouctou Nord-1': 'tom',
                'Parcelle Tombouctou Sud-1': 'tom',
                'Parcelle Tombouctou Est-1': 'tom',
                
                // RÉGION GAO
                'Parcelle Gao Nord-1': 'gao',
                'Parcelle Gao Sud-1': 'gao',
                'Parcelle Gao Est-1': 'gao',
                
                // RÉGION KIDAL
                'Parcelle Kidal Nord-1': 'kid',
                'Parcelle Kidal Sud-1': 'kid',
                'Parcelle Kidal Est-1': 'kid',
                
                // Parcelles existantes (compatibilité)
                'Parcelle Nord': 'bko',
                'Parcelle Sud': 'kay', 
                'Parcelle Centre': 'kou',
                'Parcelle Est': 'seg',
                'Parcelle Ouest': 'sik'
            };
            
            for (const [name, region] of Object.entries(regionMapping)) {
                if (cardName.includes(name)) {
                    return region;
                }
            }
            return 'all';
        }

        bindEvents() {
            const regionSelect = document.getElementById('regionSelectParcel');
            if (regionSelect) {
                // Remplacer l'écouteur d'événement existant
                regionSelect.removeEventListener('change', this.handleRegionChange);
                this.handleRegionChange = (e) => {
                    this.currentRegion = e.target.value;
                    this.filterCards();
                    this.verifyButtonsIntegrity();
                    this.showRegionNotification();
                };
                regionSelect.addEventListener('change', this.handleRegionChange);
            }
        }

        filterCards() {
            const cards = document.querySelectorAll('.parcel-card');
            
            cards.forEach(card => {
                const cardRegion = this.getCardRegion(card);
                const shouldShow = this.currentRegion === 'all' || cardRegion === this.currentRegion;
                
                // Masquer/afficher la carte au lieu de la supprimer
                card.style.display = shouldShow ? 'block' : 'none';
                card.setAttribute('aria-hidden', !shouldShow);
                
                // Forcer la préservation du bouton "Appliquer Intrant"
                if (shouldShow) {
                    this.ensureButtonExists(card);
                }
            });
        }

        ensureButtonExists(card) {
            // Vérifier si la carte a déjà un bouton "Appliquer Intrant"
            const existingButton = card.querySelector('.apply-intrant-btn');
            const statusBadge = card.querySelector('.badge');
            const isFallow = statusBadge && statusBadge.textContent === 'En jachère';
            
            // Si la carte n'est pas en jachère et n'a pas de bouton, l'ajouter
            if (!isFallow && !existingButton) {
                const parcelActions = card.querySelector('.parcel-actions');
                if (!parcelActions) {
                    // Créer le conteneur de boutons s'il n'existe pas
                    const actionsContainer = document.createElement('div');
                    actionsContainer.className = 'parcel-actions';
                    
                    // Créer le bouton
                    const button = document.createElement('button');
                    button.className = 'apply-intrant-btn';
                    button.textContent = 'Appliquer Intrant';
                    button.onclick = () => window.location.href = '/manager/stocks';
                    
                    actionsContainer.appendChild(button);
                    card.appendChild(actionsContainer);
                }
            }
        }

        verifyButtonsIntegrity() {
            // Vérification supplémentaire pour s'assurer que toutes les cartes visibles ont leur bouton
            const visibleCards = document.querySelectorAll('.parcel-card:not([style*="display: none"])');
            
            visibleCards.forEach(card => {
                setTimeout(() => {
                    this.ensureButtonExists(card);
                }, 100); // Délai pour assurer que le DOM est stabilisé
            });
        }

        showRegionNotification() {
            // Notification de changement de région
            const regionNames = {
                'all': 'Toutes les régions',
                'bko': 'Bamako',
                'kay': 'Kayes',
                'kou': 'Koulikoro',
                'seg': 'Ségou',
                'sik': 'Sikasso',
                'mop': 'Mopti',
                'tom': 'Tombouctou',
                'gao': 'Gao',
                'kid': 'Kidal'
            };

            const notification = document.createElement('div');
            notification.className = 'region-notification';
            notification.innerHTML = `
                <div class="notification-content">
                    <span class="notification-icon">📍</span>
                    <span>Filtrage appliqué : ${regionNames[this.currentRegion]}</span>
                    <span class="notification-close">×</span>
                </div>
            `;
            
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: linear-gradient(135deg, #00a65a 0%, #008c4d 100%);
                color: white;
                padding: 12px 20px;
                border-radius: 12px;
                font-size: 14px;
                font-weight: 500;
                z-index: 1001;
                opacity: 0;
                transform: translateY(-20px);
                transition: all 0.3s ease;
                box-shadow: 0 4px 12px rgba(0, 166, 90, 0.3);
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '1';
                notification.style.transform = 'translateY(0)';
            }, 10);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(-20px)';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Méthode pour réinitialiser le filtre
        reset() {
            this.currentRegion = 'all';
            const regionSelect = document.getElementById('regionSelectParcel');
            if (regionSelect) {
                regionSelect.value = 'all';
            }
            this.filterCards();
            this.verifyButtonsIntegrity();
        }
    }

    // Créer l'instance globale
    window.regionParcellesFilter = new RegionParcellesFilter();

    // Surveiller les changements DOM pour les nouvelles cartes
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                // Vérifier les nouvelles cartes ajoutées
                setTimeout(() => {
                    window.regionParcellesFilter.verifyButtonsIntegrity();
                }, 200);
            }
        });
    });

    // Observer le conteneur des cartes
    const cardsContainer = document.querySelector('.parcels-grid');
    if (cardsContainer) {
        observer.observe(cardsContainer, {
            childList: true,
            subtree: true
        });
    }

})();
