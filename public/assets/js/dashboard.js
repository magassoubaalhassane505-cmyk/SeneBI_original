(function () {
  function getRealDashboardData() {
    return window.SeneBI_DASHBOARD || {};
  }

  function computeInsightLine(data) {
    const totalHarvest = data.totalHarvestKg || 0;
    const rendement = data.rendementMoyen || 0;
    if (totalHarvest > 0 && rendement > 0) {
      return `Votre exploitation affiche un rendement de ${rendement.toLocaleString('fr-FR', { maximumFractionDigits: 2 })} t/ha sur un total de ${totalHarvest.toLocaleString('fr-FR')} kg récoltés.`;
    }
    return "Les indicateurs sont cohérents : surveillez l'évolution des prix par culture et la répartition des surfaces.";
  }

  function render(state) {
    const data = getRealDashboardData();
    const k = {
      totalHarvestKg: data.totalHarvestKg || 0,
      hectaresActifs: data.hectaresActifs || 0,
      rendementMoyenTparHa: data.rendementMoyen || 0,
      chiffreAffairesEstimeFcfa: data.totalCA || 0,
      caEvolution: data.caEvolution || 0,
      haEvolution: data.haEvolution || 0,
      rendementEvolution: data.rendementEvolution || 0,
    };

    const totalHarvestEl = document.querySelector("#kpiTotalHarvest");
    const caEl = document.querySelector("#kpiCA");
    const haEl = document.querySelector("#kpiHa");
    const rendEl = document.querySelector("#kpiRend");

    if (totalHarvestEl) {
      totalHarvestEl.textContent = k.totalHarvestKg.toLocaleString("fr-FR");
      totalHarvestEl.style.fontWeight = "bold";
    }
    if (caEl) {
      caEl.textContent = k.chiffreAffairesEstimeFcfa.toLocaleString("fr-FR");
      caEl.style.fontWeight = "bold";
    }
    if (haEl) {
      haEl.textContent = k.hectaresActifs.toLocaleString("fr-FR", { maximumFractionDigits: 1 });
      haEl.style.fontWeight = "bold";
    }
    if (rendEl) {
      rendEl.textContent = k.rendementMoyenTparHa.toLocaleString("fr-FR", { maximumFractionDigits: 2 });
      rendEl.style.fontWeight = "bold";
    }

    const insightEl = document.querySelector("#dashboardInsight");
    if (insightEl) insightEl.textContent = computeInsightLine(data);

    const criticalStocks = data.stockCritical || [];
    const alert = document.querySelector("#stockAlert");
    if (alert) {
      if (criticalStocks.length > 0) {
        alert.classList.add("show");
        const items = criticalStocks.map((c) => `${c.nom} ${Math.round((c.quantite_actuelle / c.seuil_critique) * 100)}%`).join(" • ");
        document.querySelector("#stockAlertText").textContent = `Alerte Stock: ${items}`;
      } else {
        alert.classList.remove("show");
      }
    }

    const sel = document.querySelector("#cerealPriceSelect");
    if (sel && !sel.dataset.bound) {
      sel.dataset.bound = "1";
      sel.addEventListener("change", function () {
        updatePriceChart(this.value);
      });
    }

    renderPriceChart(data);
    renderCultureChart(data);
  }

  function updatePriceChart(selectedCulture) {
    const data = getRealDashboardData();
    renderPriceChart(data, selectedCulture);
  }

  function renderPriceChart(data, selectedCulture) {
    if (!window.Chart || !document.querySelector("#priceChart")) return;

    const culture = selectedCulture || "Riz";
    const prix = data.prixCultures || {};
    const currentPrice = prix[culture.toLowerCase()] || prix[Object.keys(prix)[0]] || 0;

    const ctx = document.querySelector("#priceChart").getContext("2d");
    const existing = Chart.getChart(ctx.canvas);
    if (existing) existing.destroy();

    const labels = ["Jan", "Fév", "Mar", "Avr", "Mai", "Jun", "Jul", "Aoû", "Sep", "Oct", "Nov", "Déc"];
    const prices = labels.map(() => currentPrice);

    const gradient = ctx.createLinearGradient(0, 0, 0, ctx.canvas.height);
    gradient.addColorStop(0, 'rgba(124, 58, 237, 0.3)');
    gradient.addColorStop(1, 'rgba(124, 58, 237, 0.0)');

    new Chart(ctx, {
      type: "line",
      data: {
        labels: labels,
        datasets: [
          {
            label: `Prix ${culture} (FCFA/kg)`,
            data: prices,
            borderColor: "#7c3aed",
            backgroundColor: gradient,
            fill: true,
            tension: 0.35,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointHoverBorderWidth: 3,
            pointHoverBorderColor: "#ffffff",
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: "index", intersect: false },
        plugins: {
          legend: { display: true, position: "bottom", labels: { boxWidth: 12, font: { size: 11, weight: "600" } } },
          tooltip: {
            backgroundColor: "#111827",
            titleColor: "#ffffff",
            bodyColor: "#ffffff",
            titleFont: { weight: "600", size: 14, family: "system-ui, -apple-system, sans-serif" },
            bodyFont: { size: 13, weight: "500", family: "system-ui, -apple-system, sans-serif" },
            padding: 12,
            cornerRadius: 8,
            displayColors: true,
            boxPadding: 4,
            callbacks: {
              title(items) {
                const i = items[0]?.dataIndex;
                const months = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
                return months[i] || "";
              },
              label(ctx) {
                const v = ctx.parsed.y;
                return `● ${v.toLocaleString('fr-FR')} FCFA/kg`;
              },
            },
          },
        },
        scales: {
          y: {
            grid: { color: "rgba(55, 65, 81, 0.15)", lineWidth: 1 },
            ticks: { callback: (v) => `${v.toLocaleString('fr-FR')} FCFA/kg`, color: "#374151", font: { weight: "500" } },
            border: { display: true, color: "#374151", width: 2 }
          },
          x: {
            grid: { display: false },
            ticks: { color: "#374151", font: { weight: "500" } },
            border: { display: true, color: "#374151", width: 2 }
          },
        },
      },
    });
  }

  function renderCultureChart(data) {
    if (!window.Chart || !document.querySelector("#cultureChart")) return;

    const labels = data.culturesLabels || [];
    const values = data.culturesData || [];

    const ctx = document.querySelector("#cultureChart").getContext("2d");
    const existing = Chart.getChart(ctx.canvas);
    if (existing) existing.destroy();

    const hasData = values.length > 0 && values.some(v => v > 0);

    if (!hasData) {
      const emptyDiv = document.createElement('div');
      emptyDiv.style.cssText = 'position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: #6b7280; font-size: 14px; max-width: 90%;';
      emptyDiv.innerHTML = '<i class="fas fa-question-circle" style="font-size: 36px; color: #94a3b8; margin-bottom: 8px; display: block;"></i>Commencez par saisir vos récoltes pour voir la distribution de vos cultures';
      const chartContainer = document.querySelector("#cultureChart").parentElement;
      chartContainer.style.position = 'relative';
      const existingMsg = chartContainer.querySelector('.culture-empty-msg');
      if (existingMsg) existingMsg.remove();
      emptyDiv.className = 'culture-empty-msg';
      chartContainer.appendChild(emptyDiv);
      return;
    }

    const colors = labels.map((_, i) => ["#7c3aed", "#16a34a", "#374151", "#f59e0b", "#3b82f6"][i % 5]);

    new Chart(ctx, {
      type: "doughnut",
      data: {
        labels: labels,
        datasets: [{
          data: values,
          backgroundColor: colors,
          borderWidth: 0,
          hoverOffset: 20,
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: "68%",
        plugins: {
          legend: {
            position: "bottom",
            labels: {
              boxWidth: 10,
              padding: 15,
              font: { size: 12 },
              onClick: (e, legendItem, legend) => {
                const index = legendItem.index;
                const chart = legend.chart;
                const meta = chart.getDatasetMeta(0);
                meta.data[index].hidden = !meta.data[index].hidden;
                chart.update();
              },
            },
          },
          tooltip: {
            backgroundColor: "rgba(15, 23, 42, 0.92)",
            padding: 10,
            cornerRadius: 10,
            callbacks: {
              label(ctx) {
                const weight = ctx.raw;
                const total = values.reduce((a, b) => a + b, 0);
                const percentage = total > 0 ? ((weight / total) * 100).toFixed(1) : 0;
                return `${ctx.label} : ${weight.toLocaleString('fr-FR')} kg (${percentage}%)`;
              },
            },
          },
        },
      },
    });

    const dominant = labels.length > 0 ? labels[values.indexOf(Math.max(...values))] : "N/A";
    const dominantEl = document.querySelector("#dominantCulture");
    if (dominantEl) dominantEl.textContent = dominant;
  }

  document.addEventListener("DOMContentLoaded", function () {
    const auth = window.SeneBI?.requireRole(["manager", "client"], "Acces refuse.");
    if (!auth) return;
    const state = window.SeneBI?.loadState?.() || {};
    window.SeneBI?.renderTopbar?.(state);
    render(state);
    window.addEventListener("senebi:seasonChanged", () => {
      window.SeneBI?.renderTopbar?.(state);
      render(state);
    });
  });
})();
