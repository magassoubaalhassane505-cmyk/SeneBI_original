// Centre de Contrôle des Visites - JavaScript
(function() {
  'use strict';

  // Données de visites pour la page de planning
  const visits = [
    {
      id: 1,
      farmer: "Mamadou Diallo",
      location: "Bamako",
      date: new Date(Date.now() + 2 * 24 * 60 * 60 * 1000), // 2 days from now
      reason: "Contrôle stock Urée",
      status: "planned"
    },
    {
      id: 2,
      farmer: "Aminata Touré",
      location: "Sikasso",
      date: new Date(Date.now() + 4 * 24 * 60 * 60 * 1000), // 4 days from now
      reason: "Alerte rendement Riz",
      status: "planned"
    },
    {
      id: 3,
      farmer: "Bakary Camara",
      location: "Kayes",
      date: new Date(Date.now() + 6 * 24 * 60 * 60 * 1000), // 6 days from now
      reason: "Conseil semis Coton",
      status: "planned"
    }
  ];

  // Charger les visites planifiées
  function loadVisits() {
    const visitsList = document.getElementById('visitsList');
    if (!visitsList) return;

    // Trier les visites par date
    const sortedVisits = [...visits].sort((a, b) => a.date - b.date);

    visitsList.innerHTML = sortedVisits.map(visit => {
      const date = visit.date;
      const day = date.getDate();
      const month = date.toLocaleDateString('fr-FR', { month: 'short' });
      const time = date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });

      return `
        <div class="visit-item">
          <div class="visit-date">
            <div class="day">${day}</div>
            <div class="month">${month}</div>
          </div>
          <div class="visit-details">
            <div class="visit-farmer">${visit.farmer}</div>
            <div class="visit-location">${visit.location}</div>
            <div class="visit-time">${time}</div>
            <div class="visit-reason">${visit.reason}</div>
          </div>
          <div class="visit-status">
            <span class="status-tag planned">Planifié</span>
          </div>
        </div>
      `;
    }).join('');

    // Si aucune visite, afficher l'état vide
    if (visits.length === 0) {
      visitsList.innerHTML = `
        <div style="text-align: center; padding: 40px; color: var(--muted);">
          <div style="font-size: 48px; margin-bottom: 16px;">📅</div>
          <p>Aucune visite planifiée</p>
          <p style="font-size: 12px;">Utilisez le formulaire pour planifier votre première visite</p>
        </div>
      `;
    }
  }

  // Charger les visites urgentes
  function loadUrgentVisits() {
    const urgentList = document.getElementById('urgentList');
    if (!urgentList) return;

    // Agriculteurs avec stocks critiques
    const urgentFarmers = [
      {
        name: "Mamadou Diallo",
        location: "Bamako",
        reason: "Stock Urée critique (15%)",
        action: "Planifier visite"
      },
      {
        name: "Aminata Touré",
        location: "Sikasso",
        reason: "Alerte rendement Riz",
        action: "Planifier visite"
      },
      {
        name: "Bakary Camara",
        location: "Kayes",
        reason: "Conseil semis Coton urgent",
        action: "Planifier visite"
      }
    ];

    urgentList.innerHTML = urgentFarmers.map(farmer => `
      <div class="urgent-item">
        <div class="urgent-indicator"></div>
        <div class="urgent-info">
          <div class="urgent-name">${farmer.name}</div>
          <div class="urgent-location">${farmer.location}</div>
          <div class="urgent-reason">${farmer.reason}</div>
        </div>
        <button class="btn btn-small btn-danger" onclick="planUrgentVisit('${farmer.name}', '${farmer.location}')">
          ${farmer.action}
        </button>
      </div>
    `).join('');
  }

  // Planifier une visite urgente
  function planUrgentVisit(farmerName, location) {
    const farmerSelect = document.getElementById('farmerSelect');
    const dateTime = document.getElementById('dateTime');
    const reason = document.getElementById('reason');
    
    if (farmerSelect) {
      // Sélectionner l'agriculteur
      const options = farmerSelect.options;
      for (let i = 0; i < options.length; i++) {
        if (options[i].text.includes(farmerName)) {
          farmerSelect.value = options[i].value;
          break;
        }
      }
    }
    
    if (dateTime) {
      // Définir demain à 9h
      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 1);
      tomorrow.setHours(9, 0, 0, 0);
      dateTime.value = tomorrow.toISOString().slice(0, 16);
    }
    
    // Scroller vers le formulaire
    const form = document.getElementById('visitForm');
    if (form) {
      form.scrollIntoView({ behavior: 'smooth' });
    }
  }

  // Initialiser la page
  function init() {
    // Charger les données sans vérification d'authentification
    loadVisits();
    loadUrgentVisits();
    
    // Configurer le formulaire
    setupForm();
    
    // Définir la date/heure par défaut (demain 9h)
    setDefaultDateTime();
  }
  
  // Configurer le formulaire de visite
  function setupForm() {
    const form = document.getElementById('visitForm');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const farmerSelect = document.getElementById('farmerSelect');
      const dateTime = document.getElementById('dateTime');
      const reason = document.getElementById('reason');
      
      if (!farmerSelect.value || !dateTime.value || !reason.value) {
        alert('Veuillez remplir tous les champs');
        return;
      }
      
      // Ajouter la nouvelle visite
      const newVisit = {
        id: visits.length + 1,
        farmer: farmerSelect.options[farmerSelect.selectedIndex].text.split(' (')[0],
        location: farmerSelect.options[farmerSelect.selectedIndex].text.split(' (')[1].replace(')', ''),
        date: new Date(dateTime.value),
        reason: reason.options[reason.selectedIndex].text,
        status: "planned"
      };
      
      visits.push(newVisit);
      
      // Recharger la liste
      loadVisits();
      
      // Réinitialiser le formulaire
      form.reset();
      setDefaultDateTime();
      
      // Afficher un message de succès
      alert('Visite planifiée avec succès !');
    });
  }
  
  // Définir la date/heure par défaut (demain 9h)
  function setDefaultDateTime() {
    const dateTime = document.getElementById('dateTime');
    if (dateTime) {
      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 1);
      tomorrow.setHours(9, 0, 0, 0);
      dateTime.value = tomorrow.toISOString().slice(0, 16);
    }
  }

  // Démarrer quand le DOM est prêt
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
