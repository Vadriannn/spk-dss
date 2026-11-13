<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SPK Faency.id - Tabel A')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="app-shell">
    <header class="topbar">
        <div class="container topbar-inner">
            <a href="{{ route('dashboard') }}" class="brand">
                <div class="brand-badge">F</div>
                <div>
                    <div class="brand-title">SPK Faency.id</div>
                    <div class="brand-sub">Input Alternatif, Bobot &amp; Tabel A</div>
                </div>
            </a>

            <nav class="nav">
                <a href="{{ route('dashboard') }}"
                   class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Tabel A (Nilai Alternatif &amp; Kriteria)
                </a>
                <a href="{{ route('kriterias.index') }}"
                   class="nav-link {{ request()->routeIs('kriterias.*') ? 'active' : '' }}">
                    Kriteria &amp; Bobot
                </a>
                <a href="{{ route('alternatifs.index') }}"
                   class="nav-link {{ request()->routeIs('alternatifs.*') ? 'active' : '' }}">
                    Alternatif Supplier
                </a>
            </nav>
        </div>
    </header>

    <main class="container main-content">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <strong>Terjadi kesalahan input:</strong>
                <ul style="margin: 0.35rem 0 0; padding-left: 1.25rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer">
        SPK Pemilihan Supplier Thrift Shop Faency.id &mdash; Modul Input Alternatif, Bobot Kriteria &amp; Nilai Alternatif terhadap Kriteria (Tabel A)
    </footer>
</div>
</body>
</html>
