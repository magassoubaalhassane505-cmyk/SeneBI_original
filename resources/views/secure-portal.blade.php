<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SeneBI - Portail securise</title>
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}" />
  </head>
  <body data-page="auth-portal">
    <div class="app">
     @include('header-manager')
      <main class="portal-shell container">
        
        <section class="card section-shell" id="adminPanel">
          <div class="section-head">
            <h2>Gestion des comptes</h2>
            <p>Visualisez la liste complète de tous les clients approuvés.</p>
          </div>
          <div class="admin-stats" id="adminStats"></div>
          <div class="admin-grid">
            <section class="admin-block" aria-labelledby="admin-list-title">
              <h3 id="admin-list-title" class="admin-block-title">Liste des comptes approuvés</h3>
              <div id="usersList" class="users-list"></div>
            </section>
          </div>
        </section>

        <script>
          // Passer les clients approuvés au JavaScript
          window.SeneBI = window.SeneBI || {};
          window.SeneBI.approvedClients = {{ \Illuminate\Support\Js::from(
            $approvedClients
              ->map(fn($client) => [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'company' => $client->company,
                'phone' => $client->phone,
                'location' => $client->location,
                'role' => $client->role ?? 'client',
                'blocked' => ! $client->is_active,
                'status' => $client->status,
              ])
              ->values()
          ) }};
        </script>
      </main>
      @include('partials.footer-manager')
    </div>
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <script src="{{ asset('assets/js/core.js') }}"></script>
    <script src="{{ asset('assets/js/auth.js') }}"></script>
  </body>
</html>