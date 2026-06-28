// Supervision page JavaScript
(function() {
  document.addEventListener("DOMContentLoaded", function() {
    // Check if user is manager
    const auth = SeneBI.requireRole(["manager"], "Accès réservé aux managers");
    if (!auth) return;

    // Initialize supervision data
    initializeSupervision();
  });

  function initializeSupervision() {
    // Render the topbar navigation
    SeneBI.renderTopbar();
    
    // Simulate real-time data updates
    updateKPIs();
    updateFarmersDirectory();
    updateSystemStatus();
    
    // Set up periodic updates
    setInterval(updateKPIs, 5000); // Update every 5 seconds
    setInterval(updateFarmersDirectory, 15000); // Update every 15 seconds
    setInterval(updateSystemStatus, 30000); // Update every 30 seconds
  }

  function updateKPIs() {
    fetch('/manager/api/supervision-stats', {
      headers: { 'Accept': 'application/json' }
    })
      .then(res => res.ok ? res.json() : null)
      .then(data => {
        if (!data) return;
        document.getElementById('activeUsers').textContent = data.activeUsers ?? 0;
        document.getElementById('dailyActivities').textContent = data.dailyActivities ?? 0;
        document.getElementById('systemAlerts').textContent = data.systemAlerts ?? 0;
        document.getElementById('performanceScore').textContent = data.performanceScore ?? 0;
      })
      .catch(() => {});
  }

  function updateFarmersDirectory() {
    // Utiliser les clients réels depuis la base de données
    const realClients = window.SeneBI?.activeClients || [];

    const farmers = realClients.map(client => ({
      id: client.id,
      name: client.name,
      location: client.location,
      stockStatus: "ok",
      stockLevel: "Actif",
      riskLevel: "Faible",
      riskClass: "risk-low",
      lastActivity: "Actif"
    }));

    const container = document.getElementById('farmersTableBody');
    if (container) {
      if (farmers.length === 0) {
        container.innerHTML = `
          <tr>
            <td colspan="6" style="text-align: center; padding: 48px 24px; color: #6b7280;">
              <p style="margin: 0; font-size: 15px; font-weight: 500;">Aucun agriculteur actif</p>
              <p style="margin: 8px 0 0 0; font-size: 13px; color: #9ca3af;">Les agriculteurs approuvés apparaîtront ici</p>
            </td>
          </tr>
        `;
      } else {
        container.innerHTML = farmers.map(farmer => `
          <tr>
            <td>
              <div class="farmer-name">${farmer.name}</div>
            </td>
            <td>
              <div class="farmer-location">${farmer.location}</div>
            </td>
            <td>
              <span class="stock-badge ${farmer.stockStatus}">${farmer.stockLevel}</span>
            </td>
            <td>
              <span class="risk-badge ${farmer.riskClass}">${farmer.riskLevel}</span>
            </td>
            <td>
              <div class="last-activity">${farmer.lastActivity}</div>
            </td>
            <td>
              <button class="details-btn" onclick="showFarmerDetails('${farmer.id}', '${farmer.name}')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
                Détails
              </button>
            </td>
          </tr>
        `).join('');
      }
    }
  }

  // Function to handle farmer details click
  window.showFarmerDetails = async function(farmerId) {
    const modal = document.getElementById('farmerModal');
    modal.hidden = false;
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    document.body.style.overflow = 'hidden';

    try {
      const res = await fetch(`/manager/api/farmers/${farmerId}`);
      if (!res.ok) return;
      const data = await res.json();

      document.getElementById('modalFarmerName').textContent = data.name;
      document.getElementById('modalFarmerLocation').innerHTML = `<i class="fas fa-map-marker-alt" style="margin-right: 4px;"></i>${data.location}`;
      
      const initials = data.name.split(' ').map(n => n.charAt(0)).join('').substring(0, 2).toUpperCase();
      const avatarEl = document.getElementById('modalAvatar');
      if (avatarEl) avatarEl.textContent = initials || '?';

      const statusBadge = document.getElementById('modalStatusBadge');
      if (statusBadge) {
        const hasCritical = data.alertes && data.alertes.some(a => a.includes('Stock critique') || a.includes('Perte'));
        if (hasCritical) {
          statusBadge.className = 'status-badge danger';
          statusBadge.innerHTML = '<i class="fas fa-exclamation-circle"></i> Attention';
        } else {
          statusBadge.className = 'status-badge success';
          statusBadge.innerHTML = '<i class="fas fa-check-circle"></i> Actif';
        }
      }

      document.getElementById('modalRendement').textContent = (data.rendement_moyen || 0).toLocaleString('fr-FR') + ' t/ha';
      document.getElementById('modalProduction').textContent = Number(data.production_totale || 0).toLocaleString('fr-FR') + ' kg';
      document.getElementById('modalCA').textContent = Number(data.chiffre_affaires || 0).toLocaleString('fr-FR') + ' FCFA';
      const beneficeEl = document.getElementById('modalBenefice');
      beneficeEl.textContent = (data.benefice_net >= 0 ? '+' : '') + Number(data.benefice_net || 0).toLocaleString('fr-FR') + ' FCFA';
      beneficeEl.style.color = data.benefice_net >= 0 ? '#14532d' : '#991b1b';

      const analysisEl = document.getElementById('modalAnalysisText');
      if (analysisEl) analysisEl.textContent = generateAnalysis(data);

      const alertesSection = document.getElementById('modalAlertesSection');
      const alertesList = document.getElementById('modalAlertesList');
      if (data.alertes && data.alertes.length > 0) {
        alertesSection.style.display = 'block';
        alertesList.innerHTML = data.alertes.map(a => `
          <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 10px 12px; font-size: 13px; color: #991b1b; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-exclamation-circle"></i> ${a}
          </div>
        `).join('');
      } else {
        alertesSection.style.display = 'none';
      }

      const stocksList = document.getElementById('modalStocksList');
      if (data.stocks && data.stocks.length > 0) {
        stocksList.innerHTML = data.stocks.map(s => `
          <div style="display: flex; align-items: center; justify-content: space-between; background: ${s.est_critique ? '#fef2f2' : '#f9fafb'}; padding: 10px 12px; border-radius: 8px; border: 1px solid ${s.est_critique ? '#fecaca' : '#e5e7eb'};">
            <div>
              <div style="font-size: 13px; font-weight: 600; color: #111827;">${s.nom}</div>
              <div style="font-size: 11px; color: #6b7280;">Seuil: ${Number(s.seuil).toLocaleString('fr-FR')} kg</div>
            </div>
            <div style="text-align: right;">
              <div style="font-size: 14px; font-weight: 700; color: ${s.est_critique ? '#991b1b' : '#14532d'};">${Number(s.quantite).toLocaleString('fr-FR')} kg</div>
              <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 2px 6px; border-radius: 999px; background: ${s.est_critique ? '#fef2f2' : '#dcfce7'}; color: ${s.est_critique ? '#991b1b' : '#166534'};">${s.est_critique ? 'Critique' : 'OK'}</span>
            </div>
          </div>
        `).join('');
      } else {
        stocksList.innerHTML = '<span style="color: #9ca3af; font-size: 13px;">Aucun stock enregistré</span>';
      }

      const culturesList = document.getElementById('modalCulturesList');
      if (data.cultures && data.cultures.length > 0) {
        culturesList.innerHTML = data.cultures.map(c => `
          <span style="background: #dcfce7; color: #166534; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600;">${c.culture} — ${Number(c.quantite).toLocaleString('fr-FR')} kg</span>
        `).join('');
      } else {
        culturesList.innerHTML = '<span style="color: #9ca3af; font-size: 13px;">Aucune culture enregistrée</span>';
      }

      const visitesList = document.getElementById('modalVisitesList');
      if (data.visites && data.visites.length > 0) {
        visitesList.innerHTML = data.visites.map(v => `
          <div style="display: flex; justify-content: space-between; align-items: center; background: #f9fafb; padding: 10px 12px; border-radius: 8px; border: 1px solid #f3f4f6;">
            <div>
              <div style="font-size: 13px; font-weight: 600; color: #111827;">${v.action}</div>
              <div style="font-size: 11px; color: #6b7280;">${v.recommandation ?? 'Aucun compte rendu'}</div>
            </div>
            <div style="text-align: right;">
              <div style="font-size: 12px; font-weight: 600; color: #374151;">${v.date}</div>
              <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 2px 6px; border-radius: 999px; background: #eff6ff; color: #1e40af;">${v.statut}</span>
            </div>
          </div>
        `).join('');
      } else {
        visitesList.innerHTML = '<span style="color: #9ca3af; font-size: 13px;">Aucune visite enregistrée</span>';
      }

      renderMiniCharts(data);
      switchTab('stocks');
    } catch (err) {
      console.error('Erreur lors du chargement des détails:', err);
    }
  }

  function generateAnalysis(data) {
    const parts = [];
    if (data.benefice_net < 0) {
      parts.push('Rentabilité négative détectée. Une révision des coûts et des prix de vente est recommandée.');
    } else if (data.benefice_net > 0) {
      parts.push('Exploitation rentable. Les performances sont positives, continuez sur cette lancée.');
    }
    const criticalStocks = (data.stocks || []).filter(s => s.est_critique);
    if (criticalStocks.length > 0) {
      parts.push(`${criticalStocks.length} stock(s) critique(s) nécessitent un réapprovisionnement urgent.`);
    }
    if (data.rendement_moyen >= 1.0) {
      parts.push('Rendement excellent (≥ 1 t/ha).');
    } else if (data.rendement_moyen >= 0.5) {
      parts.push('Rendement correct mais peut être amélioré.');
    }
    if ((data.cultures || []).length > 2) {
      parts.push('Diversification des cultures bénéfique.');
    }
    if (!parts.length) {
      parts.push('Données insuffisantes pour une analyse complète.');
    }
    return parts.join(' ');
  }

  function switchTab(tabName) {
    document.querySelectorAll('.tab-btn-modern').forEach(btn => {
      btn.classList.toggle('active', btn.dataset.tab === tabName);
    });
    document.querySelectorAll('.tab-panel-modern').forEach(panel => {
      panel.style.display = 'none';
    });
    const target = document.getElementById('panel' + tabName.charAt(0).toUpperCase() + tabName.slice(1));
    if (target) {
      target.style.display = 'block';
      target.style.animation = 'none';
      target.offsetHeight;
      target.style.animation = 'fadeInSoft 0.3s ease';
    }
  }

  function renderMiniCharts(data) {
    destroyMiniCharts();

    const stockCtx = document.getElementById('stockMiniChart');
    if (stockCtx && data.stocks && data.stocks.length > 0) {
      const labels = data.stocks.map(s => s.nom);
      const current = data.stocks.map(s => s.quantite);
      const threshold = data.stocks.map(s => s.seuil);
      if (window.Chart) {
        window._stockMiniChart = new Chart(stockCtx, {
          type: 'bar',
          data: {
            labels,
            datasets: [
              { label: 'Actuel', data: current, backgroundColor: '#10b981', borderRadius: 6, barPercentage: 0.6 },
              { label: 'Seuil', data: threshold, backgroundColor: '#f59e0b', borderRadius: 6, barPercentage: 0.6 }
            ]
          },
          options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } } },
            scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { font: { size: 10 } } } }
          }
        });
      }
    }

    const cultureCtx = document.getElementById('cultureMiniChart');
    if (cultureCtx && data.cultures && data.cultures.length > 0) {
      const labels = data.cultures.map(c => c.culture);
      const values = data.cultures.map(c => c.quantite);
      const colors = ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];
      if (window.Chart) {
        window._cultureMiniChart = new Chart(cultureCtx, {
          type: 'doughnut',
          data: {
            labels,
            datasets: [{ data: values, backgroundColor: colors.slice(0, values.length), borderWidth: 0, hoverOffset: 6 }]
          },
          options: {
            responsive: true, maintainAspectRatio: false, cutout: '65%',
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 }, padding: 12 } } }
          }
        });
      }
    }
  }

  function destroyMiniCharts() {
    if (window._stockMiniChart) { window._stockMiniChart.destroy(); window._stockMiniChart = null; }
    if (window._cultureMiniChart) { window._cultureMiniChart.destroy(); window._cultureMiniChart = null; }
  }

  window.closeFarmerModal = function() {
    destroyMiniCharts();
    const modal = document.getElementById('farmerModal');
    modal.hidden = true;
    modal.style.display = 'none';
    document.body.style.overflow = '';
  }

  // Close modal when clicking on overlay
  document.addEventListener('click', function(e) {
    const modal = document.getElementById('farmerModal');
    if (e.target === modal) {
      closeFarmerModal();
    }
  });

  // Close modal with Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeFarmerModal();
    }
  });

  // Tab switching for farmer detail modal
  document.addEventListener('click', function(e) {
    const tabBtn = e.target.closest('.tab-btn-modern');
    if (!tabBtn) return;
    const tab = tabBtn.dataset.tab;
    if (tab) switchTab(tab);
  });

  function updateSystemStatus() {
    const services = [
      { name: "Base de données", status: "online", uptime: "99.9%" },
      { name: "API Services", status: "online", uptime: "99.7%" },
      { name: "File System", status: "online", uptime: "100%" },
      { name: "Cache Redis", status: "online", uptime: "99.8%" },
      { name: "Backup Service", status: "warning", uptime: "95.2%" }
    ];

    const container = document.getElementById('systemStatus');
    if (container) {
      container.innerHTML = services.map(service => `
        <div class="status-item">
          <div class="status-indicator status-${service.status}"></div>
          <div class="status-info">
            <div class="status-name">${service.name}</div>
            <div class="status-uptime">Uptime: ${service.uptime}</div>
          </div>
        </div>
      `).join('');
    }
  }
})();
