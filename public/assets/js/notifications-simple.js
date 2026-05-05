// SeneBI - Widget de notifications simplifié
(function() {
    'use strict';
    
    console.log('Chargement du widget notifications...');
    
    // Attendre que le DOM soit chargé
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            console.log('DOM chargé, vérification du rôle...');
            
            // Vérifier si l'utilisateur est un manager
            const auth = window.SeneBI?.getAuth?.();
            if (!auth || auth.role !== 'manager') {
                console.log('Utilisateur non-manager, pas de notifications');
                return; // Ne pas créer le widget pour les clients
            }
            
            console.log('Manager détecté, création du widget...');
            
            // Créer le widget de notifications
            const widget = document.createElement('div');
            widget.innerHTML = `
                <div class="notifications-bell" id="notificationsBell">
                    <div class="bell-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
                            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
                            <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                        </svg>
                    </div>
                    <div class="notification-count">2</div>
                </div>
                <div class="notifications-dropdown" id="notificationsDropdown">
                    <div class="notifications-header">
                        <h3>Notifications</h3>
                    </div>
                    <div class="notifications-list">
                        <div class="notification-item warning" onclick="window.location.href='/manager/stocks'">
                            <div class="notification-icon">⚠️</div>
                            <div class="notification-content">
                                <div class="notification-title">Alerte Stock</div>
                                <div class="notification-message">Stock de Maïs faible à Ségou !</div>
                                <div class="notification-time">Il y a 2h</div>
                            </div>
                        </div>
                        <div class="notification-item info" onclick="window.location.href='/secure-portal'">
                            <div class="notification-icon">👤</div>
                            <div class="notification-content">
                                <div class="notification-title">Nouvelle Visite</div>
                                <div class="notification-message">Nouvelle visite enregistrée par Ouane.</div>
                                <div class="notification-time">Il y a 5h</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Ajouter les styles
            const style = document.createElement('style');
            style.textContent = `
                .notifications-bell {
                    position: relative;
                    cursor: pointer;
                    padding: 8px;
                    border-radius: 50%;
                    background: rgba(16, 185, 129, 0.1);
                    transition: all 0.3s ease;
                    margin-right: 16px;
                }
                
                .notifications-bell:hover {
                    background: rgba(16, 185, 129, 0.2);
                    transform: scale(1.05);
                }
                
                .bell-icon {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: #10b981;
                }
                
                .notification-count {
                    position: absolute;
                    top: -2px;
                    right: -2px;
                    background: #ef4444;
                    color: white;
                    font-size: 11px;
                    font-weight: 600;
                    padding: 2px 5px;
                    border-radius: 10px;
                    min-width: 18px;
                    text-align: center;
                    box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
                }
                
                .notifications-dropdown {
                    position: absolute;
                    top: 100%;
                    right: 0;
                    width: 320px;
                    background: white;
                    border-radius: 12px;
                    box-shadow: 0 8px 32px rgba(15, 23, 42, 0.15);
                    border: 1px solid rgba(15, 23, 42, 0.1);
                    z-index: 1000;
                    opacity: 0;
                    visibility: hidden;
                    transform: translateY(-10px);
                    transition: all 0.3s ease;
                }
                
                .notifications-dropdown.show {
                    opacity: 1;
                    visibility: visible;
                    transform: translateY(8px);
                }
                
                .notifications-header {
                    padding: 16px;
                    border-bottom: 1px solid rgba(15, 23, 42, 0.1);
                }
                
                .notifications-header h3 {
                    margin: 0;
                    font-size: 16px;
                    font-weight: 600;
                    color: #374151;
                }
                
                .notification-item {
                    display: flex;
                    align-items: flex-start;
                    padding: 12px 16px;
                    border-bottom: 1px solid rgba(15, 23, 42, 0.05);
                    cursor: pointer;
                    transition: background 0.2s ease;
                }
                
                .notification-item:hover {
                    background: rgba(16, 185, 129, 0.05);
                }
                
                .notification-item.warning {
                    border-left: 3px solid #f59e0b;
                }
                
                .notification-item.info {
                    border-left: 3px solid #3b82f6;
                }
                
                .notification-icon {
                    font-size: 16px;
                    margin-right: 12px;
                    margin-top: 2px;
                }
                
                .notification-content {
                    flex: 1;
                }
                
                .notification-title {
                    font-weight: 600;
                    font-size: 13px;
                    color: #374151;
                    margin-bottom: 4px;
                }
                
                .notification-message {
                    font-size: 12px;
                    color: #6b7280;
                    line-height: 1.4;
                    margin-bottom: 4px;
                }
                
                .notification-time {
                    font-size: 11px;
                    color: #9ca3af;
                }
            `;
            document.head.appendChild(style);
            
            // Trouver où insérer le widget
            const topbarActions = document.querySelector('.topbar-actions');
            const nav = document.querySelector('.nav');
            const header = document.querySelector('header');
            
            console.log('Éléments trouvés:', {
                topbarActions: !!topbarActions,
                nav: !!nav,
                header: !!header
            });
            
            // Insérer le widget
            if (topbarActions) {
                topbarActions.insertBefore(widget, topbarActions.firstChild);
                console.log('Widget inséré dans topbar-actions');
            } else if (nav && nav.parentElement) {
                nav.parentElement.insertBefore(widget, nav.nextSibling);
                console.log('Widget inséré après la navigation');
            } else if (header) {
                header.appendChild(widget);
                console.log('Widget inséré dans le header');
            } else {
                console.log('Impossible d insérer le widget');
                return;
            }
            
            // Gérer le clic sur la cloche
            const bell = document.getElementById('notificationsBell');
            const dropdown = document.getElementById('notificationsDropdown');
            
            if (bell && dropdown) {
                bell.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.classList.toggle('show');
                    console.log('Clic sur la cloche, dropdown show:', dropdown.classList.contains('show'));
                });
                
                // Fermer en cliquant dehors
                document.addEventListener('click', function() {
                    dropdown.classList.remove('show');
                });
                
                // Empêcher la propagation dans le dropdown
                dropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
            
        }, 500); // Attendre 500ms pour être sûr que tout est chargé
    });
    
})();
