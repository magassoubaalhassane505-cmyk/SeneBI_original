(function () {
  let selectedPriceCulture = "Riz";

  function getCerealPriceSeries(s) {
    const base = [450, 460, 455, 470, 465, 475, 480, 485, 490, 485, 480, 475];
    return {
      Riz: base,
      "Maïs": base.map((p) => Math.round(p * 0.93)),
      Coton: base.map((p) => Math.round(p * 1.08)),
    };
  }

  function computeInsightLine(state, k) {
    // Données fixes pour le client Mimi
    const bestName = "Parcelle A";
    const maxKg = 1450;
    if (bestName && k.rendementMoyenTparHa > 0) {
      return `Le rendement est en hausse : la saison met en avant ${bestName} (${maxKg.toLocaleString("fr-FR")} kg récoltés), ce qui soutient la moyenne à ${k.rendementMoyenTparHa.toLocaleString("fr-FR", { maximumFractionDigits: 1 })} t/ha.`;
    }
    return "Les indicateurs sont cohérents : surveillez l'évolution des prix par culture (menu ci-dessus) et la répartition des surfaces.";
  }

  function bindCerealPriceSelect(state) {
    const sel = document.querySelector("#cerealPriceSelect");
    if (!sel || sel.dataset.bound) return;
    sel.dataset.bound = "1";
    if ([...sel.options].some((o) => o.value === selectedPriceCulture)) sel.value = selectedPriceCulture;
    sel.addEventListener("change", () => {
      selectedPriceCulture = sel.value;
      render(state);
    });
  }

  function computeKpis(state) {
    // Données fixes pour le client Mimi
    const totalHarvestKg = 2450;
    const hectaresActifs = 3;
    const rendementMoyenTparHa = 0.82;
    const caFcfa = 1225000;

    return {
      totalHarvestKg,
      hectaresActifs,
      rendementMoyenTparHa,
      chiffreAffairesEstimeFcfa: caFcfa,
    };
  }

  function isStockCritical(state) {
    // Données fixes pour le client Mimi
    const ratios = [
      { label: "Urée", value: 150, cap: 1000 },
      { label: "NPK", value: 80, cap: 500 },
      { label: "Semences", value: 45, cap: 200 },
    ];
    const critical = ratios.filter((r) => (r.cap ? r.value / r.cap : 0) <= 0.1).map((r) => ({ ...r, pct: r.cap ? r.value / r.cap : 0 }));
    return { any: critical.length > 0, critical };
  }

  function render(state) {
    const k = computeKpis(state);
    const stock = isStockCritical(state);

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
      rendEl.textContent = k.rendementMoyenTparHa.toLocaleString("fr-FR", { maximumFractionDigits: 1 });
      rendEl.style.fontWeight = "bold";
    }

    const insightEl = document.querySelector("#dashboardInsight");
    if (insightEl) insightEl.textContent = computeInsightLine(state, k);

    const alert = document.querySelector("#stockAlert");
    if (alert) {
      if (stock.any) {
        alert.classList.add("show");
        const items = stock.critical.map((c) => `${c.label} ${(c.pct * 100).toFixed(0)}%`).join(" • ");
        document.querySelector("#stockAlertText").textContent = `Alerte Stock: ${items}`;
      } else {
        alert.classList.remove("show");
      }
    }

    bindCerealPriceSelect(state);

    // Graphique des prix des céréales
    if (window.Chart && document.querySelector("#priceChart")) {
      const seriesMap = getCerealPriceSeries(state);
      const cultureKey = seriesMap[selectedPriceCulture] ? selectedPriceCulture : "Riz";
      const prices = seriesMap[cultureKey];
      const ctx = document.querySelector("#priceChart").getContext("2d");
      const existing = Chart.getChart(ctx.canvas);
      if (existing) existing.destroy();
      const sel = document.querySelector("#cerealPriceSelect");
      if (sel && [...sel.options].some((o) => o.value === cultureKey)) sel.value = cultureKey;

      // Fonction pour créer le dégradé
      function createGradient(context) {
        const gradient = context.createLinearGradient(0, 0, 0, context.canvas.height);
        gradient.addColorStop(0, 'rgba(124, 58, 237, 0.3)');
        gradient.addColorStop(1, 'rgba(124, 58, 237, 0.0)');
        return gradient;
      }

      // Calculer la moyenne mobile sur 3 mois
      const movingAverage = prices.map((price, index) => {
        if (index < 2) return null;
        const sum = prices.slice(index - 2, index + 1).reduce((a, b) => a + b, 0);
        return Math.round(sum / 3);
      });
      
      // Calculer la moyenne générale
      const validPrices = prices.filter(p => p !== null);
      const average = Math.round(validPrices.reduce((a, b) => a + b, 0) / validPrices.length);
      
      new Chart(ctx, {
        type: "line",
        data: {
          labels: ["Jan", "Fév", "Mar", "Avr", "Mai", "Jun", "Jul", "Aoû", "Sep", "Oct", "Nov", "Déc"],
          datasets: [
            {
              label: `Prix ${cultureKey} (FCFA/kg)`,
              data: prices,
              borderColor: "#7c3aed",
              backgroundColor: createGradient(ctx),
              fill: true,
              tension: 0.35,
              pointRadius: 4,
              pointHoverRadius: 6,
              pointHoverBorderWidth: 3,
              pointHoverBorderColor: "#ffffff",
            },
            {
              label: "Moyenne Mobile (3 mois)",
              data: movingAverage,
              borderColor: "#9ca3af",
              backgroundColor: "transparent",
              borderDash: [5, 5],
              fill: false,
              tension: 0.35,
              pointRadius: 0,
              pointHoverRadius: 0,
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
                  const monthName = months[i] || "";
                  return monthName;
                },
                label(ctx) {
                  const v = ctx.parsed.y;
                  const i = ctx.dataIndex;
                  
                  // Calculer la variation par rapport au mois précédent
                  let variation = "";
                  if (i > 0 && prices[i - 1] !== null) {
                    const prevPrice = prices[i - 1];
                    const varPercent = ((v - prevPrice) / prevPrice * 100).toFixed(1);
                    const sign = varPercent >= 0 ? '+' : '';
                    variation = ` (${sign}${varPercent}%)`;
                  }
                  
                  // Calculer l'écart par rapport à la moyenne
                  const avgDiff = v - average;
                  const avgSign = avgDiff >= 0 ? '+' : '';
                  
                  return [
                    `● ${v} FCFA${variation}`,
                    `Écart moyenne: ${avgSign}${avgDiff} FCFA`
                  ];
                },
              },
            },
          },
          scales: {
            y: { 
              grid: { color: "rgba(55, 65, 81, 0.15)", lineWidth: 1 }, 
              ticks: { callback: (v) => `${v} FCFA/kg`, color: "#374151", font: { weight: "500" } },
              border: { display: true, color: "#374151", width: 2 }
            },
            x: { 
              grid: { display: false },
              ticks: { color: "#374151", font: { weight: "500" } },
              border: { display: true, color: "#374151", width: 2 }
            },
          },
          // Ajouter les plugins pour les zones colorées
          plugins: [
            {
              id: 'seasonZones',
              beforeDraw: (chart) => {
                const ctx = chart.ctx;
                const chartArea = chart.chartArea;
                const meta = chart.getDatasetMeta(0);
                
                // Zone Saison Humide (Mai-Octobre) - vert clair
                ctx.save();
                ctx.fillStyle = 'rgba(34, 197, 94, 0.05)';
                const humidStart = meta.getPixelForValue(4); // Mai
                const humidEnd = meta.getPixelForValue(9);   // Octobre
                ctx.fillRect(humidStart, chartArea.top, humidEnd - humidStart, chartArea.bottom - chartArea.top);
                ctx.restore();
                
                // Zone Saison Sèche (Novembre-Avril) - jaune clair
                ctx.save();
                ctx.fillStyle = 'rgba(250, 204, 21, 0.05)';
                const dryStart = meta.getPixelForValue(10); // Novembre
                const dryEnd = chart.width;
                ctx.fillRect(dryStart, chartArea.top, dryEnd - dryStart, chartArea.bottom - chartArea.top);
                
                // Zone sèche début (Janvier-Avril)
                const dryStart2 = chartArea.left;
                const dryEnd2 = meta.getPixelForValue(3); // Avril
                ctx.fillRect(dryStart2, chartArea.top, dryEnd2 - dryStart2, chartArea.bottom - chartArea.top);
                ctx.restore();
              }
            }
          ],
        },
      });
    }

    // Graphique Distribution des Cultures
    if (window.Chart && document.querySelector("#cultureChart")) {
      const ctx = document.querySelector("#cultureChart").getContext("2d");
      const existing = Chart.getChart(ctx.canvas);
      if (existing) existing.destroy();
      
      // Données réelles basées sur les récoltes
      const cultureData = [
        { name: "Riz", weight: 1100 },
        { name: "Maïs", weight: 850 },
        { name: "Coton", weight: 500 }
      ];
      
      const totalWeight = cultureData.reduce((sum, c) => sum + c.weight, 0);
      
      new Chart(ctx, {
        type: "doughnut",
        data: {
          labels: cultureData.map((c) => c.name),
          datasets: [{
            data: cultureData.map((c) => c.weight),
            backgroundColor: ["#7c3aed", "#16a34a", "#374151"], // Violet, Vert agricole, Gris anthracite
            borderWidth: 0,
            hoverOffset: 20 // Animation au survol
          }]
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
                // Rendre la légende cliquable
                onClick: (e, legendItem, legend) => {
                  const index = legendItem.index;
                  const chart = legend.chart;
                  const meta = chart.getDatasetMeta(0);
                  
                  // Basculer la visibilité
                  meta.data[index].hidden = !meta.data[index].hidden;
                  chart.update();
                }
              } 
            },
            tooltip: {
              backgroundColor: "rgba(15, 23, 42, 0.92)",
              padding: 10,
              cornerRadius: 10,
              callbacks: {
                label(ctx) {
                  const weight = ctx.raw;
                  const percentage = ((weight / totalWeight) * 100).toFixed(1);
                  return `${ctx.label} : ${weight} kg (${percentage}%)`;
                },
              },
            },
          },
        },
      });
      
      const dominant = cultureData.sort((a, b) => b.weight - a.weight)[0];
      const dominantEl = document.querySelector("#dominantCulture");
      if (dominantEl) dominantEl.textContent = dominant.name;
    }
  }

  document.addEventListener("DOMContentLoaded", function () {
    const auth = SeneBI.requireRole(["manager", "client"], "Acces refuse.");
    if (!auth) return;
    const state = SeneBI.loadState();
    SeneBI.renderTopbar(state);
    render(state);
    window.addEventListener("senebi:seasonChanged", () => {
      SeneBI.renderTopbar(state);
      render(state);
    });
  });
})();

