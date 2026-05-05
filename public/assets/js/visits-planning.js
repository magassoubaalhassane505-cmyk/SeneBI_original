// Visits Planning JavaScript
(function() {
  document.addEventListener("DOMContentLoaded", function() {
    // Check if user is manager
    const auth = window.SeneBI?.requireRole?.(["manager"], "Accès réservé aux managers");
    if (!auth) return;

    // Initialize visits planning
    initializeVisitsPlanning();
  });

  function initializeVisitsPlanning() {
    // Render the topbar navigation
    window.SeneBI?.renderTopbar?.();
    
    // Load existing visits
    loadVisits();
    
    // Load urgent visits
    loadUrgentVisits();
    
    // Set up form submission
    setupFormSubmission();
    
    // Set default datetime to tomorrow 9am
    setDefaultDateTime();
  }

  // Visit data storage
  let visits = [
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

  function loadVisits() {
    console.log('Chargement des visites...');
    const visitsList = document.getElementById('visitsList');
    if (!visitsList) {
      console.log('Element visitsList non trouvé');
      return;
    }
    console.log('Element visitsList trouvé, visites:', visits);

    // Sort visits by date
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
          <div class="visit-content">
            <div class="visit-farmer">${visit.farmer} (${visit.location})</div>
            <div class="visit-reason">${visit.reason}</div>
            <div class="visit-time">${time}</div>
          </div>
        </div>
      `;
    }).join('');

    // If no visits, show empty state
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

  function loadUrgentVisits() {
    console.log('Chargement des visites urgentes...');
    const urgentList = document.getElementById('urgentList');
    if (!urgentList) {
      console.log('Element urgentList non trouvé');
      return;
    }
    console.log('Element urgentList trouvé');

    // Farmers with critical stocks (from supervision data)
    const urgentFarmers = [
      {
        name: "Mamadou Diallo",
        location: "Bamako",
        reason: "Stock Urée critique (15%)",
        action: "Planifier visite"
      },
      {
        name: "Fatoumata Konaté",
        location: "Koulikoro",
        reason: "Stock Urée critique (12%)",
        action: "Planifier visite"
      },
      {
        name: "Mariam Traoré",
        location: "Ségou",
        reason: "Stock Urée critique (10%)",
        action: "Planifier visite"
      }
    ];

    urgentList.innerHTML = urgentFarmers.map(farmer => `
      <div class="urgent-item">
        <div class="urgent-indicator"></div>
        <div class="urgent-info">
          <div class="urgent-farmer">${farmer.name} (${farmer.location})</div>
          <div class="urgent-reason">${farmer.reason}</div>
        </div>
        <a href="#" class="urgent-action" onclick="quickPlanVisit('${farmer.name}', '${farmer.location}')">${farmer.action}</a>
      </div>
    `).join('');
  }

  function setupFormSubmission() {
    const form = document.getElementById('visitForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const farmerSelect = document.getElementById('farmerSelect');
      const dateTime = document.getElementById('dateTime');
      const reason = document.getElementById('reason');

      // Get farmer name from select option
      const farmerOption = farmerSelect.options[farmerSelect.selectedIndex];
      const farmerName = farmerOption.text.split(' (')[0];

      // Create new visit
      const newVisit = {
        id: visits.length + 1,
        farmer: farmerName,
        location: farmerOption.text.match(/\(([^)]+)\)/)[1],
        date: new Date(dateTime.value),
        reason: reason.options[reason.selectedIndex].text,
        status: "planned"
      };

      // Add to visits array
      visits.push(newVisit);

      // Reload visits list
      loadVisits();

      // Show success message
      showSuccessMessage('Visite planifiée avec succès !');

      // Reset form
      form.reset();
      setDefaultDateTime();
    });
  }

  function setDefaultDateTime() {
    const dateTimeInput = document.getElementById('dateTime');
    if (!dateTimeInput) return;

    // Set to tomorrow at 9:00 AM
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    tomorrow.setHours(9, 0, 0, 0);

    const dateStr = tomorrow.toISOString().slice(0, 16);
    dateTimeInput.value = dateStr;
  }

  function showSuccessMessage(message) {
    // Create success notification with icon
    const notification = document.createElement('div');
    notification.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: linear-gradient(135deg, var(--accent), #047857);
      color: white;
      padding: 16px 20px;
      border-radius: 12px;
      font-weight: 700;
      font-size: 14px;
      z-index: 1000;
      box-shadow: 0 8px 25px rgba(5, 150, 105, 0.4);
      transform: translateX(120%) scale(0.8);
      transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
      display: flex;
      align-items: center;
      gap: 12px;
      max-width: 320px;
    `;
    
    // Add checkmark icon
    notification.innerHTML = `
      <div style="
        width: 24px;
        height: 24px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
      ">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
      </div>
      <span>${message}</span>
    `;

    document.body.appendChild(notification);

    // Animate in with bounce effect
    setTimeout(() => {
      notification.style.transform = 'translateX(0) scale(1)';
    }, 100);

    // Add subtle pulse effect
    setTimeout(() => {
      notification.style.transform = 'translateX(0) scale(1.05)';
      setTimeout(() => {
        notification.style.transform = 'translateX(0) scale(1)';
      }, 200);
    }, 600);

    // Remove with slide out effect
    setTimeout(() => {
      notification.style.transform = 'translateX(120%) scale(0.8)';
      notification.style.opacity = '0';
      setTimeout(() => {
        if (document.body.contains(notification)) {
          document.body.removeChild(notification);
        }
      }, 400);
    }, 3500);
  }

  // Quick plan visit function for urgent farmers
  window.quickPlanVisit = function(farmerName, location) {
    const farmerSelect = document.getElementById('farmerSelect');
    const reasonSelect = document.getElementById('reason');

    // Find and select the farmer with exact match
    let farmerFound = false;
    for (let i = 0; i < farmerSelect.options.length; i++) {
      const option = farmerSelect.options[i];
      if (option.text.includes(farmerName) && option.text.includes(location)) {
        farmerSelect.selectedIndex = i;
        farmerFound = true;
        break;
      }
    }

    // If exact match not found, try with name only
    if (!farmerFound) {
      for (let i = 0; i < farmerSelect.options.length; i++) {
        const option = farmerSelect.options[i];
        if (option.text.includes(farmerName)) {
          farmerSelect.selectedIndex = i;
          break;
        }
      }
    }

    // Set reason to stock control based on the urgent reason
    for (let i = 0; i < reasonSelect.options.length; i++) {
      const option = reasonSelect.options[i];
      if (option.value.includes('controle-stock')) {
        reasonSelect.selectedIndex = i;
        break;
      }
    }

    // Set default date time
    setDefaultDateTime();

    // Scroll to form with smooth animation
    const form = document.getElementById('visitForm');
    form.scrollIntoView({ behavior: 'smooth', block: 'center' });

    // Highlight form briefly with green glow
    form.style.transition = 'box-shadow 0.3s ease';
    form.style.boxShadow = '0 0 0 3px rgba(5, 150, 105, 0.3)';
    
    // Add a subtle pulse effect to the farmer select
    farmerSelect.style.transition = 'all 0.3s ease';
    farmerSelect.style.backgroundColor = 'rgba(5, 150, 105, 0.1)';
    farmerSelect.style.borderColor = 'var(--accent)';
    
    setTimeout(() => {
      form.style.boxShadow = '';
      farmerSelect.style.backgroundColor = '';
      farmerSelect.style.borderColor = '';
    }, 2000);

    // Focus on the reason field for better UX
    setTimeout(() => {
      reasonSelect.focus();
    }, 500);
  };
})();
