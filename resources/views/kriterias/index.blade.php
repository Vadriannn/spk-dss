@extends('layouts.app')

@section('title', 'Master Kriteria - SPK TOPSIS Faency.id')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Data Master Kriteria</h1>
            <p class="text-xs text-slate-500">Kelola kriteria penilaian TOPSIS (benefit/cost) beserta bobot default-nya.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Tambah Kriteria -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm h-fit">
            <h2 class="font-semibold text-slate-900 mb-4">Tambah Kriteria Baru</h2>
            <form action="{{ route('kriterias.store') }}" method="POST" class="space-y-3.5 text-sm">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Kode Kriteria</label>
                    <input type="text" name="kode" value="{{ old('kode', 'C' . ($kriterias->count() + 1)) }}" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Nama Kriteria</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Contoh: Kecepatan Pengiriman" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Tipe Atribut</label>
                        <select name="enum" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                            <option value="benefit" {{ old('enum') === 'benefit' ? 'selected' : '' }}>Benefit (Makin tinggi makin baik)</option>
                            <option value="cost" {{ old('enum') === 'cost' ? 'selected' : '' }}>Cost (Makin rendah makin baik)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Bobot Default</label>
                        <input type="number" step="0.01" min="0.01" name="bobot_default" value="{{ old('bobot_default', 2) }}" required
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Deskripsi / Rentang Nilai</label>
                    <textarea name="deskripsi" rows="3" placeholder="Contoh: 1: Kurang, 2: Cukup, 3: Baik"
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">{{ old('deskripsi') }}</textarea>
                </div>
                <button type="submit"
                        class="w-full rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white py-2.5 text-sm font-semibold transition cursor-pointer">
                    + Tambah Kriteria
                </button>
            </form>
        </div>

        <!-- Daftar Kriteria -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                <h2 class="font-semibold text-slate-900">Daftar Kriteria ({{ $kriterias->count() }})</h2>
                <span class="text-xs text-slate-500">Total bobot default: {{ number_format($kriterias->sum('bobot_default'), 2) }}</span>
            </div>
            <div class="divide-y divide-slate-200">
                @forelse ($kriterias as $kriteria)
                    <div class="p-4 hover:bg-slate-50/70">
                        <form action="{{ route('kriterias.update', $kriteria) }}" method="POST" class="space-y-3">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                                <div class="sm:col-span-2">
                                    <label class="block text-[11px] font-medium text-slate-500 mb-1">Kode</label>
                                    <input type="text" name="kode" value="{{ $kriteria->kode }}" required
                                           class="w-full rounded border border-slate-300 px-2.5 py-1.5 text-xs font-mono font-bold">
                                </div>
                                <div class="sm:col-span-4">
                                    <label class="block text-[11px] font-medium text-slate-500 mb-1">Nama Kriteria</label>
                                    <input type="text" name="nama" value="{{ $kriteria->nama }}" required
                                           class="w-full rounded border border-slate-300 px-2.5 py-1.5 text-xs font-medium">
                                </div>
                                <div class="sm:col-span-3">
                                    <label class="block text-[11px] font-medium text-slate-500 mb-1">Tipe</label>
                                    <select name="enum" class="w-full rounded border border-slate-300 px-2 py-1.5 text-xs">
                                        <option value="benefit" {{ $kriteria->enum === 'benefit' ? 'selected' : '' }}>Benefit</option>
                                        <option value="cost" {{ $kriteria->enum === 'cost' ? 'selected' : '' }}>Cost</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-3">
                                    <label class="block text-[11px] font-medium text-slate-500 mb-1">Bobot Default</label>
                                    <input type="number" step="0.01" min="0.01" name="bobot_default" value="{{ $kriteria->bobot_default }}" required
                                           class="w-full rounded border border-slate-300 px-2.5 py-1.5 text-xs font-mono">
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-center">
                                <input type="text" name="deskripsi" value="{{ $kriteria->deskripsi }}" placeholder="Deskripsi rentang penilaian"
                                       class="flex-1 rounded border border-slate-300 px-2.5 py-1.5 text-xs text-slate-600">
                                <div class="flex gap-2 shrink-0">
                                    <button type="submit"
                                            class="px-3 py-1.5 rounded bg-slate-800 hover:bg-slate-700 text-white text-xs font-medium cursor-pointer">
                                        Simpan
                                    </button>
                                    <button type="submit" form="delete-kriteria-{{ $kriteria->id }}"
                                            onclick="return confirm('Hapus kriteria {{ $kriteria->kode }}?')"
                                            class="px-3 py-1.5 rounded border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-medium cursor-pointer">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </form>
                        <form id="delete-kriteria-{{ $kriteria->id }}" action="{{ route('kriterias.destroy', $kriteria) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-slate-500">Belum ada kriteria.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

