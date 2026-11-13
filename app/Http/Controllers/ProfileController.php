<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('profil.edit');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password_lama'         => ['required', 'string'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password_lama.required'    => 'Kata sandi lama wajib diisi.',
            'password.required'         => 'Kata sandi baru wajib diisi.',
            'password.min'              => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed'        => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->password_lama, $user->password)) {
            return back()->withErrors(['password_lama' => 'Kata sandi lama tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Kata sandi berhasil diperbarui.');
    }
}

