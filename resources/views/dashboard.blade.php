@extends('layouts.app')

@section('title', 'Tabel A - Nilai Alternatif terhadap Kriteria | SPK Faency.id')

@section('content')
<div class="stack">
    <!-- Hero Header -->
    <div class="hero">
        <div>
            <h1>Tabel A: Nilai Alternatif terhadap Kriteria</h1>
            <p>Kelola data alternatif supplier, bobot kriteria, dan matriks penilaian awal ($X_{ij}$) untuk studi kasus Faency.id.</p>
        </div>
        <div class="flex-row">
            <a href="{{ route('kriterias.index') }}" class="btn btn-outline">Kelola Kriteria &amp; Bobot</a>
            <a href="{{ route('alternatifs.index') }}" class="btn btn-primary">+ Kelola Alternatif</a>
        </div>
    </div>

    <!-- Ringkasan Data -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Jumlah Alternatif</div>
            <div class="stat-value">{{ $sesiAlternatifs->count() }}</div>
            <div class="stat-sub">Supplier pakaian thrift terdaftar</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Jumlah Kriteria</div>
            <div class="stat-value">{{ $sesiKriterias->count() }}</div>
            <div class="stat-sub">Total bobot: <strong class="mono">{{ number_format($totalBobot, 2) }}</strong></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Sel Penilaian (Tabel A)</div>
            <div class="stat-value">{{ $penilaians->count() }}</div>
            <div class="stat-sub">Dari {{ $sesiAlternatifs->count() * $sesiKriterias->count() }} kombinasi alternatif &times; kriteria</div>
        </div>
    </div>

    @if ($sesiKriterias->isEmpty() || $sesiAlternatifs->isEmpty())
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 2rem;">
                <h2 style="margin: 0 0 0.5rem; font-size: 1.05rem;">Belum Ada Data Kriteria atau Alternatif</h2>
                <p class="muted" style="margin-bottom: 1rem;">
                    Anda dapat menginput kriteria dan alternatif secara manual, atau memuat data awal studi kasus jurnal (6 Kriteria &amp; 21 Alternatif).
                </p>
                <form action="{{ route('tabel-a.seed') }}" method="POST" style="display: inline-block;">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        Muat Data Awal Studi Kasus (6 Kriteria &amp; 21 Alternatif)
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Ringkasan Bobot Kriteria -->
    @if ($sesiKriterias->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <div>
                    <strong style="font-size: 0.95rem;">Daftar Kriteria &amp; Bobot Aktif</strong>
                    <div class="muted">Ubah bobot atau tambah kriteria melalui menu <a href="{{ route('kriterias.index') }}" style="color:#4f46e5; text-decoration:underline;">Kriteria &amp; Bobot</a>.</div>
                </div>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama / Keterangan Kriteria</th>
                            <th class="text-center">Tipe</th>
                            <th class="text-right">Bobot</th>
                            <th class="text-right">Proporsi (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sesiKriterias as $sk)
                            <tr>
                                <td><span class="badge badge-code">{{ $sk->kriteria->kode }}</span></td>
                                <td>{{ $sk->kriteria->deskripsi }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $sk->tipe === 'cost' ? 'badge-cost' : 'badge-benefit' }}">
                                        {{ $sk->tipe }}
                                    </span>
                                </td>
                                <td class="text-right mono" style="font-weight: 700;">{{ rtrim(rtrim(number_format($sk->bobot, 4, '.', ''), '0'), '.') }}</td>
                                <td class="text-right mono muted">
                                    {{ $totalBobot > 0 ? number_format(($sk->bobot / $totalBobot) * 100, 2) : '0.00' }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Form Cepat: Tambah Alternatif + Input Nilainya ke Tabel A -->
    @if ($sesiKriterias->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <div>
                    <strong style="font-size: 0.95rem;">Tambah Alternatif Baru + Nilai ke Tabel A</strong>
                    <div class="muted">Tambahkan supplier baru beserta nilainya terhadap seluruh kriteria dalam satu langkah.</div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('tabel-a.quick-add') }}" method="POST" class="stack" style="gap: 0.85rem;">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="label">Kode Alternatif</label>
                            <input type="text" name="kode" value="{{ old('kode', $suggestedCode) }}" required class="input mono">
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label class="label">Nama Alternatif Supplier</label>
                            <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Contoh: Thrift Nusantara" required class="input">
                        </div>
                        <div class="form-group">
                            <label class="label">Keterangan (Opsional)</label>
                            <input type="text" name="deskripsi" value="{{ old('deskripsi') }}" placeholder="Catatan supplier" class="input">
                        </div>
                    </div>

                    <div>
                        <div class="label mb-2">Nilai terhadap Setiap Kriteria (Skala 1 - 3 atau angka bebas):</div>
                        <div class="form-grid">
                            @foreach ($sesiKriterias as $sk)
                                <div class="form-group">
                                    <label class="label muted">{{ $sk->kriteria->kode }} &mdash; {{ \Illuminate\Support\Str::limit($sk->kriteria->deskripsi, 28) }}</label>
                                    <input type="number" step="0.01" min="0.01" name="nilai[{{ $sk->id }}]" value="2" required class="input mono">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary">+ Tambah Alternatif ke Tabel A</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Tabel A: Matriks Nilai Alternatif terhadap Kriteria -->
    @if ($sesiAlternatifs->isNotEmpty() && $sesiKriterias->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <div>
                    <strong style="font-size: 0.95rem;">Tabel A &mdash; Matriks Nilai Alternatif terhadap Kriteria</strong>
                    <div class="muted">Edit nilai pada tabel di bawah ini lalu klik <strong>Simpan Nilai Tabel A</strong>.</div>
                </div>
            </div>

            <form action="{{ route('tabel-a.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 70px;">Kode</th>
                                <th style="min-width: 190px;">Nama Alternatif</th>
                                @foreach ($sesiKriterias as $sk)
                                    <th class="text-center" style="min-width: 95px;">
                                        <div class="mono" style="color: #4338ca;">{{ $sk->kriteria->kode }}</div>
                                        <div style="font-weight: 400; font-size: 0.68rem; color: #64748b;">
                                            Bobot: {{ rtrim(rtrim(number_format($sk->bobot, 2, '.', ''), '0'), '.') }} ({{ strtoupper($sk->tipe) }})
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sesiAlternatifs as $sa)
                                <tr>
                                    <td><span class="badge badge-code">{{ $sa->alternatif->kode }}</span></td>
                                    <td style="font-weight: 600; color: #0f172a;">{{ $sa->alternatif->nama }}</td>
                                    @foreach ($sesiKriterias as $sk)
                                        @php
                                            $key = "{$sa->id}_{$sk->id}";
                                            $val = isset($penilaians[$key]) ? (float) $penilaians[$key]->nilai : 2;
                                        @endphp
                                        <td class="text-center">
                                            <input type="number" step="0.01" min="0.01"
                                                   name="nilai[{{ $sa->id }}][{{ $sk->id }}]"
                                                   value="{{ rtrim(rtrim(number_format($val, 2, '.', ''), '0'), '.') }}"
                                                   required
                                                   class="input input-sm input-score">
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-header" style="background: #f8fafc; border-top: 1px solid #e2e8f0; border-bottom: none;">
                    <span class="muted">Menampilkan {{ $sesiAlternatifs->count() }} alternatif &times; {{ $sesiKriterias->count() }} kriteria.</span>
                    <button type="submit" class="btn btn-success">Simpan Nilai Tabel A</button>
                </div>
            </form>
        </div>
    @endif
</div>
@endsection
