@extends('layouts.app')

@section('title', 'Riwayat Perhitungan TOPSIS - Faency.id')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Riwayat Perhitungan TOPSIS</h1>
        <p class="text-xs text-slate-500">Setiap kali perhitungan dijalankan, seluruh matriks ($X, R, Y$), solusi ideal ($A^+, A^-$), dan peringkat tersimpan di basis data.</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-600 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Waktu</th>
                        <th class="px-4 py-3 text-left">Sesi Keputusan</th>
                        <th class="px-4 py-3 text-left">Oleh</th>
                        <th class="px-4 py-3 text-center">Jml Alternatif</th>
                        <th class="px-4 py-3 text-left">Peringkat #1</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($riwayats as $r)
                        @php $top = $r->hasilPerhitungans->first(); @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-xs text-slate-600 whitespace-nowrap">{{ $r->waktu->format('d M Y H:i:s') }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $r->sesiKeputusan->nama }}</td>
                            <td class="px-4 py-3 text-xs text-slate-600">{{ $r->user?->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-center font-mono text-xs">{{ $r->hasil_perhitungans_count }}</td>
                            <td class="px-4 py-3 text-xs">
                                @if ($top)
                                    <span class="font-semibold text-emerald-700">{{ $top->sesiAlternatif->alternatif->nama }}</span>
                                    <span class="ml-1 font-mono text-indigo-700">(V = {{ number_format($top->nilai_preferensi, 4) }})</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap space-x-2">
                                <a href="{{ route('riwayat-perhitungans.show', $r) }}"
                                   class="inline-block px-3 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold">
                                    Lihat Detail
                                </a>
                                <form action="{{ route('riwayat-perhitungans.destroy', $r) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Hapus riwayat perhitungan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 rounded border border-rose-200 bg-rose-50 text-rose-700 text-xs cursor-pointer">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">Belum ada riwayat perhitungan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($riwayats->hasPages())
            <div class="px-5 py-3 border-t border-slate-200 text-xs text-slate-500">
                {{ $riwayats->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

