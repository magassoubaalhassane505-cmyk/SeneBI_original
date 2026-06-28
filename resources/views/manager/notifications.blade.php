<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Notifications - SeneBI Manager</title>
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
      .notif-page-grid {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 20px;
        margin-top: 18px;
      }
      .notif-sidebar {
        background: #fff;
        border: 1px solid rgba(15,23,42,0.08);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 16px;
        height: fit-content;
        position: sticky;
        top: 80px;
      }
      .notif-sidebar h3 {
        margin: 0 0 12px;
        font-size: 14px;
        font-weight: 800;
        color: #111827;
      }
      .notif-filter-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid transparent;
        background: transparent;
        color: #374151;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.15s ease;
        text-align: left;
      }
      .notif-filter-btn:hover {
        background: #f9fafb;
      }
      .notif-filter-btn.active {
        background: #dcfce7;
        color: #14532d;
        border-color: rgba(22,163,74,0.25);
      }
      .notif-filter-btn i {
        width: 18px;
        text-align: center;
        font-size: 13px;
      }
      .notif-main {
        background: #fff;
        border: 1px solid rgba(15,23,42,0.08);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 16px;
      }
      .notif-main-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
        flex-wrap: wrap;
        gap: 10px;
      }
      .notif-main-header h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        color: #111827;
      }
      .notif-search {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 8px 12px;
        width: 280px;
      }
      .notif-search i {
        color: #9ca3af;
        font-size: 13px;
      }
      .notif-search input {
        border: none;
        background: transparent;
        outline: none;
        font-size: 13px;
        color: #111827;
        width: 100%;
      }
      .notif-card {
        display: flex;
        gap: 12px;
        padding: 14px;
        border-radius: 12px;
        border: 1px solid #f3f4f6;
        margin-bottom: 10px;
        transition: all 0.2s ease;
        background: #fff;
      }
      .notif-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transform: translateY(-1px);
      }
      .notif-card.unread {
        background: #eff6ff;
        border-left: 3px solid #3b82f6;
      }
      .notif-card-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
      }
      .notif-card-icon.danger { background: #fef2f2; color: #991b1b; }
      .notif-card-icon.warning { background: #fffbeb; color: #92400e; }
      .notif-card-icon.success { background: #f0fdf4; color: #166534; }
      .notif-card-icon.info { background: #eff6ff; color: #1e40af; }
      .notif-card-icon.system { background: #f1f5f9; color: #475569; }
      .notif-card-body {
        flex: 1;
        min-width: 0;
      }
      .notif-card-title {
        font-weight: 700;
        font-size: 14px;
        color: #111827;
        margin-bottom: 2px;
      }
      .notif-card-message {
        font-size: 13px;
        color: #4b5563;
        line-height: 1.5;
        margin-bottom: 6px;
      }
      .notif-card-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
      }
      .notif-card-time {
        font-size: 11px;
        color: #9ca3af;
      }
      .notif-card-badge {
        display: inline-flex;
        padding: 3px 8px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
      }
      .notif-card-badge.danger { background: #fef2f2; color: #991b1b; }
      .notif-card-badge.warning { background: #fffbeb; color: #92400e; }
      .notif-card-badge.success { background: #f0fdf4; color: #166534; }
      .notif-card-badge.info { background: #eff6ff; color: #1e40af; }
      .notif-card-badge.system { background: #f1f5f9; color: #475569; }
      .notif-card-actions {
        display: flex;
        gap: 6px;
        margin-top: 8px;
      }
      .notif-card-btn {
        font-size: 12px;
        font-weight: 700;
        padding: 5px 10px;
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
      .notif-card-btn:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
      }
      .notif-card-btn.danger {
        color: #991b1b;
        border-color: #fecaca;
        background: #fef2f2;
      }
      .notif-card-btn.danger:hover {
        background: #fee2e2;
      }
      @media (max-width: 768px) {
        .notif-page-grid {
          grid-template-columns: 1fr;
        }
        .notif-sidebar {
          position: static;
        }
      }
    </style>
  </head>
  <body data-page="manager-notifications">
    @include('header-manager')

    <main class="container">
      <div class="page-title">
        <div>
          <h1>Notifications</h1>
          <p>Gérez toutes vos notifications en un seul endroit</p>
        </div>
        <div class="head-actions">
          <button class="btn" onclick="markAllAsRead()">Tout marquer comme lu</button>
        </div>
      </div>

      <div class="notif-page-grid">
        <aside class="notif-sidebar">
          <h3>Filtres</h3>
          <div style="display:flex; flex-direction:column; gap:4px;">
            <button class="notif-filter-btn active" data-filter="all"><i class="fas fa-layer-group"></i> Toutes</button>
            <button class="notif-filter-btn" data-filter="stock"><i class="fas fa-boxes"></i> Stocks</button>
            <button class="notif-filter-btn" data-filter="parcelle"><i class="fas fa-map-marked-alt"></i> Parcelles</button>
            <button class="notif-filter-btn" data-filter="visite"><i class="fas fa-calendar-check"></i> Visites</button>
            <button class="notif-filter-btn" data-filter="system"><i class="fas fa-cog"></i> Système</button>
          </div>
        </aside>

        <div class="notif-main">
          <div class="notif-main-header">
            <h2>Toutes les notifications</h2>
            <div class="notif-search">
              <i class="fas fa-search"></i>
              <input type="text" id="searchNotifications" placeholder="Rechercher..." />
            </div>
          </div>

          <div id="notificationsContainer">
            @php
              $allNotifications = \App\Models\Notification::where('user_id', auth()->id())->latest()->get();
              $unreadNotifications = $allNotifications->where('read_at', null);
              $readNotifications = $allNotifications->where('read_at', '!=', null);
            @endphp

            @if($unreadNotifications->count() > 0)
              <div style="font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; color:#9ca3af; margin-bottom:10px;">Non lues ({{ $unreadNotifications->count() }})</div>
              @foreach($unreadNotifications as $notification)
                <div class="notif-card unread" data-type="{{ $notification->type ?? 'system' }}">
                  <div class="notif-card-icon {{ $notification->level ?? 'info' }}">
                    <i class="fas {{ $notification->icon ?? 'fa-bell' }}"></i>
                  </div>
                  <div class="notif-card-body">
                    <div class="notif-card-title">{{ $notification->title }}</div>
                    <div class="notif-card-message">{{ $notification->message }}</div>
                    <div class="notif-card-meta">
                      <span class="notif-card-time">{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                      <span class="notif-card-badge {{ $notification->level ?? 'info' }}">{{ $notification->type ?? 'info' }}</span>
                    </div>
                    <div class="notif-card-actions">
                      @if($notification->action_url)
                        <a href="{{ $notification->action_url }}" class="notif-card-btn"><i class="fas fa-arrow-right"></i> Voir</a>
                      @endif
                      <button class="notif-card-btn danger" onclick="deleteNotification({{ $notification->id }})"><i class="fas fa-trash"></i> Supprimer</button>
                    </div>
                  </div>
                </div>
              @endforeach
            @endif

            @if($readNotifications->count() > 0)
              <div style="font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; color:#9ca3af; margin:16px 0 10px;">Déjà lues ({{ $readNotifications->count() }})</div>
              @foreach($readNotifications as $notification)
                <div class="notif-card" data-type="{{ $notification->type ?? 'system' }}">
                  <div class="notif-card-icon {{ $notification->level ?? 'info' }}">
                    <i class="fas {{ $notification->icon ?? 'fa-bell' }}"></i>
                  </div>
                  <div class="notif-card-body">
                    <div class="notif-card-title">{{ $notification->title }}</div>
                    <div class="notif-card-message">{{ $notification->message }}</div>
                    <div class="notif-card-meta">
                      <span class="notif-card-time">{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                      <span class="notif-card-badge {{ $notification->level ?? 'info' }}">{{ $notification->type ?? 'info' }}</span>
                    </div>
                    <div class="notif-card-actions">
                      @if($notification->action_url)
                        <a href="{{ $notification->action_url }}" class="notif-card-btn"><i class="fas fa-arrow-right"></i> Voir</a>
                      @endif
                      <button class="notif-card-btn danger" onclick="deleteNotification({{ $notification->id }})"><i class="fas fa-trash"></i> Supprimer</button>
                    </div>
                  </div>
                </div>
              @endforeach
            @endif

            @if($allNotifications->isEmpty())
              <div class="empty-state">
                <i class="fas fa-bell-slash"></i>
                <h3>Aucune notification</h3>
                <p>Vous n'avez aucune notification pour le moment.</p>
              </div>
            @endif
          </div>
        </div>
      </div>
    </main>

    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <script src="{{ asset('assets/js/core.js') }}"></script>
    <script>
      function markAllAsRead() {
        fetch('/manager/api/notifications/read-all', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          }
        }).then(() => {
          window.location.reload();
        });
      }

      function deleteNotification(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) {
          fetch(`/manager/api/notifications/${id}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json'
            }
          }).then(() => {
            window.location.reload();
          });
        }
      }

      document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.notif-filter-btn').forEach(btn => {
          btn.addEventListener('click', function() {
            document.querySelectorAll('.notif-filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const filter = this.dataset.filter;
            document.querySelectorAll('.notif-card').forEach(card => {
              if (filter === 'all' || card.dataset.type === filter) {
                card.style.display = 'flex';
              } else {
                card.style.display = 'none';
              }
            });
          });
        });

        document.getElementById('searchNotifications').addEventListener('input', function(e) {
          const searchTerm = e.target.value.toLowerCase();
          document.querySelectorAll('.notif-card').forEach(card => {
            const text = card.textContent.toLowerCase();
            card.style.display = text.includes(searchTerm) ? 'flex' : 'none';
          });
        });
      });
    </script>
  </body>
</html>
