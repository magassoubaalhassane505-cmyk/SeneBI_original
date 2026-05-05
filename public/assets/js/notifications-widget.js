// SeneBI - Widget de notifications et météo - Module additionnel
(function() {
    'use strict';
    
    class NotificationsWidget {
        constructor() {
            this.notifications = [
                {
                    type: 'warning',
                    icon: '⚠️',
                    title: 'Alerte Stock',
                    message: 'Stock de Maïs faible à Ségou !',
                    time: 'Il y a 2h',
                    link: '/manager/stocks'
                },
                {
                    type: 'info',
                    icon: '👤',
                    title: 'Nouvelle Visite',
                    message: 'Nouvelle visite enregistrée par Ouane.',
                    time: 'Il y a 5h',
                    link: '/secure-portal'
                }
            ];
            
            this.weather = {
                location: 'Bamako',
                temperature: 35,
                condition: 'Temps sec',
                icon: '☀️'
            };
            
            this.init();
        }

        init() {
            this.createNotificationsWidget();
            this.createWeatherWidget();
            this.bindEvents();
        }

        createNotificationsWidget() {
            // Créer le widget de notifications
            const widget = document.createElement('div');
            widget.className = 'notifications-widget';
            widget.innerHTML = `
                <div class="notifications-bell" id="notificationsBell">
                    <div class="bell-icon">🔔</div>
                    <div class="notification-count" id="notificationCount">2</div>
                </div>
                <div class="notifications-dropdown" id="notificationsDropdown">
                    <div class="notifications-header">
                        <h3>Notifications</h3>
                        <button class="mark-all-read" id="markAllRead">Tout marquer comme lu</button>
                    </div>
                    <div class="notifications-list" id="notificationsList">
                        ${this.generateNotificationsHTML()}
                    </div>
                </div>
            `;

            // Ajouter les styles
            this.addNotificationsStyles();

            // Insérer dans le header - attendre que le DOM soit chargé
            setTimeout(() => {
                const topbarActions = document.querySelector('.topbar-actions');
                if (topbarActions) {
                    topbarActions.insertBefore(widget, topbarActions.firstChild);
                    console.log('Widget inséré dans topbar-actions');
                } else {
                    // Alternative : insérer après la navigation
                    const nav = document.querySelector('.nav');
                    if (nav && nav.parentElement) {
                        nav.parentElement.insertBefore(widget, nav.nextSibling);
                        console.log('Widget inséré après la navigation');
                    } else {
                        // Dernière alternative : insérer dans le header
                        const header = document.querySelector('header');
                        if (header) {
                            header.appendChild(widget);
                            console.log('Widget inséré dans le header');
                        }
                    }
                }
            }, 100);
        }

        createWeatherWidget() {
            // Créer le widget météo
            const weatherWidget = document.createElement('div');
            weatherWidget.className = 'weather-widget';
            weatherWidget.innerHTML = `
                <div class="weather-content">
                    <div class="weather-icon">${this.weather.icon}</div>
                    <div class="weather-info">
                        <div class="weather-location">${this.weather.location}</div>
                        <div class="weather-temp">${this.weather.temperature}°C</div>
                        <div class="weather-condition">${this.weather.condition}</div>
                    </div>
                </div>
            `;

            // Ajouter les styles météo
            this.addWeatherStyles();

            // Insérer dans le dashboard (après les KPIs) - attendre le chargement
            setTimeout(() => {
                const kpisGrid = document.querySelector('.grid.kpis');
                if (kpisGrid) {
                    kpisGrid.insertAdjacentElement('afterend', weatherWidget);
                }
            }, 200);
        }

        generateNotificationsHTML() {
            return this.notifications.map(notification => `
                <div class="notification-item ${notification.type}" onclick="window.notificationsWidget.handleNotificationClick('${notification.link}')">
                    <div class="notification-icon">${notification.icon}</div>
                    <div class="notification-content">
                        <div class="notification-title">${notification.title}</div>
                        <div class="notification-message">${notification.message}</div>
                        <div class="notification-time">${notification.time}</div>
                    </div>
                    <button class="notification-close" onclick="event.stopPropagation(); this.parentElement.remove()">×</button>
                </div>
            `).join('');
        }

        bindEvents() {
            const bell = document.getElementById('notificationsBell');
            const dropdown = document.getElementById('notificationsDropdown');
            const markAllRead = document.getElementById('markAllRead');

            // Toggle dropdown au clic sur la cloche
            if (bell) {
                bell.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dropdown.classList.toggle('show');
                    console.log('Dropdown toggled, show:', dropdown.classList.contains('show'));
                });
            }

            // Fermer le dropdown en cliquant dehors
            document.addEventListener('click', () => {
                dropdown.classList.remove('show');
            });

            // Empêcher la propagation dans le dropdown
            if (dropdown) {
                dropdown.addEventListener('click', (e) => {
                    e.stopPropagation();
                });
            }

            // Marquer tout comme lu
            if (markAllRead) {
                markAllRead.addEventListener('click', () => {
                    this.markAllAsRead();
                });
            }
        }

        handleNotificationClick(link) {
            if (link) {
                window.location.href = link;
            }
        }

        animateBell() {
            const bell = document.querySelector('.bell-icon');
            if (bell) {
                // Animation subtile de la cloche toutes les 4 secondes
                setInterval(() => {
                    bell.style.transform = 'rotate(15deg)';
                    setTimeout(() => {
                        bell.style.transform = 'rotate(-15deg)';
                        setTimeout(() => {
                            bell.style.transform = 'rotate(0deg)';
                        }, 150);
                    }, 150);
                }, 4000);
            }
        }

        markAllAsRead() {
            const count = document.getElementById('notificationCount');
            const notifications = document.querySelectorAll('.notification-item');
            
            // Masquer le compteur
            if (count) {
                count.style.display = 'none';
            }

            // Ajouter la classe "read" aux notifications
            notifications.forEach(notification => {
                notification.classList.add('read');
            });

            // Fermer le dropdown après 1 seconde
            setTimeout(() => {
                document.getElementById('notificationsDropdown').classList.remove('show');
            }, 1000);
        }

        addNotificationsStyles() {
            const style = document.createElement('style');
            style.textContent = `
                .notifications-widget {
                    position: relative;
                    margin-right: 16px;
                }

                .notifications-bell {
                    position: relative;
                    cursor: pointer;
                    padding: 8px;
                    border-radius: 50%;
                    background: rgba(16, 185, 129, 0.1);
                    transition: all 0.3s ease;
                    border: 2px solid transparent;
                }

                .notifications-bell:hover {
                    background: rgba(16, 185, 129, 0.2);
                    border-color: rgba(16, 185, 129, 0.3);
                    transform: scale(1.05);
                }

                .bell-icon {
                    font-size: 20px;
                    transition: transform 0.3s ease;
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
                    animation: pulse 2s infinite;
                }

                .notifications-dropdown {
                    position: absolute;
                    top: 100%;
                    right: 0;
                    width: 320px;
                    max-height: 400px;
                    background: white;
                    border-radius: 12px;
                    box-shadow: 0 8px 32px rgba(15, 23, 42, 0.15);
                    border: 1px solid rgba(15, 23, 42, 0.1);
                    z-index: 1000;
                    opacity: 0;
                    visibility: hidden;
                    transform: translateY(-10px);
                    transition: all 0.3s ease;
                    overflow: hidden;
                    pointer-events: none;
                }

                .notifications-dropdown.show {
                    opacity: 1;
                    visibility: visible;
                    transform: translateY(8px);
                    pointer-events: auto;
                }

                .notifications-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 16px 16px 12px;
                    border-bottom: 1px solid rgba(15, 23, 42, 0.1);
                }

                .notifications-header h3 {
                    margin: 0;
                    font-size: 16px;
                    font-weight: 600;
                    color: #374151;
                }

                .mark-all-read {
                    background: none;
                    border: none;
                    color: #10b981;
                    font-size: 12px;
                    cursor: pointer;
                    padding: 4px 8px;
                    border-radius: 4px;
                    transition: background 0.2s ease;
                }

                .mark-all-read:hover {
                    background: rgba(16, 185, 129, 0.1);
                }

                .notifications-list {
                    max-height: 300px;
                    overflow-y: auto;
                }

                .notification-item {
                    display: flex;
                    align-items: flex-start;
                    padding: 12px 16px;
                    border-bottom: 1px solid rgba(15, 23, 42, 0.05);
                    transition: background 0.2s ease;
                    position: relative;
                }

                .notification-item:hover {
                    background: rgba(16, 185, 129, 0.05);
                }

                .notification-item.read {
                    opacity: 0.6;
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
                    min-width: 0;
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

                .notification-close {
                    position: absolute;
                    top: 8px;
                    right: 8px;
                    background: none;
                    border: none;
                    color: #9ca3af;
                    cursor: pointer;
                    font-size: 16px;
                    padding: 2px;
                    border-radius: 50%;
                    width: 20px;
                    height: 20px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    opacity: 0;
                    transition: opacity 0.2s ease;
                }

                .notification-item:hover .notification-close {
                    opacity: 1;
                }

                .notification-close:hover {
                    background: rgba(239, 68, 68, 0.1);
                    color: #ef4444;
                }

                @keyframes pulse {
                    0%, 100% {
                        transform: scale(1);
                    }
                    50% {
                        transform: scale(1.1);
                    }
                }

                @media (max-width: 768px) {
                    .notifications-dropdown {
                        width: 280px;
                        right: -50px;
                    }
                    
                    .notifications-header {
                        padding: 12px;
                    }
                    
                    .notification-item {
                        padding: 10px 12px;
                    }
                }
            `;
            document.head.appendChild(style);
        }

        addWeatherStyles() {
            const style = document.createElement('style');
            style.textContent = `
                .weather-widget {
                    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
                    border-radius: 16px;
                    padding: 20px;
                    margin: 20px 0;
                    border: 1px solid #bbf7d0;
                    box-shadow: 0 4px 20px rgba(16, 185, 129, 0.1);
                }

                .weather-content {
                    display: flex;
                    align-items: center;
                    gap: 16px;
                }

                .weather-icon {
                    font-size: 48px;
                    animation: weatherFloat 3s ease-in-out infinite;
                }

                .weather-info {
                    flex: 1;
                }

                .weather-location {
                    font-size: 14px;
                    font-weight: 600;
                    color: #065f46;
                    margin-bottom: 4px;
                }

                .weather-temp {
                    font-size: 28px;
                    font-weight: 700;
                    color: #059669;
                    margin-bottom: 4px;
                }

                .weather-condition {
                    font-size: 13px;
                    color: #047857;
                    font-style: italic;
                }

                @keyframes weatherFloat {
                    0%, 100% {
                        transform: translateY(0px);
                    }
                    50% {
                        transform: translateY(-5px);
                    }
                }

                @media (max-width: 768px) {
                    .weather-widget {
                        margin: 16px 0;
                        padding: 16px;
                    }
                    
                    .weather-content {
                        gap: 12px;
                    }
                    
                    .weather-icon {
                        font-size: 36px;
                    }
                    
                    .weather-temp {
                        font-size: 24px;
                    }
                    
                    .weather-location {
                        font-size: 13px;
                    }
                    
                    .weather-condition {
                        font-size: 12px;
                    }
                }

                @media (max-width: 480px) {
                    .weather-widget {
                        padding: 12px;
                    }
                    
                    .weather-content {
                        flex-direction: column;
                        text-align: center;
                        gap: 8px;
                    }
                    
                    .weather-icon {
                        font-size: 32px;
                    }
                    
                    .weather-temp {
                        font-size: 20px;
                    }
                }
            `;
            document.head.appendChild(style);
        }
    }

    // Créer l'instance globale
    window.notificationsWidget = new NotificationsWidget();

})();
