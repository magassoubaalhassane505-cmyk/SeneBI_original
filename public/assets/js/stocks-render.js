(function () {
    'use strict';
    
    const cfg = window.SeneBI_SERVER || {};
    
    function fmtKg(n) {
        return `${Number(n || 0).toLocaleString("fr-FR")} kg`;
    }
    
    function renderStockGauge() {
        const canvas = document.getElementById('stockGaugeChart');
        const pctEl = document.getElementById('stockGaugePct');
        if (!canvas || !window.Chart) return;
        
        const stocks = cfg.stocks || [];
        const totalStock = stocks.reduce((sum, s) => sum + Number(s.quantite_actuelle || 0), 0);
        const totalCapacity = 10000;
        const pct = totalStock > 0 ? Math.min(100, Math.round((totalStock / totalCapacity) * 100)) : 0;
        const rest = Math.max(0, 100 - pct);
        
        if (pctEl) pctEl.textContent = `${pct}%`;
        
        const existing = Chart.getChart(canvas);
        if (existing) existing.destroy();
        
        new Chart(canvas, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [pct, rest],
                    backgroundColor: ['#059669', '#e2e8f0'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                }
            }
        });
    }
    
    function renderStocksChart() {
        const canvas = document.getElementById('stocksChart');
        if (!canvas || !window.Chart) return;

        const stocks = cfg.stocks || [];
        
        if (stocks.length === 0) return;
        
        const existing = Chart.getChart(canvas);
        if (existing) existing.destroy();
        
        const labels = stocks.map(s => s.nom);
        const stockData = stocks.map(s => Number(s.quantite_actuelle || 0));
        const thresholdData = stocks.map(s => Number(s.seuil_critique || 0));
        
        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Stock Actuel',
                    data: stockData,
                    backgroundColor: '#10b981',
                    borderRadius: 10,
                }, {
                    label: 'Seuil Critique',
                    data: thresholdData,
                    backgroundColor: '#ef4444',
                    borderRadius: 10,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
    
    function renderMonthlyConsumptionChart() {
        const canvas = document.getElementById('monthlyConsumptionChart');
        if (!canvas || !window.Chart || !cfg.monthlyConsumption) return;
        
        const existing = Chart.getChart(canvas);
        if (existing) existing.destroy();
        
        const labels = cfg.monthlyConsumption.map(m => m.nom);
        const data = cfg.monthlyConsumption.map(m => Number(m.volume || 0));
        
        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Consommation (kg)',
                    data: data,
                    backgroundColor: '#3b82f6',
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(15,23,42,0.06)' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
    
    function renderTimeline() {
        const container = document.getElementById('stocksTimeline');
        if (!container || !cfg.mouvements) return;
        
        container.innerHTML = cfg.mouvements.map(m => {
            const date = new Date(m.date_mouvement);
            const dateStr = date.toLocaleDateString('fr-FR', { 
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            let icon = 'fa-exchange-alt';
            let color = '#64748b';
            let bgColor = '#f1f5f9';
            
            if (m.type === 'entree') {
                icon = 'fa-arrow-up';
                color = '#059669';
                bgColor = '#dcfce7';
            } else if (m.type === 'utilisation') {
                icon = 'fa-arrow-down';
                color = '#ef4444';
                bgColor = '#fee2e2';
            }
            
            return `
                <div class="timeline-item" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; background: ${bgColor}; margin-bottom: 8px; transition: all 0.2s ease;">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: ${color}; color: white; display: flex; align-items: center; justify-content: center;">
                        <i class="fas ${icon}" style="font-size: 14px;"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #111827;">${m.stock?.nom || 'Inconnu'}</div>
                        <div style="font-size: 12px; color: #64748b;">${dateStr}</div>
                    </div>
                    <div style="font-weight: 700; color: ${color};">
                        ${m.type === 'entree' ? '+' : '-'}${Number(m.quantite).toLocaleString('fr-FR')} kg
                    </div>
                </div>
            `;
        }).join('');
    }
    
    function renderAlertBanner() {
        const alertBanner = document.getElementById('stocksLocalAlert');
        const criticalChip = document.getElementById('criticalChip');
        
        const criticalCount = document.querySelectorAll('.status-bad').length;
        
        if (criticalCount > 0) {
            alertBanner?.classList.add('show');
            criticalChip.textContent = criticalCount.toString();
            criticalChip.style.background = '#ef4444';
            criticalChip.style.color = 'white';
        }
    }
    
    document.addEventListener('DOMContentLoaded', function () {
        if (!cfg.useDb) return;
        
        setTimeout(function () {
            renderStockGauge();
            renderStocksChart();
            renderMonthlyConsumptionChart();
            renderTimeline();
            renderAlertBanner();
        }, 100);
    });
})();