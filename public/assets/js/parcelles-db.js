(function () {
  const cfg = window.SeneBI_SERVER;
  if (!cfg?.useDb) return;

  function csrfHeaders() {
    return {
      "Content-Type": "application/json",
      Accept: "application/json",
      "X-CSRF-TOKEN": cfg.csrf,
    };
  }

  window.getUiParcelsFromServer = function () {
    if (!Array.isArray(cfg.parcelles)) return null;
    const statsMap = new Map((Array.isArray(cfg.parcelleStats) ? cfg.parcelleStats : []).map(s => [String(s.id), s]));
    return cfg.parcelles.map((p) => {
      const stat = statsMap.get(String(p.id)) || {};
      return {
        id: String(p.id),
        name: p.nom,
        culture: p.culture,
        areaHa: Number(p.surface),
        status: "En culture",
        growth: 50,
        cost: 0,
        performance: stat.badge || null,
        performanceClass: stat.badgeClass || null,
        plantingDate: null,
        region: p.region,
      };
    });
  };

  async function refreshParcelles() {
    const res = await fetch(`${cfg.apiBase}/parcelles`, { headers: { Accept: "application/json" } });
    if (!res.ok) return;
    const json = await res.json();
    cfg.parcelles = json.data || [];
    if (typeof window.renderParcels === "function") window.renderParcels();
  }

  document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("addParcelForm");
    if (form && !form.dataset.bound) {
      form.dataset.bound = "1";
      form.addEventListener("submit", async (e) => {
        e.preventDefault();
        const payload = {
          nom: document.getElementById("parcelNom")?.value?.trim(),
          region: document.getElementById("parcelRegion")?.value?.trim(),
          surface: Number(document.getElementById("parcelSurface")?.value),
          culture: document.getElementById("parcelCulture")?.value?.trim(),
        };
        const res = await fetch(`${cfg.apiBase}/parcelles`, {
          method: "POST",
          headers: csrfHeaders(),
          body: JSON.stringify(payload),
        });
        if (!res.ok) {
          alert("Impossible d'enregistrer la parcelle.");
          return;
        }
        form.reset();
        await refreshParcelles();
      });
    }
  });

  window.SeneBI_refreshParcelles = refreshParcelles;
})();
