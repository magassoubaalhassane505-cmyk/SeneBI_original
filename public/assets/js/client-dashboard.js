(function () {
  function fmtMoney(value) {
    return `${Number(value || 0).toLocaleString("fr-FR")} FCFA`;
  }

  function bindExport() {
    const btn = document.querySelector("#clientExportBtn");
    if (!btn || btn.dataset.bound) return;
    btn.dataset.bound = "1";
    btn.addEventListener("click", () => {
      const state = SeneBI.loadState();
      SeneBI.exportStyledFinancialPdf({
        state,
        title: "Rapport de synthese",
        subtitle: `Espace client · Saison ${state.season} · ${new Date().toLocaleDateString("fr-FR", { dateStyle: "long" })}`,
        filename: `SeneBI_Rapport_Client_${state.season}.pdf`,
        charts: [
          { canvas: document.querySelector("#financeChart"), title: "Revenus, couts et benefice (millions FCFA)", heightPt: 220 },
          { canvas: document.querySelector("#stockChart"), title: "Stocks intrants (kg)", heightPt: 200 },
        ],
      });
    });
  }

  function render() {
    const auth = SeneBI.requireRole(["manager", "client"], "Acces refuse.");
    if (!auth) return;
    const state = SeneBI.loadState ? SeneBI.loadState() : {};
    if (SeneBI.renderTopbar) SeneBI.renderTopbar(state);
    
    // Données de base pour le client Mimi
    const harvestTotal = 2450; // kg
    const salesFcfa = 1225000; // FCFA
    const intrantsCostFcfa = 740000; // FCFA
    const profit = salesFcfa - intrantsCostFcfa;
    const margin = salesFcfa > 0 ? (profit / salesFcfa) * 100 : 0;

    const kHarvest = document.querySelector("#kpiHarvest");
    const kSales = document.querySelector("#kpiSales");
    const kProfit = document.querySelector("#kpiProfit");
    const kMargin = document.querySelector("#kpiMargin");

    if (kHarvest) kHarvest.textContent = `${harvestTotal.toLocaleString("fr-FR")} kg`;
    if (kSales) kSales.textContent = fmtMoney(salesFcfa);
    if (kProfit) kProfit.textContent = fmtMoney(profit);
    if (kMargin) kMargin.textContent = `${margin.toLocaleString("fr-FR", { minimumFractionDigits: 1, maximumFractionDigits: 1 })}%`;

    if (!window.Chart) return;

    const financeCanvas = document.querySelector("#financeChart");
    if (financeCanvas) {
      const existing = Chart.getChart(financeCanvas);
      if (existing) existing.destroy();
      new Chart(financeCanvas, {
        type: "bar",
        data: {
          labels: ["Revenus", "Couts", "Benefice"],
          datasets: [{
            data: [1.225, 0.740, 0.485],
            backgroundColor: ["#10b981", "#ef4444", "#6366f1"],
            borderRadius: 10,
          }],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          interaction: { mode: "index", intersect: false },
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: "rgba(15, 23, 42, 0.92)",
              padding: 10,
              cornerRadius: 10,
              callbacks: {
                title(items) {
                  return items[0]?.label ? items[0].label : "";
                },
                label(ctx) {
                  const v = ctx.parsed.y;
                  return `${v.toLocaleString("fr-FR", { maximumFractionDigits: 2 })} M FCFA`;
                },
              },
            },
          },
          scales: { y: { beginAtZero: true }, x: { grid: { display: false } } },
        },
      });
    }

    const stockCanvas = document.querySelector("#stockChart");
    if (stockCanvas) {
      const existing = Chart.getChart(stockCanvas);
      if (existing) existing.destroy();
      new Chart(stockCanvas, {
        type: "bar",
        data: {
          labels: ["Uree", "NPK", "Semences"],
          datasets: [{
            data: [150, 80, 45],
            backgroundColor: ["#22c55e", "#f97316", "#0ea5e9"],
            borderRadius: 10,
          }],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          interaction: { mode: "index", intersect: false },
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: "rgba(15, 23, 42, 0.92)",
              padding: 10,
              cornerRadius: 10,
              callbacks: {
                title(items) {
                  return items[0]?.label ? `Intrant : ${items[0].label}` : "";
                },
                label(ctx) {
                  return `Stock : ${ctx.parsed.y.toLocaleString("fr-FR")} kg`;
                },
              },
            },
          },
          scales: { y: { beginAtZero: true }, x: { grid: { display: false } } },
        },
      });
    }

    bindExport();
  }

  document.addEventListener("DOMContentLoaded", render);
})();
