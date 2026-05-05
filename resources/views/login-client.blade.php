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
              src="file:///C:/Users/LENOVO/.cursor/projects/c-Users-LENOVO-Desktop-two/assets/c__Users_LENOVO_AppData_Roaming_Cursor_User_workspaceStorage_437c9e11065bafda052dbe12c51d2f7a_images_image-f31dabda-a8de-4fe5-9b81-5a747e688d5a.png"
              alt="Logo SeneBI"
            />
          </div>

          <h1>SeneBI: Espace Client</h1>
          <p class="login-subtitle">Votre plateforme de suivi agricole</p>

          <form id="loginForm" class="auth-form">
            <label for="email">Email</label>
            <input id="email" type="email" value="sidi@sidi-agri.sn" placeholder="Ex: votre-email@entreprise.com" required />

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
