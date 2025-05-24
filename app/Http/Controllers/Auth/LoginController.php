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

            // Redirect based on role
            switch ($user->role) {
                case 'stock_manager':
                    \Log::info('Redirecting to stock.dashboard');
                    return redirect()->route('stock.dashboard');
                case 'service_head':
                    \Log::info('Redirecting to service.dashboard');
                    return redirect()->route('service.dashboard');
                case 'accountant':
                    \Log::info('Redirecting to accountant.dashboard');
                    return redirect()->route('accountant.dashboard');
                case 'admin':
                    \Log::info('Redirecting to admin.dashboard');
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
        return redirect('/login');
    }
}