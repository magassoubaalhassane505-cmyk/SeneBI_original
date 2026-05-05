// Header/Footer unique (injecté) — fonctionne en file://
(function () {
  function computeBase() {
    const p = location.pathname.replace(/\\/g, "/").toLowerCase();
    return p.includes("/pages/") ? ".." : ".";
  }

  function headerHtml(base) {
    const isClientSide = base === ".." && !location.pathname.includes("supervision.html") && !location.pathname.includes("visits-control.html");
    const notificationHtml = isClientSide ? `
              <!-- Cloche de notification -->
              <div class="notification-bell" id="notificationBell">
                <svg class="bell-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                  <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
                <span class="notification-badge" id="notificationBadge"></span>
              </div>
              
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
    ` : '';

    return `
      <header class="topbar">
        <div class="topbar-inner">
          <a class="brand" href="/">
            <img class="logo-img" src="/assets/img/logo.png" alt="Logo SeneBI" />
            <div class="brand-title">
              <strong>SeneBI</strong>
              <span>Business Intelligence Agricole</span>
            </div>
          </a>

          <div class="topbar-right">
            <nav class="nav" data-senebi-nav></nav>
            <div class="topbar-actions">
              ${notificationHtml}
              
              <div class="auth-pills" id="authPills" hidden>
                <a class="pill user-pill user-pill--link" id="authUserName" href="/client/mon-compte">Mon compte</a>
                <a class="pill auth-link" id="portalBtn" href="/secure-portal" style="display:none;">Retour au Portail</a>
                <button class="pill auth-logout" id="globalLogoutBtn" type="button">Deconnexion</button>
              </div>
            </div>
          </div>
        </div>
      </header>
    `;
  }

  function footerHtml() {
    return `
      <footer class="site-footer">
        <div class="site-footer-inner">
          <div class="muted small">© 2026 SeneBI - Système Intégré de Gestion Agricole</div>
        </div>
      </footer>
    `;
  }

  document.addEventListener("DOMContentLoaded", function () {
    const base = computeBase();
    const mountHeader = document.querySelector("[data-layout='header']");
    const mountFooter = document.querySelector("[data-layout='footer']");
    if (mountHeader) mountHeader.innerHTML = headerHtml(base);
    if (mountFooter) mountFooter.innerHTML = footerHtml();

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
})();

