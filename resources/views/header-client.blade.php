<header class="topbar">
  <div class="topbar-inner">
    <!-- Partie Gauche : Logo SeneBI -->
    <a class="brand" href="{{ url('/client/dashboard') }}">
      <img class="logo-img" src="{{ asset('assets/img/logo.png') }}" alt="Logo SeneBI" />
      <div class="brand-title">
        <strong>SeneBI</strong>
        <span>Business Intelligence Agricole</span>
      </div>
    </a>

    <div class="topbar-right">
      <!-- Partie Centrale : Navigation -->
      <nav class="nav client-nav">
        <a href="{{ url('/client/dashboard') }}" class="active">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 13h8V3H3v10z"/>
            <path d="M13 21h8V11h-8v10z"/>
            <path d="M13 3h8v6h-8V3z"/>
            <path d="M3 17h8v4H3v-4z"/>
          </svg>
          <span>Dashboard</span>
        </a>
        <a href="{{ url('/client/parcelles') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 21s7-4.5 7-11a7 7 0 0 0-14 0c0 6.5 7 11 7 11z"/>
            <path d="M12 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
          </svg>
          <span>Parcelles</span>
        </a>
        <a href="{{ url('/client/rentabilite') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
            <path d="M12 7v10"/>
            <path d="M9.5 9.5c.6-1 4.4-1 5 0"/>
            <path d="M9.5 14.5c.6 1 4.4 1 5 0"/>
          </svg>
          <span>Rentabilité</span>
        </a>
        <a href="{{ url('/manager/stocks') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
          <span>Stocks</span>
        </a>
      </nav>

      <!-- Partie Droite : Actions -->
      <div class="topbar-actions">
        <div class="auth-pills">
          <a class="pill user-pill">Client</a>
          <a class="pill auth-logout" href="{{ url('/logout') }}">Déconnexion</a>
        </div>
      </div>
    </div>
  </div>
</header>