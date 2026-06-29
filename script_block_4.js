
   (function() {
     let previousUnreadCount = 0;
     
     function getEls() {
       return {
         btn: document.getElementById('managerNotifBtn'),
         dropdown: document.getElementById('managerNotifDropdown'),
         badge: document.getElementById('managerNotifBadge'),
         list: document.getElementById('managerNotifList'),
       };
     }

     async function fetchNotifications() {
       try {
         const res = await fetch('/manager/api/notifications?limit=50');
         if (!res.ok) return [];
         const json = await res.json();
         return json.data || [];
       } catch (e) {
         console.warn('Manager notifications load error:', e);
         return [];
       }
     }

     function renderBadge(unread) {
       const { badge } = getEls();
       if (!badge) return;
       
       badge.style.display = unread > 0 ? 'inline-block' : 'none';
       badge.textContent = unread > 99 ? '99+' : unread;
       
       if (unread > previousUnreadCount && unread > 0) {
         badge.classList.remove('notif-badge-pulse');
         void badge.offsetWidth;
         badge.classList.add('notif-badge-pulse');
       }
       
       previousUnreadCount = unread;
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
         await fetch('/manager/api/notifications/read-all', {
           method: 'POST',
           headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
         });
         const items = await fetchNotifications();
         const unread = items.filter(n => !n.read_at).length;
         renderBadge(unread);
         renderList(items);
       } catch (e) {
         console.warn('Mark all read error:', e);
       }
     }

async function refreshBadge() {
       const items = await fetchNotifications();
       const unread = items.filter(n => !n.read_at).length;
       renderBadge(unread);
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
