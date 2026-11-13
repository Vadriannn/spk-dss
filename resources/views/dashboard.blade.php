@extends('layouts.app')

@section('title', 'Dashboard - SPK TOPSIS Faency.id')

@section('content')
<div class="space-y-6">
    <!-- Hero Header -->
    <div class="rounded-2xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-6 sm:p-8 shadow-lg">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-500/20 text-indigo-200 border border-indigo-400/30">
                    Decision Support System &bull; Metode TOPSIS
                </span>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">
                    Sistem Rekomendasi Supplier Pakaian Thrift &mdash; Faency.id
                </h1>
                <p class="text-sm text-slate-300 leading-relaxed">
                    Mengubah proses seleksi pemasok dari penilaian manual yang subjektif menjadi pemeringkatan terstruktur, objektif, dan terukur berdasarkan jarak terhadap solusi ideal positif dan negatif.
                </p>
            </div>
            <div class="flex flex-wrap gap-3 shrink-0">
                @if ($recentSesis->isNotEmpty())
                    <a href="{{ route('sesi-keputusans.show', $recentSesis->first()) }}"
                       class="px-4 py-2.5 rounded-lg bg-indigo-500 hover:bg-indigo-400 text-white text-sm font-semibold shadow transition">
                        Kelola &amp; Hitung Sesi Aktif
                    </a>
                @else
                    <a href="{{ route('sesi-keputusans.index') }}"
                       class="px-4 py-2.5 rounded-lg bg-indigo-500 hover:bg-indigo-400 text-white text-sm font-semibold shadow transition">
                        Buat Sesi Keputusan
                    </a>
                @endif
                <a href="{{ route('proposal') }}"
                   class="px-4 py-2.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-sm font-medium border border-white/15 transition">
                    Lihat Proposal &amp; ERD
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Kriteria Penilaian</div>
            <div class="mt-2 flex items-baseline justify-between">
                <div class="text-3xl font-bold text-slate-900">{{ $totalKriteria }}</div>
                <a href="{{ route('kriterias.index') }}" class="text-xs font-medium text-indigo-600 hover:underline">Kelola &rarr;</a>
            </div>
            <p class="mt-1 text-xs text-slate-500">Harga, Kualitas, Terjual, Stok, Layanan, Reputasi</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Alternatif Supplier</div>
            <div class="mt-2 flex items-baseline justify-between">
                <div class="text-3xl font-bold text-slate-900">{{ $totalAlternatif }}</div>
                <a href="{{ route('alternatifs.index') }}" class="text-xs font-medium text-indigo-600 hover:underline">Tambah Baru &rarr;</a>
            </div>
            <p class="mt-1 text-xs text-slate-500">Dinamis (dapat ditambah/ubah kapan saja)</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Sesi Keputusan</div>
            <div class="mt-2 flex items-baseline justify-between">
                <div class="text-3xl font-bold text-slate-900">{{ $totalSesi }}</div>
                <a href="{{ route('sesi-keputusans.index') }}" class="text-xs font-medium text-indigo-600 hover:underline">Buka Sesi &rarr;</a>
            </div>
            <p class="mt-1 text-xs text-slate-500">Mendukung multi-sesi evaluasi &amp; bobot</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Riwayat Perhitungan</div>
            <div class="mt-2 flex items-baseline justify-between">
                <div class="text-3xl font-bold text-slate-900">{{ $totalRiwayat }}</div>
                <a href="{{ route('riwayat-perhitungans.index') }}" class="text-xs font-medium text-indigo-600 hover:underline">Lihat Riwayat &rarr;</a>
            </div>
            <p class="mt-1 text-xs text-slate-500">Tersimpan lengkap beserta matriks R, Y, A+, A-</p>
        </div>
    </div>

    <!-- Latest Ranking & Active Sessions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between gap-2">
                <div>
                    <h2 class="font-semibold text-slate-900">Peringkat Rekomendasi Supplier Terbaru</h2>
                    @if ($latestRiwayat)
                        <p class="text-xs text-slate-500">
                            Sesi: {{ $latestRiwayat->sesiKeputusan->nama }} &bull; {{ $latestRiwayat->waktu->format('d M Y H:i') }}
                        </p>
                    @endif
                </div>
                @if ($latestRiwayat)
                    <a href="{{ route('riwayat-perhitungans.show', $latestRiwayat) }}"
                       class="text-xs font-semibold text-indigo-600 hover:text-indigo-500">
                        Detail Matriks TOPSIS &rarr;
                    </a>
                @endif
            </div>

            @if ($latestRiwayat && $latestRiwayat->hasilPerhitungans->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-xs font-semibold text-slate-600 uppercase">
                            <tr>
                                <th class="px-4 py-3 text-center w-16">Rank</th>
                                <th class="px-4 py-3 text-left">Kode</th>
                                <th class="px-4 py-3 text-left">Supplier</th>
                                <th class="px-4 py-3 text-right">D+</th>
                                <th class="px-4 py-3 text-right">D-</th>
                                <th class="px-4 py-3 text-right">Preferensi (V)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($latestRiwayat->hasilPerhitungans->take(10) as $hasil)
                                <tr class="{{ $hasil->ranking === 1 ? 'bg-emerald-50/70' : 'hover:bg-slate-50' }}">
                                    <td class="px-4 py-2.5 text-center font-bold">
                                        @if ($hasil->ranking === 1)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-600 text-white text-xs">1</span>
                                        @elseif ($hasil->ranking === 2)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-600 text-white text-xs">2</span>
                                        @elseif ($hasil->ranking === 3)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-500 text-white text-xs">3</span>
                                        @else
                                            <span class="text-slate-500">{{ $hasil->ranking }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 font-mono text-xs text-slate-600">
                                        {{ $hasil->sesiAlternatif->alternatif->kode }}
                                    </td>
                                    <td class="px-4 py-2.5 font-medium text-slate-900">
                                        {{ $hasil->sesiAlternatif->alternatif->nama }}
                                        @if ($hasil->ranking === 1)
                                            <span class="ml-1.5 inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                                                Rekomendasi Utama
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-mono text-xs text-slate-600">{{ number_format($hasil->jarak_positif, 4) }}</td>
                                    <td class="px-4 py-2.5 text-right font-mono text-xs text-slate-600">{{ number_format($hasil->jarak_negatif, 4) }}</td>
                                    <td class="px-4 py-2.5 text-right font-mono font-bold text-indigo-700">{{ number_format($hasil->nilai_preferensi, 4) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($latestRiwayat->hasilPerhitungans->count() > 10)
                    <div class="px-5 py-3 bg-slate-50 border-t border-slate-200 text-xs text-slate-600 flex justify-between items-center">
                        <span>Menampilkan 10 teratas dari {{ $latestRiwayat->hasilPerhitungans->count() }} supplier.</span>
                        <a href="{{ route('riwayat-perhitungans.show', $latestRiwayat) }}" class="font-semibold text-indigo-600 hover:underline">
                            Lihat seluruh {{ $latestRiwayat->hasilPerhitungans->count() }} peringkat &rarr;
                        </a>
                    </div>
                @endif
            @else
                <div class="p-8 text-center text-sm text-slate-500">
                    Belum ada hasil perhitungan TOPSIS. Silakan buka sesi keputusan dan jalankan perhitungan.
                </div>
            @endif
        </div>

        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <h2 class="font-semibold text-slate-900 mb-3">Sesi Keputusan Terbaru</h2>
                <div class="space-y-3">
                    @forelse ($recentSesis as $sesi)
                        <a href="{{ route('sesi-keputusans.show', $sesi) }}"
                           class="block rounded-lg border border-slate-200 p-3 hover:border-indigo-300 hover:bg-indigo-50/30 transition">
                            <div class="flex items-start justify-between gap-2">
                                <div class="font-medium text-sm text-slate-900">{{ $sesi->nama }}</div>
                                <span class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-medium
                                    {{ $sesi->status === 'selesai' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ ucfirst($sesi->status) }}
                                </span>
                            </div>
                            <div class="mt-1.5 text-xs text-slate-500">
                                {{ $sesi->sesi_kriterias_count }} Kriteria &bull; {{ $sesi->sesi_alternatifs_count }} Alternatif &bull; {{ $sesi->riwayat_perhitungans_count }}x Hitung
                            </div>
                        </a>
                    @empty
                        <p class="text-xs text-slate-500">Belum ada sesi keputusan.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <h2 class="font-semibold text-slate-900 mb-2">Alur Penggunaan Dinamis</h2>
                <ol class="text-xs text-slate-600 space-y-2 list-decimal list-inside">
                    <li>Kelola master <strong>Kriteria</strong> &amp; <strong>Alternatif</strong> (bisa ditambah kapan saja).</li>
                    <li>Buka atau buat <strong>Sesi Keputusan</strong> baru.</li>
                    <li>Tambah alternatif baru langsung di dalam sesi beserta skor penilaiannya.</li>
                    <li>Klik <strong>Hitung TOPSIS</strong> untuk menghasilkan peringkat &amp; menyimpan riwayat lengkap.</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

