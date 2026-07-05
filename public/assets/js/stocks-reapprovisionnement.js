document.addEventListener("DOMContentLoaded", function() {
  const reapproForm = document.querySelector("#reapproForm");
  if (!reapproForm) return;

  const unitCostInput = document.querySelector("#reapproUnitCost");
  const totalCostInput = document.querySelector("#reapproTotalCost");
  const qtyInput = document.querySelector("#reapproQty");
  const reapproItem = document.querySelector("#reapproItem");

  function autoCalcTotalCost() {
    const qty = parseFloat(qtyInput?.value) || 0;
    const unit = parseFloat(unitCostInput?.value) || 0;
    if (qty > 0 && unit > 0 && totalCostInput && !totalCostInput.dataset.manuallySet) {
      totalCostInput.value = (qty * unit).toFixed(0);
    }
  }

  function autoCalcUnitCost() {
    const qty = parseFloat(qtyInput?.value) || 0;
    const total = parseFloat(totalCostInput?.value) || 0;
    if (qty > 0 && total > 0 && unitCostInput && !unitCostInput.dataset.manuallySet) {
      unitCostInput.value = (total / qty).toFixed(0);
    }
  }

  if (unitCostInput) {
    unitCostInput.addEventListener("input", function() {
      this.dataset.manuallySet = "true";
      autoCalcTotalCost();
      setTimeout(() => { this.dataset.manuallySet = ""; }, 500);
    });
  }

  if (totalCostInput) {
    totalCostInput.addEventListener("input", function() {
      this.dataset.manuallySet = "true";
      autoCalcUnitCost();
      setTimeout(() => { this.dataset.manuallySet = ""; }, 500);
    });
  }

  if (qtyInput) {
    qtyInput.addEventListener("input", function() {
      autoCalcTotalCost();
      autoCalcUnitCost();
    });
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
                  ? `<span class="badge warning">Faible (${Math.round((s.quantite_actuelle / (s.seuil_critique * 4)) * 100)}%)</span>`
                  : `<span class="badge ok">Normal (${Math.round((s.quantite_actuelle / (s.seuil_critique * 4)) * 100)}%)</span>`)}
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

  function refreshAllData() {
    fetch('/client/api/stocks', {
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': window.SeneBI_SERVER?.csrf || ''
      }
    })
    .then(r => r.json())
    .then(data => {
      const stocks = data.data || [];
      window.SeneBI_SERVER.stocks = stocks;
      
      updateStockTable(stocks);
      updateKPICards(stocks);
      updateStocksChart(stocks);
      updateStockGauge(stocks);
      updateAlertBanner(stocks);
      updateDropdowns(stocks);
    })
    .catch(err => console.error('Erreur refresh:', err));
  }

reapproForm.addEventListener("submit", async function(e) {
    e.preventDefault();

    const intrant = document.querySelector("#reapproItem")?.value || "";
    const quantite = parseFloat(document.querySelector("#reapproQty")?.value) || 0;
    const coutUnitaire = parseFloat(document.querySelector("#reapproUnitCost")?.value) || 0;
    const coutTotal = parseFloat(document.querySelector("#reapproTotalCost")?.value) || 0;
    const seuilCritique = parseFloat(document.querySelector("#reapproSeuil")?.value) || 100;
    const obs = document.querySelector("#reapproObs")?.value || "";
    const dateInput = document.querySelector("#reapproDate")?.value || "";
    const date = dateInput ? new Date(dateInput).toISOString().split('T')[0] : new Date().toISOString().split('T')[0];

    if (!intrant || quantite <= 0) {
      showToast("Veuillez sélectionner un intrant et une quantité valide.", "error");
      return;
    }

    const payload = {
      intrant: intrant,
      quantite: quantite,
      date: date,
      description: obs || "Entrée de stock",
      seuil_critique: seuilCritique,
    };

    if (coutUnitaire > 0) payload.cout_unitaire = coutUnitaire;
    else if (coutTotal > 0) payload.cout_total = coutTotal;

    try {
      const csrfToken = window.SeneBI_SERVER?.csrf || '';

      const response = await fetch('/client/api/stocks/entree', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(payload)
      });

      const contentType = response.headers.get('content-type');
      if (!contentType || !contentType.includes('application/json')) {
        const text = await response.text();
        throw new Error('Erreur serveur: réponse non JSON');
      }

      const result = await response.json();
      if (!response.ok) {
        throw new Error(result.error || "Erreur lors de l'enregistrement");
      }

      refreshAllData();

      const timeline = document.getElementById('stocksTimeline');
      if (timeline && result.stock) {
        const intrantNom = result.stock.nom;
        const newItem = document.createElement('div');
        newItem.className = 'timeline-item';
        newItem.style.cssText = 'display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; background: #dcfce7; margin-bottom: 8px;';
        newItem.innerHTML = `
          <div style="width: 32px; height: 32px; border-radius: 50%; background: #059669; color: white; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-arrow-up" style="font-size: 14px;"></i>
          </div>
          <div style="flex: 1;">
            <div style="font-weight: 600; color: #111827;">${intrantNom}</div>
            <div style="font-size: 12px; color: #64748b;">${new Date(date).toLocaleDateString('fr-FR')}${obs ? ' — ' + obs : ''}</div>
          </div>
          <div style="font-weight: 700; color: #059669;">+${quantite.toLocaleString('fr-FR')} kg</div>
        `;
        timeline.insertBefore(newItem, timeline.firstChild);
      }

      showToast("Réapprovisionnement enregistré avec succès", "success");
      reapproForm.reset();
      const dateEl = document.querySelector("#reapproDate");
      if (dateEl) dateEl.valueAsDate = new Date();

    } catch (error) {
      console.error("Erreur:", error);
      showToast("Erreur: " + (error.message || "Erreur lors de l'enregistrement"), "error");
    }
  });

  function showToast(message, type) {
    const toast = document.createElement("div");
    toast.textContent = message;
    const bgColor = type === "success" ? "#059669" : "#ef4444";
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
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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