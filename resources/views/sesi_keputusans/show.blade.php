@extends('layouts.app')

@section('title', $sesiKeputusan->nama . ' - Sesi Keputusan TOPSIS')

@section('content')
<div class="space-y-6">
    <!-- Session Header + Action -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="space-y-1">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('sesi-keputusans.index') }}" class="text-xs font-medium text-indigo-600 hover:underline">&larr; Semua Sesi</a>
                <span class="text-slate-300">/</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold
                    {{ $sesiKeputusan->status === 'selesai' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ ucfirst($sesiKeputusan->status) }}
                </span>
            </div>
            <h1 class="text-xl font-bold text-slate-900">{{ $sesiKeputusan->nama }}</h1>
            @if ($sesiKeputusan->deskripsi)
                <p class="text-xs text-slate-600">{{ $sesiKeputusan->deskripsi }}</p>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            @if ($latestRiwayat)
                <a href="{{ route('riwayat-perhitungans.show', $latestRiwayat) }}"
                   class="px-4 py-2.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-medium transition">
                    Lihat Hasil Terakhir
                </a>
            @endif
            <form action="{{ route('sesi-keputusans.hitung', $sesiKeputusan) }}" method="POST">
                @csrf
                <button type="submit"
                        class="px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold shadow transition cursor-pointer">
                    &#9654; Hitung TOPSIS Sekarang
                </button>
            </form>
        </div>
    </div>

    <!-- Step 1: Bobot & Tipe Kriteria Sesi -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="font-semibold text-slate-900">1. Kriteria &amp; Bobot Penilaian Sesi ({{ $sesiKeputusan->sesiKriterias->count() }} Kriteria)</h2>
                <p class="text-xs text-slate-500">Bobot akan dinormalisasi otomatis oleh metode TOPSIS sehingga total proporsi = 100%.</p>
            </div>
            @if ($availableKriterias->isNotEmpty())
                <form action="{{ route('sesi-keputusans.kriterias.add', $sesiKeputusan) }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <select name="kriterias_id" class="rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs">
                        @foreach ($availableKriterias as $ak)
                            <option value="{{ $ak->id }}">{{ $ak->kode }} - {{ $ak->nama }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-xs font-medium cursor-pointer">
                        + Tambah Kriteria ke Sesi
                    </button>
                </form>
            @endif
        </div>

        <form action="{{ route('sesi-keputusans.kriterias.update', $sesiKeputusan) }}" method="POST" class="p-5">
            @csrf
            @method('PUT')
            @php $totalBobot = max(0.0001, $sesiKeputusan->sesiKriterias->sum('bobot')); @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach ($sesiKeputusan->sesiKriterias as $sk)
                    <div class="rounded-lg border border-slate-200 p-3 bg-slate-50/70 space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-semibold text-xs text-slate-900">
                                <span class="font-mono text-indigo-700">{{ $sk->kriteria->kode }}</span> &mdash; {{ $sk->kriteria->nama }}
                            </span>
                            <span class="text-[11px] font-mono text-slate-500">
                                {{ number_format(($sk->bobot / $totalBobot) * 100, 1) }}%
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] uppercase text-slate-500 mb-0.5">Bobot</label>
                                <input type="number" step="0.01" min="0.01" name="kriterias[{{ $sk->id }}][bobot]" value="{{ $sk->bobot }}" required
                                       class="w-full rounded border border-slate-300 bg-white px-2 py-1 text-xs font-mono">
                            </div>
                            <div>
                                <label class="block text-[10px] uppercase text-slate-500 mb-0.5">Tipe</label>
                                <select name="kriterias[{{ $sk->id }}][tipe]" class="w-full rounded border border-slate-300 bg-white px-2 py-1 text-xs">
                                    <option value="benefit" {{ $sk->tipe === 'benefit' ? 'selected' : '' }}>Benefit</option>
                                    <option value="cost" {{ $sk->tipe === 'cost' ? 'selected' : '' }}>Cost</option>
                                </select>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold cursor-pointer">
                    Simpan Perubahan Bobot &amp; Tipe
                </button>
            </div>
        </form>
    </div>

    <!-- Step 2: Tambah Alternatif Baru Dinamis + Input Nilai -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-indigo-200 p-5 shadow-sm">
            <h2 class="font-semibold text-slate-900 mb-1">2a. Tambah Alternatif Supplier Baru + Nilai</h2>
            <p class="text-xs text-slate-500 mb-4">Buat supplier baru (belum ada di master) sekaligus masukkan nilai kriterianya untuk sesi ini.</p>

            <form action="{{ route('sesi-keputusans.alternatifs.add-new', $sesiKeputusan) }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div class="grid grid-cols-3 gap-2.5">
                    <div>
                        <label class="block font-medium text-slate-700 mb-1">Kode</label>
                        <input type="text" name="kode" value="{{ old('kode', $suggestedCode) }}" required
                               class="w-full rounded-lg border border-slate-300 px-2.5 py-1.5 font-mono font-semibold">
                    </div>
                    <div class="col-span-2">
                        <label class="block font-medium text-slate-700 mb-1">Nama Supplier</label>
                        <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Contoh: Thrift Jaya Abadi" required
                               class="w-full rounded-lg border border-slate-300 px-2.5 py-1.5">
                    </div>
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Keterangan (Opsional)</label>
                    <input type="text" name="deskripsi" value="{{ old('deskripsi') }}" placeholder="Catatan lokasi / kontak supplier"
                           class="w-full rounded-lg border border-slate-300 px-2.5 py-1.5">
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-1.5">Nilai Penilaian Kriteria</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach ($sesiKeputusan->sesiKriterias as $sk)
                            <div class="rounded border border-slate-200 p-2 bg-slate-50">
                                <div class="font-semibold text-[11px] text-slate-800 truncate" title="{{ $sk->kriteria->nama }}">
                                    {{ $sk->kriteria->kode }}: {{ $sk->kriteria->nama }}
                                </div>
                                <input type="number" step="0.01" min="0.01" name="nilai[{{ $sk->id }}]" value="2" required
                                       class="mt-1 w-full rounded border border-slate-300 bg-white px-2 py-1 font-mono text-xs">
                            </div>
                        @endforeach
                    </div>
                </div>
                <button type="submit" class="w-full rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white py-2 font-semibold cursor-pointer">
                    + Tambah Supplier Baru &amp; Simpan Nilai
                </button>
            </form>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm flex flex-col justify-between">
            <div>
                <h2 class="font-semibold text-slate-900 mb-1">2b. Tambah Dari Master Alternatif yang Ada</h2>
                <p class="text-xs text-slate-500 mb-4">Pilih supplier dari daftar master jika sebelumnya sudah terdaftar tetapi belum masuk ke sesi ini.</p>

                @if ($availableAlternatifs->isNotEmpty())
                    <form action="{{ route('sesi-keputusans.alternatifs.add-existing', $sesiKeputusan) }}" method="POST" class="space-y-3 text-xs">
                        @csrf
                        <div>
                            <label class="block font-medium text-slate-700 mb-1">Pilih Supplier</label>
                            <select name="alternatifs_id" class="w-full rounded-lg border border-slate-300 px-2.5 py-2 text-xs">
                                @foreach ($availableAlternatifs as $aa)
                                    <option value="{{ $aa->id }}">{{ $aa->kode }} &mdash; {{ $aa->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach ($sesiKeputusan->sesiKriterias as $sk)
                                <div class="rounded border border-slate-200 p-2 bg-slate-50">
                                    <div class="font-semibold text-[11px] text-slate-800 truncate">{{ $sk->kriteria->kode }}: {{ $sk->kriteria->nama }}</div>
                                    <input type="number" step="0.01" min="0.01" name="nilai[{{ $sk->id }}]" value="2" required
                                           class="mt-1 w-full rounded border border-slate-300 bg-white px-2 py-1 font-mono text-xs">
                                </div>
                            @endforeach
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-slate-800 hover:bg-slate-700 text-white py-2 font-semibold cursor-pointer">
                            + Masukkan ke Sesi Ini
                        </button>
                    </form>
                @else
                    <div class="rounded-lg bg-slate-50 border border-slate-200 p-4 text-xs text-slate-600">
                        Seluruh master alternatif ({{ $sesiKeputusan->sesiAlternatifs->count() }} supplier) sudah masuk ke dalam sesi keputusan ini. Gunakan form di sebelah kiri untuk menambahkan supplier baru.
                    </div>
                @endif
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-500">
                <strong>Panduan Skala Penilaian (Studi Kasus):</strong>
                Skala 1 = Rendah / Kurang / Murah (untuk C1 Cost), 2 = Sedang / Cukup, 3 = Tinggi / Baik / Grade A. Anda juga dapat mengisi nilai numerik bebas (misal desimal atau skala 1&ndash;5).
            </div>
        </div>
    </div>

    <!-- Step 3: Matriks Penilaian Alternatif x Kriteria -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-slate-900">3. Matriks Keputusan ($X_{ij}$) &mdash; {{ $sesiKeputusan->sesiAlternatifs->count() }} Alternatif</h2>
                <p class="text-xs text-slate-500">Ubah nilai penilaian di bawah ini lalu klik "Simpan Matriks Nilai", kemudian klik "Hitung TOPSIS Sekarang".</p>
            </div>
        </div>

        <form action="{{ route('sesi-keputusans.penilaians.update', $sesiKeputusan) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-xs">
                    <thead class="bg-slate-50 text-slate-700 font-semibold">
                        <tr>
                            <th class="px-3 py-3 text-left w-16">Kode</th>
                            <th class="px-3 py-3 text-left min-w-[180px]">Nama Alternatif Supplier</th>
                            @foreach ($sesiKeputusan->sesiKriterias as $sk)
                                <th class="px-2.5 py-3 text-center min-w-[95px]">
                                    <div class="font-mono text-indigo-700">{{ $sk->kriteria->kode }}</div>
                                    <div class="font-normal text-[11px] text-slate-500 truncate max-w-[110px] mx-auto" title="{{ $sk->kriteria->nama }}">
                                        {{ $sk->kriteria->nama }}
                                    </div>
                                    <span class="inline-block mt-0.5 px-1.5 py-0.2 rounded text-[10px] {{ $sk->tipe === 'cost' ? 'bg-amber-100 text-amber-800' : 'bg-sky-100 text-sky-800' }}">
                                        {{ strtoupper($sk->tipe) }}
                                    </span>
                                </th>
                            @endforeach
                            <th class="px-3 py-3 text-center w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($sesiKeputusan->sesiAlternatifs as $sa)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-3 py-2 font-mono font-bold text-indigo-700">{{ $sa->alternatif->kode }}</td>
                                <td class="px-3 py-2 font-medium text-slate-900">{{ $sa->alternatif->nama }}</td>
                                @foreach ($sesiKeputusan->sesiKriterias as $sk)
                                    @php
                                        $key = "{$sa->id}_{$sk->id}";
                                        $val = isset($penilaians[$key]) ? (float) $penilaians[$key]->nilai : 2;
                                    @endphp
                                    <td class="px-2 py-2 text-center">
                                        <input type="number" step="0.01" min="0.01"
                                               name="nilai[{{ $sa->id }}][{{ $sk->id }}]"
                                               value="{{ rtrim(rtrim(number_format($val, 2, '.', ''), '0'), '.') }}"
                                               required
                                               class="w-20 text-center rounded border border-slate-300 px-2 py-1 font-mono text-xs focus:border-indigo-500 focus:outline-none">
                                    </td>
                                @endforeach
                                <td class="px-3 py-2 text-center">
                                    <button type="submit" form="remove-sa-{{ $sa->id }}"
                                            onclick="return confirm('Keluarkan {{ $sa->alternatif->nama }} dari sesi ini?')"
                                            class="text-rose-600 hover:text-rose-800 font-medium cursor-pointer">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-4 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
                <span class="text-xs text-slate-500">Pastikan nilai telah disimpan sebelum menjalankan perhitungan TOPSIS.</span>
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow cursor-pointer">
                    Simpan Matriks Nilai
                </button>
            </div>
        </form>

        @foreach ($sesiKeputusan->sesiAlternatifs as $sa)
            <form id="remove-sa-{{ $sa->id }}" action="{{ route('sesi-keputusans.alternatifs.remove', [$sesiKeputusan, $sa]) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    </div>
</div>
@endsection

