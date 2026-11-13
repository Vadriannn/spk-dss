@extends('layouts.app')

@section('title', 'Dokumen Proposal & ERD - SPK TOPSIS Faency.id')

@section('content')
<div class="space-y-6">
    <div class="no-print flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Dokumen Proposal Studi Kasus &amp; ERD</h1>
            <p class="text-xs text-slate-500">Ringkasan studi kasus Faency.id beserta rancangan basis data 11 tabel sesuai ERD.</p>
        </div>
        <button onclick="window.print()"
                class="px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-sm font-medium cursor-pointer">
            Cetak Proposal (PDF / Print)
        </button>
    </div>

    <!-- Proposal Sheet -->
    <div class="bg-white rounded-xl border border-slate-200 p-6 sm:p-10 shadow-sm space-y-6">
        <div class="border-b border-slate-200 pb-4 text-center">
            <div class="text-xs font-semibold uppercase tracking-widest text-indigo-600">Proposal Sistem Pendukung Keputusan (DSS)</div>
            <h2 class="mt-1 text-xl sm:text-2xl font-bold text-slate-900">
                Sistem Rekomendasi Supplier Pakaian Thrift Berbasis Web Menggunakan Metode TOPSIS Pada Faency.id
            </h2>
            <p class="mt-2 text-xs text-slate-500">
                Rujukan Jurnal: Fandy Gery Alfiyan &amp; Achmad Sehan, <em>"Sistem Rekomendasi Supplier Pakaian Berbasis Web Menggunakan Metode Topsis Pada Faency.Id"</em>, Indonesian Journal of Multidisciplinary on Social and Technology (IJMST), Vol. 4 No. 3, 2026.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 text-sm leading-relaxed text-slate-700">
            <div class="rounded-lg bg-slate-50 p-4 border border-slate-200">
                <h3 class="font-bold text-slate-900 mb-1.5">1. Latar Belakang</h3>
                <p class="text-xs leading-relaxed">
                    Pemilihan supplier merupakan aspek krusial dalam menjaga daya saing usaha pakaian bekas (<em>thrift shop</em>) <strong>Faency.id</strong>. Kualitas supplier berpengaruh langsung pada harga jual, mutu barang, kepastian stok gudang, serta kepuasan pelanggan.
                </p>
            </div>
            <div class="rounded-lg bg-slate-50 p-4 border border-slate-200">
                <h3 class="font-bold text-slate-900 mb-1.5">2. Permasalahan</h3>
                <p class="text-xs leading-relaxed">
                    Penentuan supplier di Faency.id sebelumnya masih dilakukan secara manual dan subjektif (berdasarkan rekomendasi rekan atau pengalaman pribadi), sehingga memakan waktu lama dan tidak terukur secara objektif.
                </p>
            </div>
            <div class="rounded-lg bg-slate-50 p-4 border border-slate-200">
                <h3 class="font-bold text-slate-900 mb-1.5">3. Solusi &amp; Metode TOPSIS</h3>
                <p class="text-xs leading-relaxed">
                    Dibangun aplikasi SPK berbasis web (PHP/Laravel &amp; MySQL) dengan metode <strong>TOPSIS</strong> yang memilih alternatif terbaik berdasarkan jarak terdekat ke solusi ideal positif ($A^+$) dan jarak terjauh dari solusi ideal negatif ($A^-$).
                </p>
            </div>
        </div>

        <!-- Criteria Table -->
        <div>
            <h3 class="font-bold text-slate-900 text-sm mb-2">4. Daftar Kriteria Pengambilan Keputusan ({{ $kriterias->count() }} Kriteria)</h3>
            <div class="overflow-x-auto border border-slate-200 rounded-lg">
                <table class="min-w-full divide-y divide-slate-200 text-xs">
                    <thead class="bg-slate-50 font-semibold text-slate-700">
                        <tr>
                            <th class="px-3 py-2 text-left">Kode</th>
                            <th class="px-3 py-2 text-left">Nama Kriteria</th>
                            <th class="px-3 py-2 text-left">Tipe</th>
                            <th class="px-3 py-2 text-right">Bobot Default</th>
                            <th class="px-3 py-2 text-left">Keterangan / Rentang Penilaian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($kriterias as $k)
                            <tr>
                                <td class="px-3 py-2 font-mono font-semibold">{{ $k->kode }}</td>
                                <td class="px-3 py-2 font-medium text-slate-900">{{ $k->nama }}</td>
                                <td class="px-3 py-2 uppercase">{{ $k->enum }}</td>
                                <td class="px-3 py-2 text-right font-mono">{{ number_format($k->bobot_default, 2) }}</td>
                                <td class="px-3 py-2 text-slate-600">{{ $k->deskripsi }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Alternatives Table -->
        <div>
            <h3 class="font-bold text-slate-900 text-sm mb-2">5. Daftar Alternatif Supplier ({{ $alternatifs->count() }} Alternatif)</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 text-xs">
                @foreach ($alternatifs as $alt)
                    <div class="flex items-center justify-between px-3 py-2 rounded border border-slate-200 bg-slate-50">
                        <span><strong class="font-mono text-indigo-700">{{ $alt->kode }}</strong> &mdash; {{ $alt->nama }}</span>
                    </div>
                @endforeach
            </div>
            @if ($latestRiwayat && $latestRiwayat->hasilPerhitungans->isNotEmpty())
                @php $top = $latestRiwayat->hasilPerhitungans->first(); @endphp
                <p class="mt-3 text-xs text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2">
                    <strong>Hasil Pengujian Sistem:</strong> Peringkat ke-1 ditempati oleh <strong>{{ $top->sesiAlternatif->alternatif->nama }} ({{ $top->sesiAlternatif->alternatif->kode }})</strong> dengan nilai preferensi <strong>V = {{ number_format($top->nilai_preferensi, 4) }}</strong>.
                </p>
            @endif
        </div>

        <!-- ERD Summary -->
        <div class="print-break pt-4 border-t border-slate-200">
            <h3 class="font-bold text-slate-900 text-sm mb-3">6. Entity Relationship Diagram (ERD) &mdash; 11 Tabel</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
                @php
                    $erdTables = [
                        'users' => ['PK id', 'nama, email, password', 'timestamps'],
                        'kriterias' => ['PK id', 'kode, nama, deskripsi', 'enum (benefit/cost), bobot_default'],
                        'alternatifs' => ['PK id', 'kode, nama, deskripsi', 'timestamps'],
                        'sesi_keputusans' => ['PK id', 'nama, deskripsi, status', 'FK users_id -> users.id'],
                        'sesi_kriterias' => ['PK id', 'bobot, tipe', 'FK sesi_keputusans_id, kriterias_id'],
                        'sesi_alternatifs' => ['PK id', 'FK sesi_keputusans_id', 'FK alternatifs_id'],
                        'penilaians' => ['PK id', 'nilai (DECIMAL)', 'FK sesi_kriterias_id, sesi_alternatif_id'],
                        'riwayat_perhitungans' => ['PK id', 'waktu (DATETIME)', 'FK users_id, sesi_keputusans_id'],
                        'ideal_perhitungans' => ['PK id', 'ideal_positif, ideal_negatif', 'FK sesi_kriterias_id, riwayat_perhitungans_id'],
                        'detail_perhitungans' => ['PK id', 'nilai_awal, nilai_normalisasi, nilai_terbobot', 'FK riwayat_perhitungans_id, sesi_kriterias_id, sesi_alternatif_id'],
                        'hasil_perhitungans' => ['PK id', 'jarak_positif, jarak_negatif, nilai_preferensi, ranking', 'FK riwayat_perhitungans_id, sesi_alternatif_id'],
                    ];
                @endphp
                @foreach ($erdTables as $table => $cols)
                    <div class="rounded-lg border border-slate-300 overflow-hidden bg-white">
                        <div class="bg-slate-800 text-white px-3 py-1.5 font-mono font-semibold">{{ $table }}</div>
                        <ul class="p-3 space-y-1 text-[11px] font-mono text-slate-600">
                            @foreach ($cols as $col)
                                <li>&bull; {{ $col }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

