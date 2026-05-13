<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SeneBI - Connexion Client</title>
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}" />
  </head>
  <body data-page="auth-login">
    <main class="auth-shell">
      <section class="login-frame">
        <aside class="login-photo" aria-hidden="true"></aside>
        <div class="login-panel">
          <div class="login-brand">
            <img
               class="login-brand-image"
              src="{{ asset('assets/img/logo.png') }}"
              alt="Logo SeneBI"
            />
          </div>

          <h1>SeneBI: Espace Client</h1>
          <p class="login-subtitle">Votre plateforme de suivi agricole</p>

          <form id="loginForm" class="auth-form">
            <label for="email">Email</label>
            <input id="email" type="email" value="sidi@sidi-agri.ml" placeholder="Ex: votre-email@entreprise.com" required />

            <label for="password">Mot de passe</label>
            <input id="password" type="password" value="client123" placeholder="Votre mot de passe" required />

            <button class="btn login-submit" type="submit">Se connecter</button>
            <div id="loginFeedback" class="form-feedback" aria-live="polite"></div>
          </form>

          <div class="auth-switch">
            <p>Vous êtes un manager ?</p>
            <a href="{{ route('login.manager') }}" class="btn secondary">Accès Manager</a>
          </div>
        </div>
      </section>
    </main>

    <script src="{{ asset('assets/js/auth.js') }}"></script>
  </body>
</html>
