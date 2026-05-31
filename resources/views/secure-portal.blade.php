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
            <p>Ajoutez des clients, attribuez des droits manager et bloquez des comptes si necessaire.</p>
          </div>
          <div class="admin-stats" id="adminStats"></div>
          <div class="admin-grid">
                        <section class="admin-block admin-block--add" aria-labelledby="admin-add-title">
              <h3 id="admin-add-title" class="admin-block-title">Ajouter un utilisateur</h3>
              <div class="admin-add-layout">
                <form id="createUserForm" class="auth-form mini admin-create-form">
                  @csrf
                  <div class="admin-form-grid">
                    <div class="form-field">
                      <label for="newName">Nom complet</label>
                      <input id="newName" type="text" required autocomplete="name" />
                    </div>
                    <div class="form-field">
                      <label for="newCompany">Entreprise</label>
                      <input id="newCompany" type="text" required autocomplete="organization" />
                    </div>
                    <div class="form-field form-field--full">
                      <label for="newEmail">Email</label>
                      <input id="newEmail" type="email" required autocomplete="email" />
                    </div>
                  </div>
                  <div class="admin-form-footer">
                    <button class="btn" type="submit">Ajouter l'utilisateur</button>
                    <div id="adminFeedback" class="form-feedback" aria-live="polite"></div>
                  </div>
                </form>
              </div>
            </section>
            <section class="admin-block" aria-labelledby="admin-list-title">
              <h3 id="admin-list-title" class="admin-block-title">Liste des comptes</h3>
              <div id="usersList" class="users-list"></div>
            </section>
          </div>
        </section>
      </main>
      <div data-layout="footer"></div>
    </div>
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <script src="{{ asset('assets/js/core.js') }}"></script>
    <script src="{{ asset('assets/js/auth.js') }}"></script>
  </body>
</html>