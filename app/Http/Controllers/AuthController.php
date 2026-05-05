<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Affiche la page de connexion pour le Manager
    public function login() {
        return view('login-manager');
    }

    // Affiche la page de connexion pour le Client
    public function loginClient() {
        return view('login-client');
    }

    // Affiche le portail de sécurité
    public function portal() {
        return view('secure-portal');
    }

    // Affiche la page d'erreur 403
    public function error403() {
        return view('403');
    }

    // Gère l'authentification du Manager
    public function authenticate(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Vérifie si c'est un manager (tu peux adapter selon ta logique)
            if (Auth::user()->role === 'manager') {
                return redirect()->intended('/manager/dashboard');
            }
            
            // Redirection par défaut si le rôle n'est pas manager
            return redirect()->intended('/client/dashboard');
        }

        throw ValidationException::withMessages([
            'email' => 'Identifiants incorrects',
        ]);
    }

    // Gère l'authentification du Client
    public function authenticateClient(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Vérifie si c'est un client
            if (Auth::user()->role === 'client') {
                return redirect()->intended('/client/dashboard');
            }
            
            // Redirection vers dashboard manager si c'est un manager
            return redirect()->intended('/manager/dashboard');
        }

        throw ValidationException::withMessages([
            'email' => 'Identifiants incorrects',
        ]);
    }

    // Gère la déconnexion
    public function logout(Request $request) {
        Auth::logout(); 

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}