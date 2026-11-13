@extends('layouts.app')

@section('title', 'Input Kriteria & Bobot - SPK Faency.id')

@section('content')
<div class="stack">
    <div class="flex-row justify-between">
        <div>
            <h1 style="margin: 0; font-size: 1.25rem;">Input Kriteria &amp; Bobot Kriteria</h1>
            <p class="muted" style="margin: 0.2rem 0 0;">Tambah, ubah, atau hapus kriteria penilaian beserta nilai bobotnya (tersimpan di tabel `kriterias` &amp; `sesi_kriterias`).</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline">Kembali ke Tabel A &rarr;</a>
    </div>

    <div class="grid-2col">
        <!-- Form Tambah Kriteria & Bobot -->
        <div class="card">
            <div class="card-header">
                <strong style="font-size: 0.95rem;">Tambah Kriteria &amp; Bobot Baru</strong>
            </div>
            <div class="card-body">
                <form action="{{ route('kriterias.store') }}" method="POST" class="stack" style="gap: 0.85rem;">
                    @csrf
                    <div class="form-group">
                        <label class="label">Kode Kriteria</label>
                        <input type="text" name="kode" value="{{ old('kode', $suggestedCode) }}" required class="input mono">
                    </div>
                    <div class="form-group">
                        <label class="label">Nama / Deskripsi Kriteria</label>
                        <textarea name="deskripsi" rows="3" placeholder="Contoh: Kecepatan Pengiriman (1: Lambat, 2: Sedang, 3: Cepat)" required class="textarea">{{ old('deskripsi') }}</textarea>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="label">Tipe Atribut</label>
                            <select name="enum" class="select">
                                <option value="benefit" {{ old('enum') === 'benefit' ? 'selected' : '' }}>Benefit</option>
                                <option value="cost" {{ old('enum') === 'cost' ? 'selected' : '' }}>Cost</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="label">Bobot Kriteria</label>
                            <input type="number" step="0.01" min="0.01" name="bobot" value="{{ old('bobot', 2) }}" required class="input mono">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">+ Tambah Kriteria &amp; Bobot</button>
                </form>
            </div>
        </div>

        <!-- Daftar Kriteria & Bobot -->
        <div class="card">
            <div class="card-header">
                <strong style="font-size: 0.95rem;">Daftar Kriteria &amp; Bobot ({{ $sesiKriterias->count() }})</strong>
                <span class="muted">Total Bobot: <strong class="mono">{{ number_format($totalBobot, 2) }}</strong></span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 85px;">Kode</th>
                            <th>Nama / Deskripsi Kriteria</th>
                            <th style="width: 110px;">Tipe</th>
                            <th style="width: 100px;">Bobot</th>
                            <th style="width: 145px;" class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sesiKriterias as $sk)
                            @php $kriteria = $sk->kriteria; @endphp
                            <tr>
                                <td>
                                    <form id="update-kriteria-{{ $kriteria->id }}" action="{{ route('kriterias.update', $kriteria) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                    <input form="update-kriteria-{{ $kriteria->id }}" type="text" name="kode" value="{{ $kriteria->kode }}" required class="input input-sm mono" style="font-weight:700;">
                                </td>
                                <td>
                                    <input form="update-kriteria-{{ $kriteria->id }}" type="text" name="deskripsi" value="{{ $kriteria->deskripsi }}" required class="input input-sm">
                                </td>
                                <td>
                                    <select form="update-kriteria-{{ $kriteria->id }}" name="enum" class="select input-sm">
                                        <option value="benefit" {{ $kriteria->enum === 'benefit' ? 'selected' : '' }}>Benefit</option>
                                        <option value="cost" {{ $kriteria->enum === 'cost' ? 'selected' : '' }}>Cost</option>
                                    </select>
                                </td>
                                <td>
                                    <input form="update-kriteria-{{ $kriteria->id }}" type="number" step="0.01" min="0.01" name="bobot" value="{{ rtrim(rtrim(number_format($sk->bobot, 4, '.', ''), '0'), '.') }}" required class="input input-sm mono">
                                </td>
                                <td class="text-right" style="white-space: nowrap;">
                                    <button form="update-kriteria-{{ $kriteria->id }}" type="submit" class="btn btn-dark btn-sm">Simpan</button>
                                    <form action="{{ route('kriterias.destroy', $kriteria) }}" method="POST" style="display:inline;"
                                          onsubmit="return confirm('Hapus kriteria {{ $kriteria->kode }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center muted" style="padding: 1.5rem;">Belum ada kriteria. Tambahkan kriteria di sebelah kiri.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
