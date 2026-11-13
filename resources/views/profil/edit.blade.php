@extends('layouts.app')

@section('title', 'Profil - SPK TOPSIS Faency.id')

@section('content')
<div class="max-w-lg">
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-900">Profil & Keamanan</h1>
        <p class="text-xs text-slate-500 mt-0.5">Kelola informasi akun dan kata sandi Anda.</p>
    </div>

    {{-- Info Akun --}}
    <div class="bg-white border border-slate-200 rounded-xl p-5 mb-5 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-700 mb-3">Informasi Akun</h2>
        <div class="space-y-2 text-sm">
            <div class="flex gap-3">
                <span class="w-24 text-xs text-slate-500 pt-0.5">Nama</span>
                <span class="font-medium text-slate-900">{{ auth()->user()->nama }}</span>
            </div>
            <div class="flex gap-3">
                <span class="w-24 text-xs text-slate-500 pt-0.5">Email</span>
                <span class="font-medium text-slate-900">{{ auth()->user()->email }}</span>
            </div>
        </div>
    </div>

    {{-- Ganti Password --}}
    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-700 mb-4">Ganti Kata Sandi</h2>
        <form action="{{ route('profil.password') }}" method="POST" class="space-y-4 text-sm">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Kata Sandi Lama</label>
                <input type="password" name="password_lama" required autocomplete="current-password"
                       class="w-full rounded-lg border @error('password_lama') border-rose-400 @else border-slate-300 @enderror px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                @error('password_lama')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Kata Sandi Baru</label>
                <input type="password" name="password" required autocomplete="new-password"
                       class="w-full rounded-lg border @error('password') border-rose-400 @else border-slate-300 @enderror px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                @error('password')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-slate-400">Minimal 8 karakter.</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" required autocomplete="new-password"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
            </div>

            <div class="pt-1">
                <button type="submit"
                        class="px-5 py-2 rounded-lg bg-slate-900 hover:bg-slate-700 text-white text-sm font-semibold transition cursor-pointer">
                    Simpan Kata Sandi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

