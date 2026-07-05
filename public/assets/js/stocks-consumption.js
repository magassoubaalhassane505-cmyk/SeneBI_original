// Script simple pour gérer le formulaire de consommation
document.addEventListener("DOMContentLoaded", function() {
  const consumeForm = document.querySelector("#consumeForm");
  if (!consumeForm) return;

  consumeForm.addEventListener("submit", async function(e) {
    e.preventDefault();
    
    const region = document.querySelector("#consumeRegion")?.value || "";
    const parcelle = document.querySelector("#consumeParcel")?.value || "";
    const intrant = document.querySelector("#consumeItem")?.value || "";
    const quantite = parseFloat(document.querySelector("#consumeQty")?.value) || 0;
    const dateInput = document.querySelector("#consumeDate")?.value || "";
    const date = dateInput ? new Date(dateInput).toISOString().split('T')[0] : new Date().toISOString().split('T')[0];
    
    if (!parcelle || !intrant || quantite <= 0) {
      showToast("Veuillez remplir tous les champs", "error");
      return;
    }
    
    try {
      const csrfToken = window.SeneBI_SERVER?.csrf || '';
      
      const response = await fetch('/client/api/consommation', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
          region: region,
          parcelle: parcelle,
          date: date,
          intrant: intrant,
          quantite: quantite
        })
      });
      
      const contentType = response.headers.get('content-type');
      
      if (!contentType || !contentType.includes('application/json')) {
        const text = await response.text();
        throw new Error('Erreur serveur: réponse non JSON');
      }
      
      const result = await response.json();
      
      if (!response.ok) {
        throw new Error(result.error || 'Erreur lors de l\'enregistrement');
      }

      // Rafraîchir toutes les données depuis l'API
      await refreshAllData();

      showToast("Consommation enregistrée avec succès", "success");
      consumeForm.reset();
      
    } catch (error) {
      console.error("Erreur:", error);
      showToast("Erreur: " + (error.message || "Erreur lors de l'enregistrement"), "error");
    }
  });

  async function refreshAllData() {
    try {
      const response = await fetch('/client/api/stocks', {
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': window.SeneBI_SERVER?.csrf || ''
        }
      });
      
      const data = await response.json();
      const stocks = data.data || [];
      
      // Mettre à jour le tableau
      updateStockTable(stocks);
      
      // Mettre à jour les KPI
      updateKPICards(stocks);
      
      // Mettre à jour le graphique
      updateStocksChart(stocks);
      
      // Mettre à jour la jauge
      updateStockGauge(stocks);
      
      // Mettre à jour l'alerte
      updateAlertBanner(stocks);

      // Mettre à jour les dropdowns
      updateDropdowns(stocks);

      // Mettre à jour la timeline avec les mouvements
      const mouvementsResponse = await fetch('/client/api/stocks/mouvements', {
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': window.SeneBI_SERVER?.csrf || ''
        }
      });
      const mouvementsData = await mouvementsResponse.json();
      renderTimeline(mouvementsData.data || []);
      
    } catch (error) {
      console.error('Erreur refresh:', error);
    }
  }

  function updateStockTable(stocks) {
    const tbody = document.getElementById('stockTableBody');
    if (!tbody) return;

    tbody.innerHTML = stocks.map(s => {
      const isCritical = s.quantite_actuelle <= s.seuil_critique;
      const isLow = s.quantite_actuelle > s.seuil_critique && s.quantite_actuelle <= (s.seuil_critique * 2);
      const ratio = s.seuil_critique > 0 ? (s.quantite_actuelle / (s.seuil_critique * 4)) * 100 : 100;
      const progressPercent = Math.min(100, ratio);
      const statusClass = isCritical ? 'critical' : (isLow ? 'warning' : 'ok');
      const statusText = isCritical 
        ? 'Critique (' + Math.round((s.quantite_actuelle / s.seuil_critique) * 100) + '%)' 
        : (isLow 
            ? 'Faible (' + Math.round((s.quantite_actuelle / (s.seuil_critique * 4)) * 100) + '%)' 
            : 'Normal (' + Math.round((s.quantite_actuelle / (s.seuil_critique * 4)) * 100) + '%)');
      
      return `
        <tr>
          <td><strong>${s.nom}</strong></td>
          <td><span class="stock-type">${s.type}</span></td>
          <td>
            <div>${Number(s.quantite_actuelle).toLocaleString("fr-FR")} kg</div>
            <div class="stock-progress-bar">
              <div class="stock-progress-fill ${statusClass}" style="width: ${progressPercent}%;"></div>
            </div>
          </td>
          <td>${Number(s.seuil_critique).toLocaleString("fr-FR")} kg</td>
          <td>${Number(s.cout_unitaire || 0).toLocaleString("fr-FR")} FCFA</td>
          <td class="${isCritical ? 'status-bad' : (isLow ? 'status-warning' : 'status-ok')}">
            ${isCritical 
              ? `<span class="badge critical">Critique (${Math.round((s.quantite_actuelle / s.seuil_critique) * 100)}%)</span>` 
              : (isLow 
                  ? `<span class="badge warning">Faible (${Math.round((s.quantite_actuelle / (s.seuil_critique * 4)) * 100}%)</span>`
                  : `<span class="badge ok">Normal (${Math.round((s.quantite_actuelle / (s.seuil_critique * 4)) * 100}%)</span>`)}
          </td>
        </tr>
      `;
    }).join('');

    if (stocks.length === 0) {
      tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; color:#9ca3af; padding: 20px;">Aucun stock disponible.</td></tr>';
    }
  }

  function updateKPICards(stocks) {
    const kpiValues = document.querySelectorAll('.kpi-value');
    
    const totalIntrants = stocks.length;
    const totalValue = stocks.reduce((sum, s) => sum + Number(s.quantite_actuelle || 0) * Number(s.cout_unitaire || 0), 0);
    const criticalCount = stocks.filter(s => s.quantite_actuelle <= s.seuil_critique).length;
    
    if (kpiValues.length >= 1) {
      kpiValues[0].textContent = totalIntrants;
    }
    if (kpiValues.length >= 2) {
      kpiValues[1].innerHTML = `${Number(totalValue).toLocaleString("fr-FR")} <span class="muted" style="font-size:14px;font-weight:700;">FCFA</span>`;
    }
    if (kpiValues.length >= 3) {
      kpiValues[2].textContent = criticalCount;
      kpiValues[2].style.color = criticalCount > 0 ? '#ef4444' : '';
    }

    const cards = document.querySelectorAll('.kpi-card');
    cards.forEach(card => {
      card.classList.toggle('critical-alert', criticalCount > 0);
    });
  }

  function updateStocksChart(stocks) {
    const canvas = document.getElementById('stocksChart');
    if (!canvas || !window.Chart) return;

    const existing = Chart.getChart(canvas);
    if (existing) existing.destroy();

    if (stocks.length === 0) return;

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

  function updateStockGauge(stocks) {
    const canvas = document.getElementById('stockGaugeChart');
    const pctEl = document.getElementById('stockGaugePct');
    if (!canvas || !window.Chart || stocks.length === 0) {
      if (pctEl) pctEl.textContent = '0%';
      return;
    }

    const totalStock = stocks.reduce((sum, s) => sum + Number(s.quantite_actuelle || 0), 0);
    const pct = totalStock > 0 ? Math.min(100, Math.round((totalStock / 10000) * 100)) : 0;
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

  function updateAlertBanner(stocks) {
    const alertBanner = document.getElementById('stocksLocalAlert');
    const alertText = document.getElementById('stocksLocalAlertText');
    const criticalChip = document.getElementById('criticalChip');
    
    const criticalCount = stocks.filter(s => s.quantite_actuelle <= s.seuil_critique).length;

    if (criticalCount > 0) {
      alertBanner?.classList.add('show');
      alertText.textContent = `${criticalCount} intrant(s) en dessous du seuil critique. Réapprovisionnement urgent nécessaire.`;
      criticalChip.textContent = criticalCount.toString();
      criticalChip.style.background = '#ef4444';
      criticalChip.style.color = 'white';
    } else {
      alertBanner?.classList.remove('show');
      alertText.textContent = "Aucun intrant critique pour le moment.";
      criticalChip.textContent = "-";
    }
  }

function updateDropdowns(stocks) {
     // Dropdowns are now static - no dynamic population needed
   }

  function renderTimeline(mouvements) {
    const container = document.getElementById('stocksTimeline');
    if (!container) return;

    if (mouvements.length === 0) {
      container.innerHTML = '<div style="text-align:center;padding:20px;color:#64748b;">Aucun mouvement récent</div>';
      return;
    }

    container.innerHTML = mouvements.map(m => {
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
  
  function showToast(message, type) {
    const toast = document.createElement("div");
    toast.textContent = message;
    const bgColor = type === "success" ? "#10b981" : "#ef4444";
    toast.style.cssText = `
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: ${bgColor};
      color: white;
      padding: 12px 20px;
      border-radius: 12px;
      font-weight: 600;
      z-index: 1000;
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.3s ease;
    `;
    document.body.appendChild(toast);
    setTimeout(() => {
      toast.style.opacity = "1";
      toast.style.transform = "translateY(0)";
    }, 100);
    setTimeout(() => {
      toast.style.opacity = "0";
      toast.style.transform = "translateY(20px)";
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }
});