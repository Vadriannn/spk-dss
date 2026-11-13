@extends('layouts.app')

@section('title', 'Hasil Perhitungan TOPSIS - Faency.id')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('riwayat-perhitungans.index') }}" class="text-indigo-600 hover:underline">&larr; Semua Riwayat</a>
                <span>/</span>
                <span>{{ $riwayatPerhitungan->waktu->format('d M Y H:i:s') }}</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900">Hasil &amp; Detail Perhitungan TOPSIS</h1>
            <p class="text-xs text-slate-600">
                Sesi: <strong>{{ $riwayatPerhitungan->sesiKeputusan->nama }}</strong> &bull;
                Dihitung oleh: <strong>{{ $riwayatPerhitungan->user?->nama ?? '-' }}</strong>
            </p>
        </div>
        <div class="no-print flex flex-wrap gap-2">
            <a href="{{ route('sesi-keputusans.show', $riwayatPerhitungan->sesiKeputusan) }}"
               class="px-4 py-2 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold">
                Ubah Data / Hitung Ulang
            </a>
            <button onclick="window.print()"
                    class="px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold cursor-pointer">
                Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Top 3 Podium -->
    @if ($riwayatPerhitungan->hasilPerhitungans->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach ($riwayatPerhitungan->hasilPerhitungans->take(3) as $top)
                <div class="rounded-xl border p-5 shadow-sm {{ $top->ranking === 1 ? 'border-emerald-300 bg-emerald-50/70' : 'border-slate-200 bg-white' }}">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold
                            {{ $top->ranking === 1 ? 'bg-emerald-600 text-white' : ($top->ranking === 2 ? 'bg-indigo-600 text-white' : 'bg-amber-500 text-white') }}">
                            Peringkat #{{ $top->ranking }}
                        </span>
                        <span class="font-mono text-xs text-slate-500">{{ $top->sesiAlternatif->alternatif->kode }}</span>
                    </div>
                    <div class="mt-3 text-lg font-bold text-slate-900">{{ $top->sesiAlternatif->alternatif->nama }}</div>
                    <div class="mt-2 flex items-baseline justify-between text-xs text-slate-600">
                        <span>Nilai Preferensi (V)</span>
                        <span class="text-xl font-mono font-bold text-indigo-700">{{ number_format($top->nilai_preferensi, 4) }}</span>
                    </div>
                    <div class="mt-2 text-[11px] font-mono text-slate-500 flex justify-between">
                        <span>D+: {{ number_format($top->jarak_positif, 4) }}</span>
                        <span>D-: {{ number_format($top->jarak_negatif, 4) }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Step 5: Final Ranking Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="font-semibold text-slate-900">1. Hasil Akhir Nilai Preferensi ($V_i$) &amp; Perankingan</h2>
            <p class="text-xs text-slate-500">Rumus: $V_i = \frac{D_i^-}{D_i^+ + D_i^-}$ (nilai mendekati 1 menunjukkan alternatif terdekat dengan solusi ideal positif).</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-xs">
                <thead class="bg-slate-50 text-slate-700 font-semibold">
                    <tr>
                        <th class="px-4 py-3 text-center w-16">Rank</th>
                        <th class="px-4 py-3 text-left">Kode</th>
                        <th class="px-4 py-3 text-left">Nama Supplier</th>
                        <th class="px-4 py-3 text-right">Jarak Positif (D+)</th>
                        <th class="px-4 py-3 text-right">Jarak Negatif (D-)</th>
                        <th class="px-4 py-3 text-right">Preferensi (V)</th>
                        <th class="px-4 py-3 w-40">Visual Skor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($riwayatPerhitungan->hasilPerhitungans as $hasil)
                        <tr class="{{ $hasil->ranking === 1 ? 'bg-emerald-50/70' : 'hover:bg-slate-50' }}">
                            <td class="px-4 py-2.5 text-center font-bold">{{ $hasil->ranking }}</td>
                            <td class="px-4 py-2.5 font-mono text-indigo-700 font-semibold">{{ $hasil->sesiAlternatif->alternatif->kode }}</td>
                            <td class="px-4 py-2.5 font-medium text-slate-900">{{ $hasil->sesiAlternatif->alternatif->nama }}</td>
                            <td class="px-4 py-2.5 text-right font-mono">{{ number_format($hasil->jarak_positif, 4) }}</td>
                            <td class="px-4 py-2.5 text-right font-mono">{{ number_format($hasil->jarak_negatif, 4) }}</td>
                            <td class="px-4 py-2.5 text-right font-mono font-bold text-indigo-700">{{ number_format($hasil->nilai_preferensi, 4) }}</td>
                            <td class="px-4 py-2.5">
                                <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full {{ $hasil->ranking === 1 ? 'bg-emerald-600' : 'bg-indigo-600' }}"
                                         style="width: {{ min(100, round($hasil->nilai_preferensi * 100, 1)) }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Step 4: Solusi Ideal Positif & Negatif -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="font-semibold text-slate-900">2. Bobot Kriteria &amp; Solusi Ideal Positif ($A^+$) / Negatif ($A^-$)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-xs">
                <thead class="bg-slate-50 text-slate-700 font-semibold">
                    <tr>
                        <th class="px-4 py-2.5 text-left">Kode</th>
                        <th class="px-4 py-2.5 text-left">Kriteria</th>
                        <th class="px-4 py-2.5 text-center">Tipe</th>
                        <th class="px-4 py-2.5 text-right">Bobot Input</th>
                        <th class="px-4 py-2.5 text-right">Bobot Normal (W)</th>
                        <th class="px-4 py-2.5 text-right">Ideal Positif (A+)</th>
                        <th class="px-4 py-2.5 text-right">Ideal Negatif (A-)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($sesiKriterias as $sk)
                        @php $ideal = $idealsByKrit->get($sk->id); @endphp
                        <tr>
                            <td class="px-4 py-2 font-mono font-bold text-indigo-700">{{ $sk->kriteria->kode }}</td>
                            <td class="px-4 py-2 font-medium text-slate-900">{{ $sk->kriteria->nama }}</td>
                            <td class="px-4 py-2 text-center uppercase">{{ $sk->tipe }}</td>
                            <td class="px-4 py-2 text-right font-mono">{{ number_format($sk->bobot, 2) }}</td>
                            <td class="px-4 py-2 text-right font-mono">{{ number_format($totalBobot > 0 ? $sk->bobot / $totalBobot : 0, 4) }}</td>
                            <td class="px-4 py-2 text-right font-mono text-emerald-700 font-semibold">{{ $ideal ? number_format($ideal->ideal_positif, 4) : '-' }}</td>
                            <td class="px-4 py-2 text-right font-mono text-rose-700 font-semibold">{{ $ideal ? number_format($ideal->ideal_negatif, 4) : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Step 1, 2, 3: Detail Matrices X, R, Y -->
    @foreach ([
        ['title' => '3. Matriks Keputusan Awal (X)', 'field' => 'nilai_awal', 'decimals' => 2],
        ['title' => '4. Matriks Ternormalisasi (R)', 'field' => 'nilai_normalisasi', 'decimals' => 4],
        ['title' => '5. Matriks Ternormalisasi Terbobot (Y)', 'field' => 'nilai_terbobot', 'decimals' => 4],
    ] as $matrix)
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-200">
                <h2 class="font-semibold text-slate-900 text-sm">{{ $matrix['title'] }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-xs">
                    <thead class="bg-slate-50 text-slate-700 font-semibold">
                        <tr>
                            <th class="px-3 py-2.5 text-left">Kode</th>
                            <th class="px-3 py-2.5 text-left">Alternatif</th>
                            @foreach ($sesiKriterias as $sk)
                                <th class="px-3 py-2.5 text-right font-mono">{{ $sk->kriteria->kode }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($sesiAlternatifs as $sa)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-2 font-mono font-bold text-indigo-700">{{ $sa->alternatif->kode }}</td>
                                <td class="px-3 py-2 font-medium text-slate-900">{{ $sa->alternatif->nama }}</td>
                                @foreach ($sesiKriterias as $sk)
                                    @php $d = $detailsByAltKrit->get("{$sa->id}_{$sk->id}"); @endphp
                                    <td class="px-3 py-2 text-right font-mono text-slate-700">
                                        {{ $d ? number_format($d->{$matrix['field']}, $matrix['decimals']) : '-' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
@endsection

