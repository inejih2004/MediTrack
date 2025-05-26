<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validate the form fields
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Log the incoming request
        \Log::info('Login attempt', [
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        if ($user) {
            \Log::info('User found', [
                'email' => $user->email,
                'stored_password' => $user->password,
                'role' => $user->role,
                'service_id' => $user->service_id,
            ]);
        } else {
            \Log::info('User not found for email: ' . $request->email);
        }

        // Check if user exists and password matches (plain text comparison)
        if ($user && $user->password === $request->password) {
            \Log::info('Password match, logging in user: ' . $user->email);
            // Log the user in manually
            Auth::login($user);

            // Verify the user is logged in
            if (Auth::check()) {
                \Log::info('User authenticated successfully: ' . Auth::user()->email);
            } else {
                \Log::error('Authentication failed after login attempt for user: ' . $user->email);
            }

            // Store service_id and role in session
            session(['service_id' => $user->service_id, 'role' => $user->role]);

            // Redirect based on role
            switch ($user->role) {
                case 'service_head':
                    if ($user->service_id) {
                        return redirect()->route('dashboard');
                    } else {
                        Auth::logout();
                        return back()->with('error', "Aucun service associé à cet utilisateur.");
                    }
                case 'stock_manager':
                    return redirect()->route('dashboard');
                case 'accountant':
                    return redirect()->route('dashboard');
                case 'admin':
                    return redirect()->route('admin.dashboard');
                default:
                    \Log::info('Unknown role for user: ' . $user->email);
                    Auth::logout();
                    return back()->with('error', "Rôle d'utilisateur inconnu.");
            }
        }

        // Authentication failed
        \Log::info('Authentication failed: Email or password incorrect for email: ' . $request->email);
        return back()->with('error', 'Email ou mot de passe incorrect.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Vous êtes déconnecté avec succès.');
    }
}