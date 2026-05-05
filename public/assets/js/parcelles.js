(function () {
  function fmtHectaresWord(n) {
    const v = Number(n || 0);
    const num = v.toLocaleString("fr-FR", { maximumFractionDigits: 1 });
    return `${num} hectares`;
  }
  function fmtKg(n) {
    return `${Number(n || 0).toLocaleString("fr-FR")} kg`;
  }
  function fmtKgPerHa(n) {
    const v = Math.round(Number(n || 0));
    return `${v.toLocaleString("fr-FR")} kg/ha`;
  }
  function fmtCost(n) {
    return `${Number(n || 0).toLocaleString("fr-FR")} FCFA`;
  }

  function renderParcels() {
    const list = SeneBI.qs("#parcelsList");
    if (!list) return;

    const uiParcels = [
      // RÉGION BAMAKO (bko)
      { id: "BKO-N1", name: "Parcelle Bamako Nord-1", culture: "Riz", areaHa: 4.2, status: "En culture", growth: 80, cost: 98000, performance: 12, plantingDate: "10/01/2026" },
      { id: "BKO-S1", name: "Parcelle Bamako Sud-1", culture: "Maïs", areaHa: 3.5, status: "Récoltée", growth: 100, cost: 78000, performance: 18, plantingDate: "20/11/2025" },
      { id: "BKO-C1", name: "Parcelle Bamako Centre-1", culture: "Coton", areaHa: 2.8, status: "En culture", growth: 65, cost: 67000, performance: null, plantingDate: "15/02/2026" },
      
      // RÉGION KAYES (kay)
      { id: "KAY-N1", name: "Parcelle Kayes Nord-1", culture: "Riz", areaHa: 5.1, status: "En culture", growth: 70, cost: 115000, performance: null, plantingDate: "05/01/2026" },
      { id: "KAY-S1", name: "Parcelle Kayes Sud-1", culture: "Maïs", areaHa: 4.2, status: "En jachère", growth: 0, cost: 52000, performance: null, lastActivity: "10/02/2026" },
      { id: "KAY-E1", name: "Parcelle Kayes Est-1", culture: "Coton", areaHa: 3.8, status: "Récoltée", growth: 100, cost: 89000, performance: 22, plantingDate: "25/11/2025" },
      
      // RÉGION KOULIKORO (kou)
      { id: "KOU-N1", name: "Parcelle Koulikoro Nord-1", culture: "Riz", areaHa: 6.2, status: "En culture", growth: 85, cost: 142000, performance: 15, plantingDate: "08/01/2026" },
      { id: "KOU-S1", name: "Parcelle Koulikoro Sud-1", culture: "Maïs", areaHa: 4.8, status: "En culture", growth: 60, cost: 108000, performance: null, plantingDate: "12/02/2026" },
      { id: "KOU-C1", name: "Parcelle Koulikoro Centre-1", culture: "Coton", areaHa: 3.5, status: "Récoltée", growth: 100, cost: 82000, performance: 20, plantingDate: "18/11/2025" },
      
      // RÉGION SÉGOU (seg)
      { id: "SEG-N1", name: "Parcelle Ségou Nord-1", culture: "Riz", areaHa: 5.8, status: "En culture", growth: 75, cost: 132000, performance: null, plantingDate: "03/01/2026" },
      { id: "SEG-S1", name: "Parcelle Ségou Sud-1", culture: "Maïs", areaHa: 4.1, status: "En jachère", growth: 0, cost: 48000, performance: null, lastActivity: "05/02/2026" },
      { id: "SEG-E1", name: "Parcelle Ségou Est-1", culture: "Coton", areaHa: 3.2, status: "Récoltée", growth: 100, cost: 76000, performance: 25, plantingDate: "22/11/2025" },
      
      // RÉGION SIKASSO (sik)
      { id: "SIK-N1", name: "Parcelle Sikasso Nord-1", culture: "Riz", areaHa: 7.1, status: "En culture", growth: 90, cost: 165000, performance: 18, plantingDate: "01/01/2026" },
      { id: "SIK-S1", name: "Parcelle Sikasso Sud-1", culture: "Maïs", areaHa: 5.3, status: "En culture", growth: 55, cost: 118000, performance: null, plantingDate: "18/02/2026" },
      { id: "SIK-C1", name: "Parcelle Sikasso Centre-1", culture: "Coton", areaHa: 4.5, status: "Récoltée", growth: 100, cost: 105000, performance: 28, plantingDate: "15/11/2025" },
      
      // RÉGION Mopti (mop)
      { id: "MOP-N1", name: "Parcelle Mopti Nord-1", culture: "Riz", areaHa: 4.5, status: "En culture", growth: 70, cost: 102000, performance: null, plantingDate: "07/01/2026" },
      { id: "MOP-S1", name: "Parcelle Mopti Sud-1", culture: "Maïs", areaHa: 3.2, status: "Récoltée", growth: 100, cost: 73000, performance: 16, plantingDate: "28/11/2025" },
      { id: "MOP-E1", name: "Parcelle Mopti Est-1", culture: "Coton", areaHa: 2.9, status: "En jachère", growth: 0, cost: 38000, performance: null, lastActivity: "12/02/2026" },
      
      // RÉGION TOMBOUCTOU (tom)
      { id: "TOM-N1", name: "Parcelle Tombouctou Nord-1", culture: "Riz", areaHa: 3.8, status: "En culture", growth: 65, cost: 86000, performance: null, plantingDate: "14/01/2026" },
      { id: "TOM-S1", name: "Parcelle Tombouctou Sud-1", culture: "Maïs", areaHa: 2.5, status: "Récoltée", growth: 100, cost: 58000, performance: 12, plantingDate: "30/11/2025" },
      { id: "TOM-E1", name: "Parcelle Tombouctou Est-1", culture: "Coton", areaHa: 2.1, status: "En culture", growth: 45, cost: 49000, performance: null, plantingDate: "25/02/2026" },
      
      // RÉGION GAO (gao)
      { id: "GAO-N1", name: "Parcelle Gao Nord-1", culture: "Riz", areaHa: 3.2, status: "En culture", growth: 60, cost: 72000, performance: null, plantingDate: "16/01/2026" },
      { id: "GAO-S1", name: "Parcelle Gao Sud-1", culture: "Maïs", areaHa: 2.8, status: "En jachère", growth: 0, cost: 35000, performance: null, lastActivity: "08/02/2026" },
      { id: "GAO-E1", name: "Parcelle Gao Est-1", culture: "Coton", areaHa: 2.4, status: "Récoltée", growth: 100, cost: 56000, performance: 14, plantingDate: "02/12/2025" },
      
      // RÉGION KIDAL (kid)
      { id: "KID-N1", name: "Parcelle Kidal Nord-1", culture: "Riz", areaHa: 2.9, status: "En culture", growth: 55, cost: 65000, performance: null, plantingDate: "19/01/2026" },
      { id: "KID-S1", name: "Parcelle Kidal Sud-1", culture: "Maïs", areaHa: 2.1, status: "Récoltée", growth: 100, cost: 48000, performance: 10, plantingDate: "05/12/2025" },
      { id: "KID-E1", name: "Parcelle Kidal Est-1", culture: "Coton", areaHa: 1.8, status: "En jachère", growth: 0, cost: 28000, performance: null, lastActivity: "15/02/2026" },
      
      // Parcelles existantes (compatibilité)
      { id: "PN", name: "Parcelle Nord", culture: "Riz", areaHa: 5.5, status: "En culture", growth: 75, cost: 125500, performance: null, plantingDate: "12/01/2026" },
      { id: "PS", name: "Parcelle Sud", culture: "Maïs", areaHa: 3.2, status: "Récoltée", growth: 100, cost: 82000, performance: 15, plantingDate: "15/11/2025" },
      { id: "PE", name: "Parcelle Est", culture: "Coton", areaHa: 4.0, status: "En culture", growth: 40, cost: 95000, performance: null, plantingDate: "20/02/2026" },
      { id: "PO", name: "Parcelle Ouest", culture: "Riz", areaHa: 2.8, status: "En jachère", growth: 0, cost: 45000, performance: null, lastActivity: "01/02/2026" },
      { id: "PC", name: "Parcelle Centre", culture: "Maïs", areaHa: 6.0, status: "En culture", growth: 85, cost: 158000, performance: null, plantingDate: "08/01/2026" },
    ];

    const activityJournal = {
      // RÉGION BAMAKO
      "BKO-N1": [
        { label: "Dernier arrosage", value: "Hier (14 h)" },
        { label: "Engrais applique", value: "08/04/2026 (NPK)" },
      ],
      "BKO-S1": [
        { label: "Dernier arrosage", value: "Il y a 2 jours" },
        { label: "Engrais applique", value: "20/03/2026" },
      ],
      "BKO-C1": [
        { label: "Dernier arrosage", value: "Aujourd'hui (matin)" },
        { label: "Engrais applique", value: "12/04/2026" },
      ],
      
      // RÉGION KAYES
      "KAY-N1": [
        { label: "Dernier arrosage", value: "Hier (18 h)" },
        { label: "Engrais applique", value: "05/04/2026 (Ureea)" },
      ],
      "KAY-S1": [
        { label: "Dernier arrosage", value: "— (jachere)" },
        { label: "Derniere intervention", value: "10/02/2026" },
      ],
      "KAY-E1": [
        { label: "Dernier arrosage", value: "Il y a 4 jours" },
        { label: "Engrais applique", value: "18/03/2026" },
      ],
      
      // RÉGION KOULIKORO
      "KOU-N1": [
        { label: "Dernier arrosage", value: "Hier (15 h)" },
        { label: "Engrais applique", value: "02/04/2026 (NPK)" },
      ],
      "KOU-S1": [
        { label: "Dernier arrosage", value: "Aujourd'hui (matin)" },
        { label: "Engrais applique", value: "15/04/2026" },
      ],
      "KOU-C1": [
        { label: "Dernier arrosage", value: "Il y a 3 jours" },
        { label: "Engrais applique", value: "25/03/2026" },
      ],
      
      // RÉGION SÉGOU
      "SEG-N1": [
        { label: "Dernier arrosage", value: "Hier (16 h)" },
        { label: "Engrais applique", value: "06/04/2026 (Complexe)" },
      ],
      "SEG-S1": [
        { label: "Dernier arrosage", value: "— (jachere)" },
        { label: "Derniere intervention", value: "05/02/2026" },
      ],
      "SEG-E1": [
        { label: "Dernier arrosage", value: "Il y a 5 jours" },
        { label: "Engrais applique", value: "20/03/2026" },
      ],
      
      // RÉGION SIKASSO
      "SIK-N1": [
        { label: "Dernier arrosage", value: "Hier (13 h)" },
        { label: "Engrais applique", value: "09/04/2026 (NPK)" },
      ],
      "SIK-S1": [
        { label: "Dernier arrosage", value: "Aujourd'hui (matin)" },
        { label: "Engrais applique", value: "18/04/2026" },
      ],
      "SIK-C1": [
        { label: "Dernier arrosage", value: "Il y a 2 jours" },
        { label: "Engrais applique", value: "12/03/2026" },
      ],
      
      // RÉGION Mopti
      "MOP-N1": [
        { label: "Dernier arrosage", value: "Hier (17 h)" },
        { label: "Engrais applique", value: "07/04/2026 (Ureea)" },
      ],
      "MOP-S1": [
        { label: "Dernier arrosage", value: "Il y a 3 jours" },
        { label: "Engrais applique", value: "24/03/2026" },
      ],
      "MOP-E1": [
        { label: "Dernier arrosage", value: "— (jachere)" },
        { label: "Derniere intervention", value: "12/02/2026" },
      ],
      
      // RÉGION TOMBOUCTOU
      "TOM-N1": [
        { label: "Dernier arrosage", value: "Hier (19 h)" },
        { label: "Engrais applique", value: "04/04/2026 (Complexe)" },
      ],
      "TOM-S1": [
        { label: "Dernier arrosage", value: "Il y a 4 jours" },
        { label: "Engrais applique", value: "28/03/2026" },
      ],
      "TOM-E1": [
        { label: "Dernier arrosage", value: "Aujourd'hui (soir)" },
        { label: "Engrais applique", value: "20/04/2026" },
      ],
      
      // RÉGION GAO
      "GAO-N1": [
        { label: "Dernier arrosage", value: "Hier (16 h)" },
        { label: "Engrais applique", value: "11/04/2026 (NPK)" },
      ],
      "GAO-S1": [
        { label: "Dernier arrosage", value: "— (jachere)" },
        { label: "Derniere intervention", value: "08/02/2026" },
      ],
      "GAO-E1": [
        { label: "Dernier arrosage", value: "Il y a 5 jours" },
        { label: "Engrais applique", value: "30/03/2026" },
      ],
      
      // RÉGION KIDAL
      "KID-N1": [
        { label: "Dernier arrosage", value: "Hier (18 h)" },
        { label: "Engrais applique", value: "13/04/2026 (Ureea)" },
      ],
      "KID-S1": [
        { label: "Dernier arrosage", value: "Il y a 6 jours" },
        { label: "Engrais applique", value: "02/04/2026" },
      ],
      "KID-E1": [
        { label: "Dernier arrosage", value: "— (jachere)" },
        { label: "Derniere intervention", value: "15/02/2026" },
      ],
      
      // Parcelles existantes (compatibilité)
      PN: [
        { label: "Dernier arrosage", value: "Hier (16 h)" },
        { label: "Engrais applique", value: "10/04/2026 (NPK)" },
      ],
      PS: [
        { label: "Dernier arrosage", value: "Il y a 3 jours" },
        { label: "Engrais applique", value: "22/03/2026" },
      ],
      PE: [
        { label: "Dernier arrosage", value: "Aujourd'hui (matin)" },
        { label: "Engrais applique", value: "05/04/2026" },
      ],
      PO: [
        { label: "Dernier arrosage", value: "— (jachere)" },
        { label: "Derniere intervention", value: "01/02/2026" },
      ],
      PC: [
        { label: "Dernier arrosage", value: "Hier" },
        { label: "Engrais applique", value: "28/03/2026" },
      ],
    };

    const demoHarvests = [
      { parcelId: "PN", date: "2025-11-15", quantityKg: 22000 },
      { parcelId: "PS", date: "2026-02-20", quantityKg: 0 },
      { parcelId: "PC", date: "2025-12-10", quantityKg: 18000 },
    ];

    const lastHarvestByParcel = new Map();
    for (const h of demoHarvests) {
      const prev = lastHarvestByParcel.get(h.parcelId);
      if (!prev || prev.date < h.date) lastHarvestByParcel.set(h.parcelId, h);
    }

    const badgeClass = (st) => (st === "En culture" ? "green" : st === "Récoltée" ? "blue" : "yellow");

    list.innerHTML = uiParcels
      .map((p) => {
        const h = lastHarvestByParcel.get(p.id);
        const hasHarvest = h && Number(h.quantityKg || 0) > 0;
        const yieldKgHa = hasHarvest ? Number(h.quantityKg) / Number(p.areaHa || 1) : null;
        const dateFr = h?.date ? new Date(h.date).toLocaleDateString("fr-FR") : null;
        const journal = activityJournal[p.id] || [];
        const journalHtml = journal
          .map(
            (j) => `
            <li class="parcel-journal-item">
              <span class="parcel-journal-k">${j.label}</span>
              <span class="parcel-journal-v">${j.value}</span>
            </li>`
          )
          .join("");
        return `
          <article class="parcel-card ${p.status === "En jachère" ? 'fallow-card' : ''}">
            <div class="parcel-head">
              <div class="parcel-name">${p.name}</div>
              <span class="badge ${badgeClass(p.status)}">${p.status}</span>
            </div>
            
            <!-- Barre de progression de croissance -->
            <div class="parcel-growth">
              <div class="growth-bar">
                <div class="growth-fill" style="width: ${p.growth}%"></div>
              </div>
              <span class="growth-text">${p.growth}% de croissance</span>
            </div>

            <div class="parcel-body">
            <div class="parcel-grid">
              <div class="kv">
                <div class="k">Culture</div>
                <div class="v">
                  ${p.culture}
                  ${p.status === "En culture" && p.plantingDate ? `<div class="planting-date">Semé le : ${p.plantingDate}</div>` : ""}
                  ${p.status === "En jachère" && p.lastActivity ? `<div class="planting-date">Dernière activité : ${p.lastActivity}</div>` : ""}
                </div>
              </div>
              <div class="kv"><div class="k">Surface</div><div class="v">${fmtHectaresWord(p.areaHa)}</div></div>
              <div class="kv"><div class="k">Dernière récolte</div><div class="v">${dateFr || "—"}</div></div>

              <div class="kv"><div class="k">Quantité</div><div class="v">${hasHarvest ? fmtKg(h.quantityKg) : "—"}</div></div>
              <div class="kv"><div class="k">Coût investi</div><div class="v">${fmtCost(p.cost)}</div></div>
              <div class="kv">
                <div class="k">Rendement</div>
                <div class="v yield">
                  <span>${hasHarvest ? fmtKgPerHa(yieldKgHa) : "—"}</span>
                  ${hasHarvest ? `<span class="yield-check" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></span>` : ""}
                  ${hasHarvest && p.performance ? `<span class="performance-indicator" title="+${p.performance}% par rapport à la saison dernière" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 14l5-5 5 5"/></svg></span>` : ""}
                </div>
              </div>
              <div></div>
            </div>
            <aside class="parcel-journal" aria-label="Journal d'activites">
              <div class="parcel-journal-title">Journal d'activites</div>
              <ul class="parcel-journal-list">
                ${journalHtml}
              </ul>
            </aside>
            </div>
            ${p.status !== "En jachère" ? `
            <!-- Bouton d'action rapide - Style SeneBI moderne -->
            <div class="parcel-actions">
              <button class="apply-intrant-btn" onclick="window.location.href='/manager/stocks'">
                Appliquer Intrant
              </button>
            </div>
            ` : '<!-- PAS DE BOUTON - PARCELLE EN JACHÈRE -->'}
          </article>
        `;
      })
      .join("");

    const parcelSelect = SeneBI.qs("#parcelle-recoltee");
    if (parcelSelect) {
      parcelSelect.innerHTML = `<option value="">Sélectionner une parcelle</option>` + uiParcels.map((p) => `<option value="${p.id}">${p.name}</option>`).join("");
    }
  }

  function bindForm() {
    const openBtn = SeneBI.qs("#openHarvestBtn");
    const panel = SeneBI.qs("#harvestPanel");
    const cancelBtn = SeneBI.qs("#cancelHarvestBtn");
    if (panel) {
      panel.classList.remove("show");
      panel.setAttribute("aria-hidden", "true");
    }
    if (openBtn && panel && !openBtn.dataset.bound) {
      openBtn.dataset.bound = "1";
      openBtn.addEventListener("click", () => {
        const willShow = !panel.classList.contains("show");
        panel.classList.toggle("show", willShow);
        panel.setAttribute("aria-hidden", willShow ? "false" : "true");
        if (willShow) panel.scrollIntoView({ behavior: "smooth", block: "start" });
      });
    }
    if (cancelBtn && panel && !cancelBtn.dataset.bound) {
      cancelBtn.dataset.bound = "1";
      cancelBtn.addEventListener("click", () => {
        panel.classList.remove("show");
        panel.setAttribute("aria-hidden", "true");
      });
    }
    const form = SeneBI.qs("#harvestForm");
    if (form && !form.dataset.bound) {
      form.dataset.bound = "1";
      form.addEventListener("submit", (e) => {
        e.preventDefault();
        const parcelId = SeneBI.qs("#parcelle-recoltee").value;
        const date = SeneBI.qs("#date-recolte").value;
        const quantityKg = Number(SeneBI.qs("#quantite-recoltee").value);
        if (!parcelId || !date || !Number.isFinite(quantityKg) || quantityKg <= 0) return;
        form.reset();
        const d = SeneBI.qs("#date-recolte");
        if (d) d.valueAsDate = new Date();
        if (panel) {
          panel.classList.remove("show");
          panel.setAttribute("aria-hidden", "true");
        }
      });
      const d = SeneBI.qs("#date-recolte");
      if (d && !d.value) d.valueAsDate = new Date();
    }
  }

  document.addEventListener("DOMContentLoaded", function () {
    const auth = SeneBI.requireRole(["manager", "client"], "Acces refuse.");
    if (!auth) return;
    const readOnly = false; // Les clients peuvent aussi saisir des récoltes
    const openBtn = SeneBI.qs("#openHarvestBtn");
    if (openBtn) openBtn.hidden = readOnly;
    const panel = SeneBI.qs("#harvestPanel");
    if (panel && readOnly) {
      panel.classList.remove("show");
      panel.hidden = true;
      panel.setAttribute("aria-hidden", "true");
    }

    const state = SeneBI.loadState();
    SeneBI.renderTopbar(state);
    renderParcels();
    if (!readOnly) bindForm();
    window.addEventListener("senebi:seasonChanged", () => SeneBI.renderTopbar(state));
  });
})();

