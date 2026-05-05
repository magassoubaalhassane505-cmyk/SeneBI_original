// Dashboard Global JavaScript
(function() {
  document.addEventListener("DOMContentLoaded", function() {
    // Check if user is authenticated
    const auth = SeneBI.getAuth();
    if (!auth) {
      window.location.href = "/";
      return;
    }

    // Initialize dashboard
    initializeDashboard();
  });

  function initializeDashboard() {
    // Render the topbar navigation
    SeneBI.renderTopbar();
    
    // Load charts
    loadProductionChart();
    loadAlertsChart();
    
    // Load top farmers table
    loadTopFarmers();
    
    // Initialize map interactions
    initializeMap();
  }

  // Production Chart (Bar Chart)
  function loadProductionChart() {
    const canvas = document.getElementById('productionChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    
    // Destroy existing chart if it exists
    if (window.productionChartInstance) {
      window.productionChartInstance.destroy();
    }

    // Production data for Riz, Maïs, Coton
    const productionData = {
      labels: ['Riz', 'Maïs', 'Coton'],
      datasets: [{
        label: 'Production (tonnes)',
        data: [45000, 38000, 22000],
        backgroundColor: [
          '#16a34a',
          '#16a34a', 
          '#16a34a'
        ],
        borderColor: [
          '#059669',
          '#059669',
          '#059669'
        ],
        borderWidth: 1,
        borderRadius: 4
      }]
    };

    // Create bar chart
    window.productionChartInstance = new Chart(ctx, {
      type: 'bar',
      data: productionData,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            enabled: true,
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleFont: {
              family: 'Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif'
            },
            bodyFont: {
              family: 'Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif'
            },
            callbacks: {
              label: function(context) {
                let label = context.dataset.label || '';
                if (label) {
                  label += ': ';
                }
                label += new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' tonnes';
                return label;
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Tonnes',
              font: {
                family: 'Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif',
                size: 10
              },
              color: '#64748b'
            },
            grid: {
              color: 'rgba(226, 232, 240, 0.5)',
              drawBorder: false
            },
            ticks: {
              font: {
                family: 'Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif',
                size: 10
              },
              color: '#64748b',
              callback: function(value) {
                return new Intl.NumberFormat('fr-FR').format(value);
              }
            }
          },
          x: {
            grid: {
              display: false,
              drawBorder: false
            },
            ticks: {
              font: {
                family: 'Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif',
                size: 11,
                weight: 600
              },
              color: '#0f172a'
            }
          }
        }
      }
    });
  }

  // Alerts Chart (Donut Chart)
  function loadAlertsChart() {
    const canvas = document.getElementById('alertsChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    
    // Destroy existing chart if it exists
    if (window.alertsChartInstance) {
      window.alertsChartInstance.destroy();
    }

    // Alerts data
    const alertsData = {
      labels: ['Manque d\'Urée', 'Retard Pluviométrie', 'Autres'],
      datasets: [{
        data: [60, 30, 10],
        backgroundColor: [
          '#dc2626',
          '#f59e0b',
          '#64748b'
        ],
        borderColor: [
          '#991b1b',
          '#d97706',
          '#475569'
        ],
        borderWidth: 1
      }]
    };

    // Create donut chart
    window.alertsChartInstance = new Chart(ctx, {
      type: 'doughnut',
      data: alertsData,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              font: {
                family: 'Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif',
                size: 11
              },
              usePointStyle: true,
              padding: 15
            }
          },
          tooltip: {
            enabled: true,
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleFont: {
              family: 'Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif'
            },
            bodyFont: {
              family: 'Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif'
            },
            callbacks: {
              label: function(context) {
                let label = context.label || '';
                if (label) {
                  label += ': ';
                }
                label += context.parsed + '%';
                return label;
              }
            }
          }
        },
        cutout: '60%'
      }
    });
  }

  // Load Top Farmers Table
  function loadTopFarmers() {
    const tableBody = document.getElementById('topFarmersBody');
    if (!tableBody) return;

    // Top farmers data
    const topFarmers = [
      {
        rank: 1,
        name: "Ibrahim Bamba",
        location: "Mopti",
        performance: "4.8 t/ha",
        culture: "Riz"
      },
      {
        rank: 2,
        name: "Aminata Touré",
        location: "Sikasso",
        performance: "4.5 t/ha",
        culture: "Maïs"
      },
      {
        rank: 3,
        name: "Bakary Camara",
        location: "Kayes",
        performance: "4.2 t/ha",
        culture: "Coton"
      }
    ];

    tableBody.innerHTML = topFarmers.map(farmer => `
      <div class="table-row">
        <div class="table-rank">${farmer.rank}</div>
        <div class="table-name">${farmer.name}</div>
        <div class="table-location">${farmer.location}</div>
        <div class="table-performance">${farmer.performance}</div>
        <div class="table-culture">${farmer.culture}</div>
      </div>
    `).join('');
  }

  // Initialize Map Interactions
  function initializeMap() {
    const mapRegions = document.querySelectorAll('.map-region');
    
    mapRegions.forEach(region => {
      region.addEventListener('click', function() {
        const regionName = this.dataset.region;
        showRegionDetails(regionName);
      });
      
      region.addEventListener('mouseenter', function() {
        this.style.zIndex = '10';
      });
      
      region.addEventListener('mouseleave', function() {
        this.style.zIndex = '1';
      });
    });
  }

  // Show Region Details (could be expanded)
  function showRegionDetails(regionName) {
    // Simple alert for now - could be expanded to show modal
    const regionNames = {
      'bamako': 'Bamako',
      'sikasso': 'Sikasso',
      'segou': 'Ségou',
      'kayes': 'Kayes',
      'mopti': 'Mopti',
      'tombouctou': 'Tombouctou',
      'gao': 'Gao'
    };
    
    console.log(`Région sélectionnée: ${regionNames[regionName]}`);
  }
})();
