@extends('layouts.app')

@section('title', 'Master Alternatif Supplier - SPK TOPSIS Faency.id')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Data Master Alternatif Supplier</h1>
        <p class="text-xs text-slate-500">Daftar supplier pakaian thrift. Pengguna dapat menambah supplier baru secara dinamis kapan saja.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Tambah Alternatif -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm h-fit">
            <h2 class="font-semibold text-slate-900 mb-4">Tambah Alternatif Supplier</h2>
            <form action="{{ route('alternatifs.store') }}" method="POST" class="space-y-3.5 text-sm">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Kode Alternatif</label>
                    <input type="text" name="kode" value="{{ old('kode', $suggestedCode) }}" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-mono focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Nama Supplier / Toko</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Contoh: Thrift Nusantara" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Keterangan / Lokasi</label>
                    <textarea name="deskripsi" rows="3" placeholder="Catatan singkat atau lokasi supplier"
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">{{ old('deskripsi') }}</textarea>
                </div>
                <button type="submit"
                        class="w-full rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white py-2.5 text-sm font-semibold transition cursor-pointer">
                    + Tambah Alternatif
                </button>
            </form>
        </div>

        <!-- Daftar Alternatif -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                <h2 class="font-semibold text-slate-900">Daftar Alternatif ({{ $alternatifs->total() }})</h2>
                <a href="{{ route('sesi-keputusans.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                    Evaluasi di Sesi Keputusan &rarr;
                </a>
            </div>
            <div class="divide-y divide-slate-200">
                @forelse ($alternatifs as $alt)
                    <div class="p-4 hover:bg-slate-50/70">
                        <form action="{{ route('alternatifs.update', $alt) }}" method="POST" class="flex flex-col sm:flex-row gap-2.5 items-stretch sm:items-center">
                            @csrf
                            @method('PUT')
                            <input type="text" name="kode" value="{{ $alt->kode }}" required
                                   class="sm:w-20 rounded border border-slate-300 px-2.5 py-1.5 text-xs font-mono font-bold text-indigo-700">
                            <input type="text" name="nama" value="{{ $alt->nama }}" required
                                   class="sm:w-52 rounded border border-slate-300 px-2.5 py-1.5 text-xs font-medium text-slate-900">
                            <input type="text" name="deskripsi" value="{{ $alt->deskripsi }}" placeholder="Keterangan"
                                   class="flex-1 rounded border border-slate-300 px-2.5 py-1.5 text-xs text-slate-600">
                            <div class="flex gap-2 shrink-0">
                                <button type="submit"
                                        class="px-3 py-1.5 rounded bg-slate-800 hover:bg-slate-700 text-white text-xs font-medium cursor-pointer">
                                    Simpan
                                </button>
                                <button type="submit" form="delete-alt-{{ $alt->id }}"
                                        onclick="return confirm('Hapus alternatif {{ $alt->nama }}?')"
                                        class="px-3 py-1.5 rounded border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-medium cursor-pointer">
                                    Hapus
                                </button>
                            </div>
                        </form>
                        <form id="delete-alt-{{ $alt->id }}" action="{{ route('alternatifs.destroy', $alt) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-slate-500">Belum ada alternatif supplier.</div>
                @endforelse
            </div>
            @if ($alternatifs->hasPages())
                <div class="px-5 py-3 border-t border-slate-200 text-xs text-slate-500">
                    {{ $alternatifs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

