<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SeneBI - Connexion</title>
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}" />
  </head>
  <body data-page="auth-login" data-server-side="1">
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

          <h1>SeneBI</h1>
          <p class="login-subtitle">Connectez-vous à votre espace</p>

          @if (session('status'))
            <div class="form-feedback success">{{ session('status') }}</div>
          @endif

          @if ($errors->any())
            <div class="form-feedback error">
              @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
              @endforeach
            </div>
          @endif

          <form method="POST" action="{{ route('login.post') }}" class="auth-form">
            @csrf
            <label for="email">Email</label>
            <input
              id="email"
              name="email"
              type="email"
              value="{{ old('email') }}"
              placeholder="Ex: votre-email@entreprise.com"
              required
              autocomplete="username"
            />

            <label for="password">Mot de passe</label>
            <input
              id="password"
              name="password"
              type="password"
              placeholder="Votre mot de passe"
              required
              autocomplete="current-password"
            />

            <button class="btn login-submit" type="submit">Se connecter</button>
          </form>

          <div class="auth-switch" style="margin-top: 12px;">
            <a href="{{ route('password.request') }}" style="color: #4b5563; font-weight: 500;">Mot de passe oublié ?</a>
          </div>

          <div class="auth-switch" style="margin-top: 16px;">
            <a href="{{ route('public.register') }}" class="text-muted" style="color: #4b5563; font-weight: 500;">Vous n'avez pas de compte ? S'inscrire</a>
          </div>
        </div>
      </section>
    </main>
  </body>
</html>
