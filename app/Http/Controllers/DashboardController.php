<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Affiche la page d'accueil principale du site (Fichier: index.blade.php)
    public function index() {
        return view('index');
    }

    // Si tu veux que la page 'dashboard.blade.php' soit différente de l'index :
    public function managerDashboard() {
        return view('dashboard');
    }
}