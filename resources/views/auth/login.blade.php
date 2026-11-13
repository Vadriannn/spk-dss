@extends('layouts.app')

@section('title', 'Masuk - SPK TOPSIS Faency.id')

@section('content')
<div class="max-w-sm mx-auto mt-10">
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-900">Masuk ke Sistem</h1>
        <p class="text-xs text-slate-500 mt-0.5">Sistem Pendukung Keputusan Faency.id</p>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
        <form action="{{ route('login.submit') }}" method="POST" class="space-y-4 text-sm">
            @csrf

            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full rounded-lg border @error('email') border-rose-400 @else border-slate-300 @enderror px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                @error('email')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Kata Sandi</label>
                <input type="password" name="password" required autocomplete="current-password"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember" class="rounded border-slate-300">
                <label for="remember" class="text-xs text-slate-600">Ingat saya</label>
            </div>

            <button type="submit"
                    class="w-full rounded-lg bg-slate-900 hover:bg-slate-800 text-white py-2.5 text-sm font-semibold transition cursor-pointer">
                Masuk
            </button>
        </form>
    </div>
</div>
@endsection

