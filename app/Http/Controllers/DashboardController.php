<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\RiwayatPerhitungan;
use App\Models\SesiKeputusan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalKriteria = Kriteria::count();
        $totalAlternatif = Alternatif::count();
        $totalSesi = SesiKeputusan::count();
        $totalRiwayat = RiwayatPerhitungan::count();

        $latestRiwayat = RiwayatPerhitungan::with([
            'sesiKeputusan',
            'user',
            'hasilPerhitungans' => fn ($q) => $q->orderBy('ranking')->with('sesiAlternatif.alternatif'),
        ])->latest('id')->first();

        $recentSesis = SesiKeputusan::with(['user', 'latestRiwayat'])
            ->withCount(['sesiKriterias', 'sesiAlternatifs', 'riwayatPerhitungans'])
            ->latest('id')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalKriteria',
            'totalAlternatif',
            'totalSesi',
            'totalRiwayat',
            'latestRiwayat',
            'recentSesis'
        ));
    }

    public function proposal(): View
    {
        $kriterias = Kriteria::orderBy('id')->get();
        $alternatifs = Alternatif::orderBy('id')->get();
        $latestRiwayat = RiwayatPerhitungan::with([
            'hasilPerhitungans' => fn ($q) => $q->orderBy('ranking')->with('sesiAlternatif.alternatif'),
        ])->latest('id')->first();

        return view('proposal', compact('kriterias', 'alternatifs', 'latestRiwayat'));
    }
}

