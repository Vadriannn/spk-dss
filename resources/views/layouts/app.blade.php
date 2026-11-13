<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SPK TOPSIS - Faency.id')</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .print-full { width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
            .print-break { page-break-before: always; }
        }
    </style>
</head>
<body class="h-full text-slate-800 antialiased">
<div class="min-h-full flex flex-col">
    <!-- Top Navigation -->
    <header class="no-print bg-slate-900 text-white shadow-md sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between gap-4">
                <div class="flex items-center gap-6">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 shrink-0">
                        <div class="w-9 h-9 rounded-lg bg-indigo-500 flex items-center justify-center font-bold text-white shadow">
                            F
                        </div>
                        <div>
                            <div class="font-bold text-sm leading-tight tracking-wide">SPK Faency.id</div>
                            <div class="text-[11px] text-slate-400 leading-tight">Metode TOPSIS</div>
                        </div>
                    </a>

                    <nav class="hidden md:flex items-center gap-1 text-sm">
                        <a href="{{ route('dashboard') }}"
                           class="px-3 py-2 rounded-md transition {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('sesi-keputusans.index') }}"
                           class="px-3 py-2 rounded-md transition {{ request()->routeIs('sesi-keputusans.*') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            Sesi &amp; Hitung TOPSIS
                        </a>
                        <a href="{{ route('kriterias.index') }}"
                           class="px-3 py-2 rounded-md transition {{ request()->routeIs('kriterias.*') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            Kriteria
                        </a>
                        <a href="{{ route('alternatifs.index') }}"
                           class="px-3 py-2 rounded-md transition {{ request()->routeIs('alternatifs.*') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            Alternatif Supplier
                        </a>
                        <a href="{{ route('riwayat-perhitungans.index') }}"
                           class="px-3 py-2 rounded-md transition {{ request()->routeIs('riwayat-perhitungans.*') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            Riwayat
                        </a>
                        <a href="{{ route('proposal') }}"
                           class="px-3 py-2 rounded-md transition {{ request()->routeIs('proposal') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            Proposal &amp; ERD
                        </a>
                    </nav>
                </div>

                <div class="flex items-center gap-3 text-sm">
                    @auth
                        <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-800 text-slate-200 text-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            {{ auth()->user()->nama }}
                        </span>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-md bg-slate-800 hover:bg-slate-700 text-xs font-medium text-slate-200 transition cursor-pointer">
                                Keluar
                            </button>
                        </form>
                    @else
                        <form action="{{ route('demo-login') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-md bg-indigo-600 hover:bg-indigo-500 text-xs font-semibold text-white transition cursor-pointer">
                                Login Demo Admin
                            </button>
                        </form>
                        <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-md bg-slate-800 hover:bg-slate-700 text-xs font-medium text-slate-200 transition">
                            Masuk
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Mobile Nav -->
            <div class="md:hidden pb-3 flex flex-wrap gap-1 text-xs">
                <a href="{{ route('dashboard') }}" class="px-2.5 py-1.5 rounded {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-300' }}">Dashboard</a>
                <a href="{{ route('sesi-keputusans.index') }}" class="px-2.5 py-1.5 rounded {{ request()->routeIs('sesi-keputusans.*') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-300' }}">Sesi TOPSIS</a>
                <a href="{{ route('kriterias.index') }}" class="px-2.5 py-1.5 rounded {{ request()->routeIs('kriterias.*') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-300' }}">Kriteria</a>
                <a href="{{ route('alternatifs.index') }}" class="px-2.5 py-1.5 rounded {{ request()->routeIs('alternatifs.*') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-300' }}">Alternatif</a>
                <a href="{{ route('riwayat-perhitungans.index') }}" class="px-2.5 py-1.5 rounded {{ request()->routeIs('riwayat-perhitungans.*') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-300' }}">Riwayat</a>
                <a href="{{ route('proposal') }}" class="px-2.5 py-1.5 rounded {{ request()->routeIs('proposal') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-300' }}">Proposal &amp; ERD</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 print-full">
        @if (session('success'))
            <div class="no-print mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="no-print mb-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <div class="font-semibold mb-1">Terjadi kesalahan input:</div>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="no-print border-t border-slate-200 bg-white py-4 text-center text-xs text-slate-500">
        Sistem Pendukung Keputusan Pemilihan Supplier Thrift Shop Faency.id &mdash; Metode TOPSIS (Laravel &amp; MySQL)
    </footer>
</div>
</body>
</html>

