@extends('layouts.app')

@section('title', 'Daftar Akun - SPK TOPSIS Faency.id')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm mt-6">
    <h1 class="text-xl font-bold text-slate-900 mb-1">Daftar Akun Baru</h1>
    <p class="text-xs text-slate-500 mb-5">Buat akun untuk mengelola sesi keputusan dan mencatat riwayat perhitungan.</p>

    <form action="{{ route('register.submit') }}" method="POST" class="space-y-4 text-sm">
        @csrf
        <div>
            <label class="block text-xs font-medium text-slate-700 mb-1">Nama Lengkap</label>
            <input type="text" name="nama" value="{{ old('nama') }}" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-700 mb-1">Kata Sandi</label>
            <input type="password" name="password" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-700 mb-1">Konfirmasi Kata Sandi</label>
            <input type="password" name="password_confirmation" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        </div>
        <button type="submit" class="w-full rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white py-2.5 font-semibold cursor-pointer">
            Daftar
        </button>
    </form>

    <p class="mt-4 text-center text-xs text-slate-500">
        Sudah punya akun? <a href="{{ route('login') }}" class="text-indigo-600 font-semibold hover:underline">Masuk di sini</a>
    </p>
</div>
@endsection

