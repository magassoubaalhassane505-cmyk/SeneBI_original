<header class="topbar">
  <div class="topbar-inner">
    <!-- Partie Gauche : Logo SeneBI -->
    <a class="brand" href="{{ route('manager.dashboard') }}">
      <img class="logo-img" src="{{ asset('assets/img/logo.png') }}" alt="Logo SeneBI" />
      <div class="brand-title">
        <strong>SeneBI</strong>
        <span>Business Intelligence Agricole</span>
      </div>
    </a>

    <div class="topbar-right">
      <!-- Partie Centrale : Navigation -->
      <nav class="nav manager-nav">
        <a href="{{ route('manager.dashboard') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 13h8V3H3v10z"/>
            <path d="M13 21h8V11h-8v10z"/>
            <path d="M13 3h8v6h-8V3z"/>
            <path d="M3 17h8v4H3v-4z"/>
          </svg>
          <span>Dashboard</span>
        </a>
        <a href="{{ route('manager.supervision') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
          </svg>
          <span>Supervision</span>
        </a>
        <a href="{{ route('manager.visites') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
          <span>Visites</span>
        </a>
        <a href="{{ route('manager.catalogue') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
            <path d="M12 22V8"/>
            <path d="M4.93 4.93L12 12l7.07-7.07"/>
          </svg>
          <span>Catalogue</span>
        </a>
      </nav>

      <!-- Partie Droite : Actions -->
      <div class="topbar-actions">
        <!-- Cloche de notification -->
        <div class="notification-bell" id="notificationBell">
          <svg class="bell-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
          </svg>
          <span class="notification-badge" id="notificationBadge">2</span>
          
          <!-- Menu dropdown notifications -->
          <div class="notification-dropdown" id="notificationDropdown">
            <div class="notification-content">
              <div class="notification-item">
                <span class="notification-icon">⚠️</span>
                <div class="notification-text">
                  <strong>Stock Critique</strong>
                  <p>Il ne reste que 2 sacs d'engrais NPK. Pensez à passer commande.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="auth-pills">
          <a class="pill user-pill" href="{{ route('manager.dashboard') }}">Manager</a>
          <form action="{{ route('logout') }}" method="POST" style="display:inline-flex; margin:0;">
            @csrf
            <button type="submit" class="pill auth-logout">Déconnexion</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</header>

<!-- Styles pour la cloche de notification -->
<style>
.notification-bell {
  position: relative;
  cursor: pointer;
  margin-right: 16px;
  padding: 8px;
  border: 2px solid #10b981;
  border-radius: 50%;
  transition: all 0.2s ease;
}

.notification-bell:hover {
  border-color: #059669;
  box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
}

.bell-icon {
  color: #10b981;
  transition: color 0.2s ease;
}

.notification-bell:hover .bell-icon {
  color: #059669;
}

.notification-badge {
  position: absolute;
  top: -4px;
  right: -4px;
  background: #ef4444;
  color: white;
  border-radius: 50%;
  width: 16px;
  height: 16px;
  font-size: 10px;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
}

.notification-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  margin-top: 8px;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  min-width: 320px;
  z-index: 1000;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px);
  transition: all 0.3s ease;
}

.notification-dropdown.show {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.notification-content {
  padding: 16px;
}

.notification-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 12px 0;
}

.notification-icon {
  font-size: 16px;
  flex-shrink: 0;
}

.notification-text strong {
  color: #1e293b;
  font-size: 14px;
  display: block;
  margin-bottom: 4px;
}

.notification-text p {
  color: #64748b;
  font-size: 13px;
  margin: 0;
  line-height: 1.4;
}

/* Styles pour la navigation active */
.manager-nav a.active {
  background: #dcfce7;
  color: #14532d;
  font-weight: 600;
  border-left: 3px solid #10b981;
  border-radius: 0 8px 8px 0;
  transition: all 0.2s ease;
}

.manager-nav a.active:hover {
  background: #bbf7d0;
  border-left-color: #059669;
}
</style>

<!-- JavaScript pour la navigation active et notifications -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Détecter la page active dans la navigation
  const navLinks = document.querySelectorAll('.manager-nav a');
  const currentPath = window.location.pathname;

  navLinks.forEach(link => {
    const linkPath = new URL(link.href, window.location.origin).pathname;
    if (currentPath === linkPath) {
      link.classList.add('active');
    }
  });
  
  // Gestion de la cloche de notification
  const notificationBell = document.getElementById('notificationBell');
  const notificationDropdown = document.getElementById('notificationDropdown');

  if (notificationBell && notificationDropdown) {
    // Au clic sur la cloche
    notificationBell.addEventListener('click', function(e) {
      e.stopPropagation();
      notificationDropdown.classList.toggle('show');
    });

    // Fermeture au clic ailleurs
    document.addEventListener('click', function(e) {
      if (!notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
        notificationDropdown.classList.remove('show');
      }
    });

    // Fermeture au clic sur le dropdown lui-même
    notificationDropdown.addEventListener('click', function(e) {
      e.stopPropagation();
    });
  }
});
</script>