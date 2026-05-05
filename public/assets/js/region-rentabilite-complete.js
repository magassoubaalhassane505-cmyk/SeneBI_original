// SeneBI - Filtrage par région complet pour la page Rentabilité
(function() {
    'use strict';
    
    class RegionRentabiliteComplete {
        constructor() {
            this.currentRegion = 'all';
            this.regionData = this.initCompleteRegionData();
            this.originalTableData = [];
            this.init();
        }

        init() {
            this.preserveOriginalTableData();
            this.bindEvents();
            this.updateRentabilitePage();
        }

        initCompleteRegionData() {
            // Données complètes par région du Mali avec calculs précis
            return {
                all: {
                    name: 'Toutes les régions',
                    salesFcfa: 14200000,
                    intrantsCostFcfa: 6100000,
                    exportsFcfa: 3200000,
                    margeFcfa: 8100000,
                    rentabilite: 57.0,
                    coutProduction: 4300,
                    prixVenteMoyen: 5800,
                    beneficeParHa: 431000,
                    tableRows: [
                        { date: '15/03/2026', parcelle: 'Parcelle Bamako Nord-1', quantite: 4200, prix: 3450, revenu: 14490000 },
                        { date: '18/03/2026', parcelle: 'Parcelle Kayes Nord-1', quantite: 2800, prix: 3520, revenu: 9856000 },
                        { date: '22/03/2026', parcelle: 'Parcelle Koulikoro Nord-1', quantite: 3500, prix: 3480, revenu: 12180000 },
                        { date: '25/03/2026', parcelle: 'Parcelle Ségou Nord-1', quantite: 2900, prix: 3550, revenu: 10295000 },
                        { date: '28/03/2026', parcelle: 'Parcelle Sikasso Nord-1', quantite: 3800, prix: 3420, revenu: 12996000 },
                        { date: '01/04/2026', parcelle: 'Parcelle Mopti Nord-1', quantite: 2200, prix: 3490, revenu: 7678000 },
                        { date: '04/04/2026', parcelle: 'Parcelle Tombouctou Nord-1', quantite: 1800, prix: 3560, revenu: 6408000 },
                        { date: '07/04/2026', parcelle: 'Parcelle Gao Nord-1', quantite: 1500, prix: 3620, revenu: 5430000 },
                        { date: '10/04/2026', parcelle: 'Parcelle Kidal Nord-1', quantite: 1200, prix: 3680, revenu: 4416000 }
                    ]
                },
                bko: {
                    name: 'Bamako',
                    salesFcfa: 2430000,
                    intrantsCostFcfa: 1050000,
                    exportsFcfa: 550000,
                    margeFcfa: 1380000,
                    rentabilite: 56.8,
                    coutProduction: 4250,
                    prixVenteMoyen: 3450,
                    beneficeParHa: 460000,
                    tableRows: [
                        { date: '15/03/2026', parcelle: 'Parcelle Bamako Nord-1', quantite: 4200, prix: 3450, revenu: 14490000 },
                        { date: '20/03/2026', parcelle: 'Parcelle Bamako Sud-1', quantite: 2800, prix: 3500, revenu: 9800000 },
                        { date: '25/03/2026', parcelle: 'Parcelle Bamako Centre-1', quantite: 2100, prix: 3400, revenu: 7140000 }
                    ]
                },
                kay: {
                    name: 'Kayes',
                    salesFcfa: 2180000,
                    intrantsCostFcfa: 950000,
                    exportsFcfa: 480000,
                    margeFcfa: 1230000,
                    rentabilite: 56.4,
                    coutProduction: 4390,
                    prixVenteMoyen: 3520,
                    beneficeParHa: 441000,
                    tableRows: [
                        { date: '18/03/2026', parcelle: 'Parcelle Kayes Nord-1', quantite: 2800, prix: 3520, revenu: 9856000 },
                        { date: '23/03/2026', parcelle: 'Parcelle Kayes Est-1', quantite: 2400, prix: 3550, revenu: 8520000 },
                        { date: '28/03/2026', parcelle: 'Parcelle Kayes Sud-1', quantite: 1800, prix: 3480, revenu: 6264000 }
                    ]
                },
                kou: {
                    name: 'Koulikoro',
                    salesFcfa: 2650000,
                    intrantsCostFcfa: 1120000,
                    exportsFcfa: 590000,
                    margeFcfa: 1530000,
                    rentabilite: 57.7,
                    coutProduction: 4180,
                    prixVenteMoyen: 3480,
                    beneficeParHa: 493000,
                    tableRows: [
                        { date: '22/03/2026', parcelle: 'Parcelle Koulikoro Nord-1', quantite: 3500, prix: 3480, revenu: 12180000 },
                        { date: '27/03/2026', parcelle: 'Parcelle Koulikoro Centre-1', quantite: 2900, prix: 3500, revenu: 10150000 },
                        { date: '01/04/2026', parcelle: 'Parcelle Koulikoro Sud-1', quantite: 2200, prix: 3450, revenu: 7590000 }
                    ]
                },
                seg: {
                    name: 'Ségou',
                    salesFcfa: 2340000,
                    intrantsCostFcfa: 1020000,
                    exportsFcfa: 520000,
                    margeFcfa: 1320000,
                    rentabilite: 56.4,
                    coutProduction: 4430,
                    prixVenteMoyen: 3550,
                    beneficeParHa: 462000,
                    tableRows: [
                        { date: '25/03/2026', parcelle: 'Parcelle Ségou Nord-1', quantite: 2900, prix: 3550, revenu: 10295000 },
                        { date: '30/03/2026', parcelle: 'Parcelle Ségou Est-1', quantite: 2500, prix: 3600, revenu: 9000000 },
                        { date: '04/04/2026', parcelle: 'Parcelle Ségou Sud-1', quantite: 1900, prix: 3500, revenu: 6650000 }
                    ]
                },
                sik: {
                    name: 'Sikasso',
                    salesFcfa: 2890000,
                    intrantsCostFcfa: 1250000,
                    exportsFcfa: 640000,
                    margeFcfa: 1640000,
                    rentabilite: 56.7,
                    coutProduction: 4320,
                    prixVenteMoyen: 3420,
                    beneficeParHa: 547000,
                    tableRows: [
                        { date: '28/03/2026', parcelle: 'Parcelle Sikasso Nord-1', quantite: 3800, prix: 3420, revenu: 12996000 },
                        { date: '02/04/2026', parcelle: 'Parcelle Sikasso Centre-1', quantite: 3200, prix: 3400, revenu: 10880000 },
                        { date: '06/04/2026', parcelle: 'Parcelle Sikasso Sud-1', quantite: 2600, prix: 3450, revenu: 8970000 }
                    ]
                },
                mop: {
                    name: 'Mopti',
                    salesFcfa: 1760000,
                    intrantsCostFcfa: 780000,
                    exportsFcfa: 390000,
                    margeFcfa: 980000,
                    rentabilite: 55.7,
                    coutProduction: 4440,
                    prixVenteMoyen: 3490,
                    beneficeParHa: 327000,
                    tableRows: [
                        { date: '01/04/2026', parcelle: 'Parcelle Mopti Nord-1', quantite: 2200, prix: 3490, revenu: 7678000 },
                        { date: '05/04/2026', parcelle: 'Parcelle Mopti Sud-1', quantite: 1800, prix: 3520, revenu: 6336000 },
                        { date: '09/04/2026', parcelle: 'Parcelle Mopti Est-1', quantite: 1500, prix: 3450, revenu: 5175000 }
                    ]
                },
                tom: {
                    name: 'Tombouctou',
                    salesFcfa: 1280000,
                    intrantsCostFcfa: 580000,
                    exportsFcfa: 280000,
                    margeFcfa: 700000,
                    rentabilite: 54.7,
                    coutProduction: 5450,
                    prixVenteMoyen: 3560,
                    beneficeParHa: 233000,
                    tableRows: [
                        { date: '04/04/2026', parcelle: 'Parcelle Tombouctou Nord-1', quantite: 1800, prix: 3560, revenu: 6408000 },
                        { date: '08/04/2026', parcelle: 'Parcelle Tombouctou Est-1', quantite: 1400, prix: 3600, revenu: 5040000 },
                        { date: '12/04/2026', parcelle: 'Parcelle Tombouctou Sud-1', quantite: 1100, prix: 3500, revenu: 3850000 }
                    ]
                },
                gao: {
                    name: 'Gao',
                    salesFcfa: 1120000,
                    intrantsCostFcfa: 520000,
                    exportsFcfa: 240000,
                    margeFcfa: 600000,
                    rentabilite: 53.6,
                    coutProduction: 5750,
                    prixVenteMoyen: 3620,
                    beneficeParHa: 188000,
                    tableRows: [
                        { date: '07/04/2026', parcelle: 'Parcelle Gao Nord-1', quantite: 1500, prix: 3620, revenu: 5430000 },
                        { date: '11/04/2026', parcelle: 'Parcelle Gao Est-1', quantite: 1200, prix: 3650, revenu: 4380000 },
                        { date: '15/04/2026', parcelle: 'Parcelle Gao Sud-1', quantite: 1000, prix: 3580, revenu: 3580000 }
                    ]
                },
                kid: {
                    name: 'Kidal',
                    salesFcfa: 980000,
                    intrantsCostFcfa: 460000,
                    exportsFcfa: 210000,
                    margeFcfa: 520000,
                    rentabilite: 53.1,
                    coutProduction: 5900,
                    prixVenteMoyen: 3680,
                    beneficeParHa: 173000,
                    tableRows: [
                        { date: '10/04/2026', parcelle: 'Parcelle Kidal Nord-1', quantite: 1200, prix: 3680, revenu: 4416000 },
                        { date: '14/04/2026', parcelle: 'Parcelle Kidal Sud-1', quantite: 900, prix: 3700, revenu: 3330000 },
                        { date: '18/04/2026', parcelle: 'Parcelle Kidal Est-1', quantite: 800, prix: 3650, revenu: 2920000 }
                    ]
                }
            };
        }

        preserveOriginalTableData() {
            // Sauvegarder les données originales du tableau
            const tableBody = document.getElementById('harvestRows');
            if (tableBody) {
                const rows = tableBody.querySelectorAll('tr');
                this.originalTableData = Array.from(rows).map(row => ({
                    element: row,
                    html: row.outerHTML,
                    visible: true
                }));
            }
        }

        bindEvents() {
            const regionSelect = document.getElementById('regionSelectRent');
            if (regionSelect) {
                // Remplacer l'écouteur existant
                regionSelect.removeEventListener('change', this.handleRegionChange);
                this.handleRegionChange = (e) => {
                    this.currentRegion = e.target.value;
                    this.updateRentabilitePage();
                    this.showRegionNotification();
                };
                regionSelect.addEventListener('change', this.handleRegionChange);
            }
        }

        updateRentabilitePage() {
            const data = this.regionData[this.currentRegion];
            if (!data) return;

            // 1. Mettre à jour les KPI principaux (Revenus Totaux et Bénéfice Net)
            this.updateMainKPIs(data);
            
            // 2. Mettre à jour le tableau des récoltes
            this.updateHarvestTable(data);
            
            // 3. Mettre à jour les graphiques en temps réel
            this.updateChartsInRealTime(data);
            
            // 4. Préserver les éléments interactifs
            this.preserveInteractiveElements();
        }

        updateMainKPIs(data) {
            // Mettre à jour "Revenus Totaux" (Chiffre d'Affaires)
            const salesKpi = document.getElementById('salesKpi');
            if (salesKpi) {
                const currentSales = parseFloat(salesKpi.querySelector('.number')?.textContent.replace(/[^\d.-]/g, '') || '0');
                this.animateKPIValue(salesKpi, currentSales, data.salesFcfa, '#10b981');
            }

            // Mettre à jour "Coûts Intrants"
            const costKpi = document.getElementById('costKpi');
            if (costKpi) {
                const currentCost = parseFloat(costKpi.querySelector('.number')?.textContent.replace(/[^\d.-]/g, '') || '0');
                this.animateKPIValue(costKpi, currentCost, data.intrantsCostFcfa, '#ef4444');
            }

            // Mettre à jour "Bénéfice Net"
            const profitKpi = document.getElementById('profitKpi');
            if (profitKpi) {
                const profit = data.salesFcfa - data.intrantsCostFcfa;
                const currentProfit = parseFloat(profitKpi.querySelector('.number')?.textContent.replace(/[^\d.-]/g, '') || '0');
                const profitColor = profit >= 0 ? '#10b981' : '#ef4444';
                this.animateKPIValue(profitKpi, currentProfit, profit, profitColor);
            }

            // Mettre à jour "Marge Nette"
            const marginKpi = document.getElementById('marginKpi');
            if (marginKpi) {
                const margin = data.salesFcfa > 0 ? ((data.salesFcfa - data.intrantsCostFcfa) / data.salesFcfa) * 100 : 0;
                const marginColor = margin >= 0 ? '#10b981' : '#ef4444';
                marginKpi.innerHTML = `
                    <span class="number" style="color: ${marginColor}; font-size: 2rem; font-weight: 800; letter-spacing: -0.5px;">${margin.toFixed(1)}%</span>
                `;
                
                // Mettre à jour l'explication
                const explanationDiv = marginKpi.nextElementSibling;
                if (explanationDiv && explanationDiv.classList.contains('explanation')) {
                    const explanation = margin >= 0 
                        ? `Vous gagnez ${Math.abs(margin * 10).toLocaleString('fr-FR')} FCFA pour chaque 1 000 FCFA de chiffre d'affaires.`
                        : `Vous perdez ${Math.abs(margin * 10).toLocaleString('fr-FR')} FCFA pour chaque 1 000 FCFA gagnés.`;
                    explanationDiv.textContent = explanation;
                    explanationDiv.style.color = margin >= 0 ? '#10b981' : '#ef4444';
                }
            }
        }

        animateKPIValue(element, start, end, color) {
            const duration = 800;
            const startTime = performance.now();
            
            const updateValue = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const currentValue = start + (end - start) * this.easeOutQuad(progress);
                
                element.innerHTML = `
                    <span class="number" style="color: ${color}; font-size: 2rem; font-weight: 800; letter-spacing: -0.5px;">${Math.abs(currentValue).toLocaleString('fr-FR')}</span>
                    <span class="unit">FCFA</span>
                `;
                
                if (progress < 1) {
                    requestAnimationFrame(updateValue);
                }
            };
            
            requestAnimationFrame(updateValue);
        }

        updateHarvestTable(data) {
            const tableBody = document.getElementById('harvestRows');
            if (!tableBody || !data.tableRows) return;

            // Vider le tableau actuel
            tableBody.innerHTML = '';

            // Ajouter les lignes filtrées avec tous les éléments interactifs préservés
            data.tableRows.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.date}</td>
                    <td>${row.parcelle}</td>
                    <td>${row.quantite.toLocaleString('fr-FR')} kg</td>
                    <td>${row.prix.toLocaleString('fr-FR')} FCFA</td>
                    <td style="color: #10b981; font-weight: 600;">${row.revenu.toLocaleString('fr-FR')} FCFA</td>
                `;
                tableBody.appendChild(tr);
            });

            // S'assurer que les lignes sont cliquables et interactives
            this.makeTableRowsInteractive(tableBody);
        }

        makeTableRowsInteractive(tableBody) {
            const rows = tableBody.querySelectorAll('tr');
            rows.forEach(row => {
                // Ajouter des événements hover pour l'interactivité
                row.style.cursor = 'pointer';
                row.addEventListener('mouseenter', () => {
                    row.style.backgroundColor = 'rgba(16, 185, 129, 0.05)';
                });
                row.addEventListener('mouseleave', () => {
                    row.style.backgroundColor = '';
                });
                
                // Ajouter un événement click pour voir les détails
                row.addEventListener('click', () => {
                    const parcelle = row.cells[1].textContent;
                    this.showParcelDetails(parcelle);
                });
            });
        }

        showParcelDetails(parcelle) {
            // Notification avec détails de la parcelle
            const notification = document.createElement('div');
            notification.className = 'parcel-details-notification';
            notification.innerHTML = `
                <div class="notification-content">
                    <span class="notification-icon">📊</span>
                    <div>
                        <strong>Détails de ${parcelle}</strong>
                        <p>Cliquez pour voir l'analyse complète</p>
                    </div>
                    <span class="notification-close">×</span>
                </div>
            `;
            
            notification.style.cssText = `
                position: fixed;
                top: 80px;
                right: 20px;
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                padding: 16px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                z-index: 1002;
                opacity: 0;
                transform: translateY(-20px);
                transition: all 0.3s ease;
                max-width: 300px;
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
            }, 4000);
        }

        updateChartsInRealTime(data) {
            // Mettre à jour le graphique de comparaison revenus vs coûts
            this.updateProfitChart(data);
            
            // Mettre à jour le graphique de performance par culture
            this.updateCultureChart(data);
        }

        updateProfitChart(data) {
            const profitChart = window.myChart;
            if (profitChart && data) {
                const sales = data.salesFcfa;
                const costs = data.intrantsCostFcfa;
                const profit = sales - costs;
                
                profitChart.data.datasets[0].data = [sales, costs, profit];
                profitChart.data.datasets[0].backgroundColor = ['#10b981', '#6b7280', '#8b5cf6'];
                profitChart.update('active'); // Animation active
            }
        }

        updateCultureChart(data) {
            const cultureChart = window.cultureChart;
            if (cultureChart && data && data.cultures) {
                // Simuler des données de cultures basées sur la région
                const cultureData = this.generateCultureData(data);
                cultureChart.data.datasets[0].data = cultureData;
                cultureChart.update('active'); // Animation active
            }
        }

        generateCultureData(data) {
            // Générer des données réalistes par culture selon la région
            const baseData = {
                'Riz': Math.random() * 20 + 15,
                'Maïs': Math.random() * 15 + 10,
                'Coton': Math.random() * 10 + 8
            };
            
            return Object.values(baseData);
        }

        preserveInteractiveElements() {
            // S'assurer que tous les boutons et liens restent fonctionnels
            const buttons = document.querySelectorAll('button');
            buttons.forEach(button => {
                if (!button.hasAttribute('data-preserved')) {
                    button.setAttribute('data-preserved', 'true');
                    button.addEventListener('click', (e) => {
                        // Éviter que le filtrage n'interfère avec les clics
                        e.stopPropagation();
                    });
                }
            });

            // S'assurer que les liens restent cliquables
            const links = document.querySelectorAll('a');
            links.forEach(link => {
                if (!link.hasAttribute('data-preserved')) {
                    link.setAttribute('data-preserved', 'true');
                    link.addEventListener('click', (e) => {
                        e.stopPropagation();
                    });
                }
            });
        }

        showRegionNotification() {
            const data = this.regionData[this.currentRegion];
            if (!data || this.currentRegion === 'all') return;

            const notification = document.createElement('div');
            notification.className = 'region-notification-rentabilite';
            notification.innerHTML = `
                <div class="notification-content">
                    <span class="notification-icon">📍</span>
                    <div>
                        <strong>Filtrage appliqué : ${data.name}</strong>
                        <p>Revenus: ${data.salesFcfa.toLocaleString('fr-FR')} FCFA • Bénéfice: ${(data.salesFcfa - data.intrantsCostFcfa).toLocaleString('fr-FR')} FCFA</p>
                    </div>
                    <span class="notification-close">×</span>
                </div>
            `;
            
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                color: white;
                padding: 16px 20px;
                border-radius: 12px;
                font-size: 14px;
                font-weight: 500;
                z-index: 1001;
                opacity: 0;
                transform: translateY(-20px);
                transition: all 0.3s ease;
                box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
                max-width: 350px;
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
            }, 4000);
        }

        easeOutQuad(t) {
            return t * (2 - t);
        }

        // Méthode pour réinitialiser à "Toutes les régions"
        resetToAll() {
            this.currentRegion = 'all';
            const regionSelect = document.getElementById('regionSelectRent');
            if (regionSelect) {
                regionSelect.value = 'all';
            }
            this.updateRentabilitePage();
        }
    }

    // Créer l'instance globale
    window.regionRentabiliteComplete = new RegionRentabiliteComplete();

    // Surveiller les changements DOM pour préserver les éléments interactifs
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                setTimeout(() => {
                    window.regionRentabiliteComplete.preserveInteractiveElements();
                }, 200);
            }
        });
    });

    // Observer le conteneur principal
    const mainContainer = document.querySelector('.container');
    if (mainContainer) {
        observer.observe(mainContainer, {
            childList: true,
            subtree: true
        });
    }

})();
