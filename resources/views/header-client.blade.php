<header class="topbar">
  @php($u = auth()->user())
  <script>
    window.__SENEBI_AUTH__ = {{ \Illuminate\Support\Js::from([
      'id' => optional($u)->id,
      'name' => optional($u)->name,
      'email' => optional($u)->email,
      'company' => optional($u)->company,
      'role' => optional($u)->role,
    ]) }};
  </script>
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
        <a href="{{ url('/client/dashboard') }}" class="{{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 13h8V3H3v10z"/>
            <path d="M13 21h8V11h-8v10z"/>
            <path d="M13 3h8v6h-8V3z"/>
            <path d="M3 17h8v4H3v-4z"/>
          </svg>
          <span>Dashboard</span>
        </a>
        <a href="{{ url('/client/parcelles') }}" class="{{ request()->routeIs('client.parcelles') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 21s7-4.5 7-11a7 7 0 0 0-14 0c0 6.5 7 11 7 11z"/>
            <path d="M12 10a2 2 0 1 0 0-4 2 2 0 0 0 4z"/>
          </svg>
          <span>Parcelles</span>
        </a>
        <a href="{{ url('/client/rentabilite') }}" class="{{ request()->routeIs('client.rentabilite') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
            <path d="M12 7v10"/>
            <path d="M9.5 9.5c.6-1 4.4-1 5 0"/>
            <path d="M9.5 14.5c.6 1 4.4 1 5 0"/>
          </svg>
          <span>Rentabilité</span>
        </a>
        <a href="{{ url('/client/stocks') }}" class="{{ request()->routeIs('client.stocks') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
          <span>Stocks</span>
        </a>
      </nav>

      <!-- Partie Droite : Actions -->
      <div class="topbar-actions">
        <div style="position: relative; margin-right: 16px;">
          <button id="clientNotifBtn" class="icon-btn" aria-label="Notifications" style="position: relative; background: none; border: none; cursor: pointer; padding: 8px; color: inherit;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
              <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            <span id="notifBadge" style="position: absolute; top: 2px; right: 2px; background: #ef4444; color: white; border-radius: 999px; font-size: 11px; font-weight: 700; padding: 2px 6px; display: none;">0</span>
          </button>
          <div id="notifDropdown" class="notif-dropdown hidden">
            <div class="notif-header">
              <span class="notif-header-title">Notifications</span>
              <div class="notif-header-actions">
                <button class="notif-action-link" id="markAllReadBtn">Tout marquer comme lu</button>
                <a href="{{ url('/client/notifications') }}" class="notif-action-link">Tout voir</a>
              </div>
            </div>
            <div id="notifList" class="notif-list"></div>
          </div>
        </div>

        <div class="auth-pills">
          <a class="pill user-pill" href="{{ url('/client/mon-compte') }}">{{ optional($u)->name ?? 'Mon compte' }}</a>
          <form action="{{ route('logout') }}" method="POST" style="display:inline-flex; margin:0;">
            @csrf
            <button type="submit" class="pill auth-logout">Déconnexion</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <style>
    .client-nav a.active {
      background: #dcfce7;
      color: #14532d;
      font-weight: 600;
      border-left: 3px solid #10b981;
      border-radius: 0 8px 8px 0;
      transition: all 0.2s ease;
    }

    .client-nav a.active:hover {
      background: #bbf7d0;
      border-left-color: #059669;
    }

    .notif-dropdown {
      display: none;
      position: absolute;
      right: 0;
      top: 110%;
      width: 360px;
      background: #fff;
      color: #111827;
      border-radius: 16px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.12), 0 0 0 1px rgba(15,23,42,0.06);
      z-index: 1000;
      max-height: 480px;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      animation: notifSlideIn 0.2s ease;
    }

    @keyframes notifSlideIn {
      from { opacity: 0; transform: translateY(-8px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .notif-dropdown.hidden { display: none; }
    .notif-dropdown.visible { display: flex; }

    .notif-header {
      padding: 14px 16px;
      border-bottom: 1px solid #e5e7eb;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #fff;
      flex-shrink: 0;
    }

    .notif-header-title {
      font-weight: 800;
      font-size: 14px;
      color: #111827;
    }

    .notif-header-actions {
      display: flex;
      gap: 8px;
    }

    .notif-action-link {
      font-size: 12px;
      font-weight: 700;
      color: #10b981;
      text-decoration: none;
      cursor: pointer;
      border: none;
      background: none;
      padding: 4px 8px;
      border-radius: 6px;
      transition: background 0.15s ease;
    }

    .notif-action-link:hover {
      background: #dcfce7;
    }

    .notif-list {
      flex: 1;
      overflow-y: auto;
      padding: 0;
    }

    .notif-list::-webkit-scrollbar {
      width: 5px;
    }

    .notif-list::-webkit-scrollbar-track {
      background: transparent;
    }

    .notif-list::-webkit-scrollbar-thumb {
      background: #d1d5db;
      border-radius: 10px;
    }

    .notif-section-label {
      padding: 10px 16px 4px;
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #9ca3af;
      background: #f9fafb;
      border-bottom: 1px solid #f3f4f6;
      position: sticky;
      top: 0;
      z-index: 1;
    }

    .notif-item {
      padding: 12px 16px;
      border-bottom: 1px solid #f3f4f6;
      font-size: 13px;
      display: flex;
      gap: 10px;
      align-items: flex-start;
      transition: background 0.15s ease;
      cursor: pointer;
    }

    .notif-item:hover {
      background: #f9fafb;
    }

    .notif-item:last-child {
      border-bottom: none;
    }

    .notif-item.unread {
      background: #eff6ff;
      border-left: 3px solid #3b82f6;
    }

    .notif-item.unread:hover {
      background: #dbeafe;
    }

    .notif-icon {
      width: 32px;
      height: 32px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 13px;
      flex-shrink: 0;
    }

    .notif-icon.danger { background: #fef2f2; color: #991b1b; }
    .notif-icon.warning { background: #fffbeb; color: #92400e; }
    .notif-icon.success { background: #f0fdf4; color: #166534; }
    .notif-icon.info { background: #eff6ff; color: #1e40af; }
    .notif-icon.system { background: #f1f5f9; color: #475569; }

    .notif-content {
      flex: 1;
      min-width: 0;
    }

    .notif-title {
      font-weight: 700;
      font-size: 13px;
      color: #111827;
      margin-bottom: 1px;
      line-height: 1.3;
    }

    .notif-message {
      font-size: 12px;
      color: #4b5563;
      line-height: 1.4;
      margin-bottom: 4px;
    }

    .notif-meta {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }

    .notif-time {
      font-size: 11px;
      color: #9ca3af;
    }

    .notif-badge {
      display: inline-flex;
      padding: 2px 7px;
      border-radius: 999px;
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }

    .notif-badge.danger { background: #fef2f2; color: #991b1b; }
    .notif-badge.warning { background: #fffbeb; color: #92400e; }
    .notif-badge.success { background: #f0fdf4; color: #166534; }
    .notif-badge.info { background: #eff6ff; color: #1e40af; }
    .notif-badge.system { background: #f1f5f9; color: #475569; }

    .notif-actions {
      display: flex;
      gap: 4px;
      margin-top: 6px;
    }

    .notif-quick-action {
      font-size: 11px;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 6px;
      border: 1px solid #e5e7eb;
      background: #fff;
      color: #374151;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 4px;
      transition: all 0.15s ease;
    }

    .notif-quick-action:hover {
      background: #f3f4f6;
      border-color: #d1d5db;
    }

    .notif-footer {
      padding: 10px 16px;
      border-top: 1px solid #e5e7eb;
      text-align: center;
      background: #fff;
      flex-shrink: 0;
    }

    .notif-footer a {
      font-size: 13px;
      font-weight: 700;
      color: #10b981;
      text-decoration: none;
    }

    .notif-footer a:hover {
      text-decoration: underline;
    }

    .notif-empty {
      padding: 32px 16px;
      text-align: center;
      color: #9ca3af;
      font-size: 13px;
    }

    .notif-badge-pulse {
      animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }
  </style>
</header>

<script>
  (function() {
    function getEls() {
      return {
        btn: document.getElementById('clientNotifBtn'),
        dropdown: document.getElementById('notifDropdown'),
        badge: document.getElementById('notifBadge'),
        list: document.getElementById('notifList'),
      };
    }

    async function fetchNotifications() {
      try {
        const res = await fetch('/client/api/notifications?limit=50');
        if (!res.ok) return [];
        const json = await res.json();
        return json.data || [];
      } catch (e) {
        console.warn('Notifications load error:', e);
        return [];
      }
    }

    function renderBadge(items) {
      const { badge } = getEls();
      if (!badge) return;
      const unread = items.filter(n => !n.read_at).length;
      if (unread > 0) {
        badge.style.display = 'inline-block';
        badge.textContent = unread > 99 ? '99+' : unread;
        badge.classList.add('notif-badge-pulse');
      } else {
        badge.style.display = 'none';
        badge.classList.remove('notif-badge-pulse');
      }
    }

    function groupNotifications(items) {
      const groups = [];
      const seen = new Set();
      items.forEach(n => {
        const key = `${n.type}-${n.title}`;
        if (seen.has(key)) {
          const last = groups[groups.length - 1];
          if (last && last.key === key) {
            last.count++;
            return;
          }
        }
        seen.add(key);
        groups.push({
          key,
          type: n.type,
          title: n.title,
          message: n.message,
          time: n.created_at,
          read_at: n.read_at,
          count: 1,
          icon: n.icon,
          level: n.level,
          action_url: n.action_url,
        });
      });
      return groups;
    }

    function renderList(items) {
      const { list } = getEls();
      if (!list) return;
      if (items.length === 0) {
        list.innerHTML = '<div class="notif-empty"><i class="fas fa-bell-slash" style="font-size:24px;display:block;margin-bottom:8px;color:#d1d5db"></i>Aucune notification</div>';
        return;
      }

      const grouped = groupNotifications(items);

      const unreadItems = grouped.filter(g => !g.read_at);
      const readItems = grouped.filter(g => g.read_at);

      function renderSection(title, items) {
        if (items.length === 0) return '';
        const rows = items.map(g => {
          const iconClass = g.level === 'danger' ? 'danger' : g.level === 'warning' ? 'warning' : g.level === 'success' ? 'success' : g.level === 'system' ? 'system' : 'info';
          const time = g.time ? new Date(g.time).toLocaleString('fr-FR', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' }) : '';
          const groupLabel = g.count > 1 ? `<div style="font-size:11px;color:#6b7280;margin-top:2px">${g.count} notifications similaires</div>` : '';
          let actionsHtml = '';
          if (g.action_url) {
            const label = g.action_url.includes('stocks') ? 'Voir le stock' :
                          g.action_url.includes('parcelles') ? 'Voir la parcelle' :
                          g.action_url.includes('supervision') ? 'Examiner la demande' :
                          g.action_url.includes('visites') ? 'Planifier une visite' : 'Voir';
            actionsHtml = `<a href="${g.action_url}" class="notif-quick-action"><i class="fas fa-arrow-right"></i> ${label}</a>`;
          }
          return `
            <div class="notif-item ${g.read_at ? '' : 'unread'}" data-id="${g.key}">
              <div class="notif-icon ${iconClass}">
                <i class="fas ${g.icon || 'fa-bell'}"></i>
              </div>
              <div class="notif-content">
                <div class="notif-title">${g.title}${g.count > 1 ? ` <span style="font-weight:500;color:#6b7280">(${g.count})</span>` : ''}</div>
                <div class="notif-message">${g.message}</div>
                ${groupLabel}
                <div class="notif-meta">
                  <span class="notif-time">${time}</span>
                  <span class="notif-badge ${iconClass}">${g.level || 'info'}</span>
                </div>
                ${actionsHtml ? `<div class="notif-actions">${actionsHtml}</div>` : ''}
              </div>
            </div>
          `;
        }).join('');
        return `<div class="notif-section-label">${title} (${items.length})</div>${rows}`;
      }

      const html = (unreadItems.length > 0 ? renderSection('Non lues', unreadItems) : '') +
                   (readItems.length > 0 ? renderSection('Lues', readItems) : '');

      list.innerHTML = html || '<div class="notif-empty"><i class="fas fa-check-circle" style="font-size:24px;display:block;margin-bottom:8px;color:#10b981"></i>Toutes les notifications sont lues</div>';
    }

    async function markAllAsRead() {
      try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        await fetch('/client/api/notifications/read-all', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        });
        const items = await fetchNotifications();
        renderBadge(items);
        renderList(items);
      } catch (e) {
        console.warn('Mark all read error:', e);
      }
    }

    async function refreshBadge() {
      const items = await fetchNotifications();
      renderBadge(items);
      return items;
    }

    async function openDropdown() {
      const { dropdown, list } = getEls();
      if (!dropdown) return;
      dropdown.classList.add('visible');
      dropdown.classList.remove('hidden');
      list.innerHTML = '<div class="notif-empty"><i class="fas fa-spinner fa-spin" style="font-size:20px;display:block;margin-bottom:8px;color:#d1d5db"></i>Chargement...</div>';
      const items = await fetchNotifications();
      renderList(items);
    }

    function closeDropdown() {
      const { dropdown } = getEls();
      if (dropdown) {
        dropdown.classList.remove('visible');
        dropdown.classList.add('hidden');
      }
    }

    function init() {
      const { btn } = getEls();
      if (!btn) return;

      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const { dropdown } = getEls();
        if (dropdown && dropdown.classList.contains('visible')) {
          closeDropdown();
        } else {
          openDropdown();
        }
      });

      document.addEventListener('click', function(e) {
        const { dropdown, btn } = getEls();
        if (dropdown && !dropdown.contains(e.target) && e.target !== btn) {
          closeDropdown();
        }
      });

      refreshBadge();
      setInterval(refreshBadge, 30000);
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', init);
    } else {
      init();
    }
  })();
</script>