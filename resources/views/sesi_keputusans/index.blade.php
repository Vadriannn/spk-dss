@extends('layouts.app')

@section('title', 'Sesi Keputusan - SPK TOPSIS Faency.id')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Sesi Keputusan &amp; Perhitungan TOPSIS</h1>
        <p class="text-xs text-slate-500">Buat sesi baru atau buka sesi yang ada untuk menambah alternatif, mengubah bobot/nilai, dan menjalankan TOPSIS.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Create New Session -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm h-fit">
            <h2 class="font-semibold text-slate-900 mb-4">Buat Sesi Keputusan Baru</h2>
            <form action="{{ route('sesi-keputusans.store') }}" method="POST" class="space-y-3.5 text-sm">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Nama Sesi</label>
                    <input type="text" name="nama" required placeholder="Contoh: Seleksi Supplier Q4 2026"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="2" placeholder="Tujuan evaluasi supplier pada sesi ini"
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Sertakan Kriteria (kosongkan = semua)</label>
                    <div class="max-h-32 overflow-y-auto border border-slate-200 rounded-lg p-2.5 space-y-1.5 text-xs">
                        @foreach ($kriterias as $k)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="kriteria_ids[]" value="{{ $k->id }}" checked class="rounded text-indigo-600">
                                <span><strong>{{ $k->kode }}</strong> - {{ $k->nama }} ({{ ucfirst($k->enum) }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Sertakan Alternatif Awal (kosongkan = semua)</label>
                    <div class="max-h-40 overflow-y-auto border border-slate-200 rounded-lg p-2.5 space-y-1.5 text-xs">
                        @foreach ($alternatifs as $a)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="alternatif_ids[]" value="{{ $a->id }}" checked class="rounded text-indigo-600">
                                <span><strong>{{ $a->kode }}</strong> - {{ $a->nama }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <button type="submit"
                        class="w-full rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white py-2.5 text-sm font-semibold transition cursor-pointer">
                    + Buat Sesi &amp; Buka Matriks
                </button>
            </form>
        </div>

        <!-- Session List -->
        <div class="lg:col-span-2 space-y-4">
            @forelse ($sesis as $sesi)
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-slate-900">{{ $sesi->nama }}</h3>
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold
                                    {{ $sesi->status === 'selesai' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ ucfirst($sesi->status) }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-600">{{ $sesi->deskripsi }}</p>
                            <div class="flex flex-wrap gap-3 pt-1 text-xs text-slate-500">
                                <span>Oleh: <strong>{{ $sesi->user?->nama ?? '-' }}</strong></span>
                                <span>&bull; {{ $sesi->sesi_kriterias_count }} Kriteria</span>
                                <span>&bull; {{ $sesi->sesi_alternatifs_count }} Alternatif</span>
                                <span>&bull; {{ $sesi->riwayat_perhitungans_count }} Riwayat Hitung</span>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 shrink-0">
                            <a href="{{ route('sesi-keputusans.show', $sesi) }}"
                               class="px-3.5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold transition">
                                Kelola &amp; Hitung
                            </a>
                            @if ($sesi->latestRiwayat)
                                <a href="{{ route('riwayat-perhitungans.show', $sesi->latestRiwayat) }}"
                                   class="px-3.5 py-2 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium transition">
                                    Hasil Terakhir
                                </a>
                            @endif
                            <form action="{{ route('sesi-keputusans.destroy', $sesi) }}" method="POST"
                                  onsubmit="return confirm('Hapus sesi keputusan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-2 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-medium cursor-pointer">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>

                    @if ($sesi->latestRiwayat && $sesi->latestRiwayat->hasilPerhitungans->isNotEmpty())
                        @php $top = $sesi->latestRiwayat->hasilPerhitungans->first(); @endphp
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-slate-600">
                                Peringkat #1 Terakhir:
                                <strong class="text-emerald-700">{{ $top->sesiAlternatif->alternatif->nama }} ({{ $top->sesiAlternatif->alternatif->kode }})</strong>
                            </span>
                            <span class="font-mono font-bold text-indigo-700">V = {{ number_format($top->nilai_preferensi, 4) }}</span>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-xl border border-slate-200 p-8 text-center text-sm text-slate-500">
                    Belum ada sesi keputusan. Silakan buat sesi baru di sebelah kiri.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

