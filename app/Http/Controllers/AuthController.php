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

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Berhasil masuk sebagai ' . Auth::user()->nama . '.');
        }

        return back()
            ->withErrors(['email' => 'Email atau kata sandi tidak sesuai.'])
            ->onlyInput('email');
    }

    public function demoLogin(Request $request): RedirectResponse
    {
        $user = User::where('email', 'admin@faency.id')->first() ?? User::first();

        if (! $user) {
            $user = User::create([
                'nama' => 'Admin Faency.id',
                'email' => 'admin@faency.id',
                'password' => Hash::make('password'),
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Masuk dengan akun Demo Admin (' . $user->nama . ').');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'nama' => $data['nama'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Akun berhasil dibuat. Selamat datang, ' . $user->nama . '!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('dashboard')->with('success', 'Anda telah keluar.');
    }
}

