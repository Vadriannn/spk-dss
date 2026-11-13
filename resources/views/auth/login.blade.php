@extends('layouts.app')

@section('title', 'Masuk - SPK TOPSIS Faency.id')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm mt-6">
    <h1 class="text-xl font-bold text-slate-900 mb-1">Masuk ke Sistem SPK</h1>
    <p class="text-xs text-slate-500 mb-5">Gunakan akun admin atau klik tombol Login Demo untuk mencoba langsung.</p>

    <form action="{{ route('login.submit') }}" method="POST" class="space-y-4 text-sm">
        @csrf
        <div>
            <label class="block text-xs font-medium text-slate-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', 'admin@faency.id') }}" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-700 mb-1">Kata Sandi</label>
            <input type="password" name="password" value="password" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        </div>
        <button type="submit" class="w-full rounded-lg bg-slate-900 hover:bg-slate-800 text-white py-2.5 font-semibold cursor-pointer">
            Masuk
        </button>
    </form>

    <div class="my-4 border-t border-slate-200 pt-4">
        <form action="{{ route('demo-login') }}" method="POST">
            @csrf
            <button type="submit" class="w-full rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white py-2.5 text-sm font-semibold cursor-pointer">
                1-Klik Login Demo Admin (admin@faency.id)
            </button>
        </form>
    </div>

    <p class="text-center text-xs text-slate-500">
        Belum punya akun? <a href="{{ route('register') }}" class="text-indigo-600 font-semibold hover:underline">Daftar di sini</a>
    </p>
</div>
@endsection

