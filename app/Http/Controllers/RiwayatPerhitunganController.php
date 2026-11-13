<?php

namespace App\Http\Controllers;

use App\Models\RiwayatPerhitungan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RiwayatPerhitunganController extends Controller
{
    public function index(): View
    {
        $riwayats = RiwayatPerhitungan::with([
            'sesiKeputusan',
            'user',
            'hasilPerhitungans' => fn ($q) => $q->orderBy('ranking')->with('sesiAlternatif.alternatif'),
        ])
            ->withCount('hasilPerhitungans')
            ->latest('id')
            ->get();

        return view('riwayat_perhitungans.index', compact('riwayats'));
    }

    public function show(RiwayatPerhitungan $riwayatPerhitungan): View
    {
        $riwayatPerhitungan->load([
            'sesiKeputusan.sesiKriterias' => fn ($q) => $q->orderBy('id')->with('kriteria'),
            'sesiKeputusan.sesiAlternatifs' => fn ($q) => $q->orderBy('id')->with('alternatif'),
            'user',
            'idealPerhitungans.sesiKriteria.kriteria',
            'detailPerhitungans',
            'hasilPerhitungans' => fn ($q) => $q->orderBy('ranking')->with('sesiAlternatif.alternatif'),
        ]);

        $sesiKriterias = $riwayatPerhitungan->sesiKeputusan->sesiKriterias;
        $sesiAlternatifs = $riwayatPerhitungan->sesiKeputusan->sesiAlternatifs;
        $totalBobot = (float) $sesiKriterias->sum('bobot');

        $detailsByAltKrit = $riwayatPerhitungan->detailPerhitungans->keyBy(
            fn ($d): string => "{$d->sesi_alternatif_id}_{$d->sesi_kriterias_id}"
        );

        $idealsByKrit = $riwayatPerhitungan->idealPerhitungans->keyBy('sesi_kriterias_id');

        return view('riwayat_perhitungans.show', compact(
            'riwayatPerhitungan',
            'sesiKriterias',
            'sesiAlternatifs',
            'totalBobot',
            'detailsByAltKrit',
            'idealsByKrit'
        ));
    }

    public function destroy(RiwayatPerhitungan $riwayatPerhitungan): RedirectResponse
    {
        $riwayatPerhitungan->delete();

        return redirect()->route('riwayat-perhitungans.index')
            ->with('success', 'Riwayat perhitungan berhasil dihapus.');
    }
}

