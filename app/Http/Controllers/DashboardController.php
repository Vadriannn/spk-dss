<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Penilaian;
use App\Models\SesiAlternatif;
use App\Models\SesiKeputusan;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $sesi = SesiKeputusan::current();
        $sesiKriterias = $sesi->sesiKriterias()->with('kriteria')->orderBy('id')->get();
        $sesiAlternatifs = $sesi->sesiAlternatifs()->with('alternatif')->orderBy('id')->get();

        $penilaians = Penilaian::query()
            ->whereIn('sesi_alternatif_id', $sesiAlternatifs->pluck('id'))
            ->whereIn('sesi_kriterias_id', $sesiKriterias->pluck('id'))
            ->get()
            ->keyBy(fn (Penilaian $p): string => "{$p->sesi_alternatif_id}_{$p->sesi_kriterias_id}");

        $totalBobot = (float) $sesiKriterias->sum('bobot');
        $suggestedCode = 'A' . (Alternatif::count() + 1);

        return view('dashboard', compact(
            'sesi',
            'sesiKriterias',
            'sesiAlternatifs',
            'penilaians',
            'totalBobot',
            'suggestedCode'
        ));
    }

    public function updateTabelA(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nilai' => ['required', 'array'],
            'nilai.*' => ['required', 'array'],
            'nilai.*.*' => ['required', 'numeric', 'min:0.01'],
        ]);

        $sesi = SesiKeputusan::current();
        $validAltIds = $sesi->sesiAlternatifs()->pluck('id')->flip();
        $validKritIds = $sesi->sesiKriterias()->pluck('id')->flip();

        DB::transaction(function () use ($data, $validAltIds, $validKritIds): void {
            foreach ($data['nilai'] as $saId => $kriteriaValues) {
                if (! isset($validAltIds[(int) $saId])) {
                    continue;
                }

                foreach ($kriteriaValues as $skId => $val) {
                    if (! isset($validKritIds[(int) $skId])) {
                        continue;
                    }

                    Penilaian::updateOrCreate(
                        [
                            'sesi_alternatif_id' => (int) $saId,
                            'sesi_kriterias_id' => (int) $skId,
                        ],
                        ['nilai' => (float) $val]
                    );
                }
            }
        });

        return redirect()->route('dashboard')
            ->with('success', 'Nilai alternatif terhadap kriteria (Tabel A) berhasil disimpan.');
    }

    public function quickAddAlternatif(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:45', 'unique:alternatifs,kode'],
            'nama' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'nilai' => ['nullable', 'array'],
            'nilai.*' => ['required', 'numeric', 'min:0.01'],
        ]);

        $sesi = SesiKeputusan::current();
        $sesiKriterias = $sesi->sesiKriterias()->orderBy('id')->get();

        DB::transaction(function () use ($data, $sesi, $sesiKriterias): void {
            $alternatif = Alternatif::create([
                'kode' => $data['kode'],
                'nama' => $data['nama'],
                'deskripsi' => $data['deskripsi'] ?? null,
            ]);

            $sa = SesiAlternatif::create([
                'sesi_keputusans_id' => $sesi->id,
                'alternatifs_id' => $alternatif->id,
            ]);

            foreach ($sesiKriterias as $sk) {
                Penilaian::create([
                    'nilai' => $data['nilai'][$sk->id] ?? 2,
                    'sesi_kriterias_id' => $sk->id,
                    'sesi_alternatif_id' => $sa->id,
                ]);
            }
        });

        return redirect()->route('dashboard')
            ->with('success', 'Alternatif ' . $data['nama'] . ' (' . $data['kode'] . ') berhasil ditambahkan ke Tabel A.');
    }

    public function seedInitialData(): RedirectResponse
    {
        app(DatabaseSeeder::class)->run();

        return redirect()->route('dashboard')
            ->with('success', 'Data awal studi kasus (6 Kriteria, Bobot, 21 Alternatif, dan Tabel A) berhasil dimuat.');
    }
}
