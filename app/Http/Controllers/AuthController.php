<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'wisatawan',
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put([
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role,
        ]);

        $redirectRoute = $user->role === 'wisatawan'
            ? 'dashboard-wisatawan.index'
            : 'wisata.index';

        return redirect()
            ->route($redirectRoute)
            ->with('success', 'Registrasi berhasil.');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $request->session()->put([
                'user_id' => $user?->id,
                'user_email' => $user?->email,
                'user_role' => $user?->role,
            ]);

            // Custom redirect logic for wisatawan
            if ($user?->role === 'wisatawan') {
                $rattingCount = \App\Models\Ratting::where('user_id', $user->id)->count();
                if ($rattingCount < 5) {
                    return redirect()->route('rattings-wisatawan.index');
                } else {
                    return redirect()->route('dashboard-wisatawan.index');
                }
            }

            return redirect()->route('wisata.index');
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Berhasil logout.');
    }
}
