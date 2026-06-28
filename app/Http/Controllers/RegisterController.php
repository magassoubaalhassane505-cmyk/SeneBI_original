<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function create()
    {
        return view('register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'company' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Le nom complet est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être valide.',
            'email.unique' => 'Cet email est déjà utilisé. Si vous avez oublié votre mot de passe, connectez-vous avec l\'option "Mot de passe oublié".',
            'phone.required' => 'Le téléphone est obligatoire.',
            'company.required' => 'L\'entreprise/exploitation est obligatoire.',
            'location.required' => 'La localisation est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $data = [
            'name' => trim($request->name),
            'email' => Str::lower(trim($request->email)),
            'phone' => trim($request->phone),
            'company' => trim($request->company),
            'location' => trim($request->location),
            'password' => $request->password,
            'role' => 'client',
            'status' => 'pending',
            'is_active' => false,
            'rejection_reason' => null,
        ];

        if (Schema::hasColumn('users', 'approved_at')) {
            $data['approved_at'] = null;
            $data['approved_by'] = null;
            $data['rejected_at'] = null;
        }

        User::create($data);

        \App\Models\Notification::notifyManager(
            'inscription',
            'Nouvelle inscription agriculteur',
            "Nouvelle demande d'inscription de {$data['name']} ({$data['company']}) - {$data['location']}.",
            'info',
            url('/manager/supervision')
        );

        return redirect()->route('login')
            ->with('status', 'Votre demande d\'inscription a bien été enregistrée. Elle est en attente de validation par l\'équipe SeneBI.');
    }
}
