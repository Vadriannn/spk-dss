@extends('layouts.app')

@section('title', 'Input Alternatif Supplier - SPK Faency.id')

@section('content')
<div class="stack">
    <div class="flex-row justify-between">
        <div>
            <h1 style="margin: 0; font-size: 1.25rem;">Input Alternatif Supplier</h1>
            <p class="muted" style="margin: 0.2rem 0 0;">Tambah, ubah, atau hapus daftar alternatif supplier pakaian thrift (tersimpan di `alternatifs` &amp; `sesi_alternatifs`).</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline">Lihat &amp; Edit Tabel A &rarr;</a>
    </div>

    <div class="grid-2col">
        <!-- Form Tambah Alternatif -->
        <div class="card">
            <div class="card-header">
                <strong style="font-size: 0.95rem;">Tambah Alternatif Supplier</strong>
            </div>
            <div class="card-body">
                <form action="{{ route('alternatifs.store') }}" method="POST" class="stack" style="gap: 0.85rem;">
                    @csrf
                    <div class="form-group">
                        <label class="label">Kode Alternatif</label>
                        <input type="text" name="kode" value="{{ old('kode', $suggestedCode) }}" required class="input mono">
                    </div>
                    <div class="form-group">
                        <label class="label">Nama Supplier / Toko</label>
                        <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Contoh: Thrift Jaya Abadi" required class="input">
                    </div>
                    <div class="form-group">
                        <label class="label">Keterangan / Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" rows="2" placeholder="Catatan singkat supplier" class="textarea">{{ old('deskripsi') }}</textarea>
                    </div>

                    @if ($sesiKriterias->isNotEmpty())
                        <div>
                            <div class="label mb-2">Nilai Awal terhadap Kriteria (Opsional, default 2):</div>
                            <div class="form-grid">
                                @foreach ($sesiKriterias as $sk)
                                    <div class="form-group">
                                        <label class="label muted">{{ $sk->kriteria->kode }}</label>
                                        <input type="number" step="0.01" min="0.01" name="nilai[{{ $sk->id }}]" value="2" required class="input input-sm mono">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary btn-block">+ Tambah Alternatif</button>
                </form>
            </div>
        </div>

        <!-- Daftar Alternatif -->
        <div class="card">
            <div class="card-header">
                <strong style="font-size: 0.95rem;">Daftar Alternatif Terdaftar ({{ $alternatifs->count() }})</strong>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 85px;">Kode</th>
                            <th style="width: 210px;">Nama Supplier</th>
                            <th>Keterangan</th>
                            <th style="width: 145px;" class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($alternatifs as $alt)
                            <tr>
                                <td>
                                    <form id="update-alt-{{ $alt->id }}" action="{{ route('alternatifs.update', $alt) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                    <input form="update-alt-{{ $alt->id }}" type="text" name="kode" value="{{ $alt->kode }}" required class="input input-sm mono" style="font-weight:700;">
                                </td>
                                <td>
                                    <input form="update-alt-{{ $alt->id }}" type="text" name="nama" value="{{ $alt->nama }}" required class="input input-sm">
                                </td>
                                <td>
                                    <input form="update-alt-{{ $alt->id }}" type="text" name="deskripsi" value="{{ $alt->deskripsi }}" placeholder="Keterangan" class="input input-sm">
                                </td>
                                <td class="text-right" style="white-space: nowrap;">
                                    <button form="update-alt-{{ $alt->id }}" type="submit" class="btn btn-dark btn-sm">Simpan</button>
                                    <form action="{{ route('alternatifs.destroy', $alt) }}" method="POST" style="display:inline;"
                                          onsubmit="return confirm('Hapus alternatif {{ $alt->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center muted" style="padding: 1.5rem;">Belum ada alternatif supplier.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
