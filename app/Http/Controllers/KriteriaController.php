<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\SesiKeputusan;
use App\Models\SesiKriteria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KriteriaController extends Controller
{
    public function index(): View
    {
        $sesi = SesiKeputusan::current();
        $sesiKriterias = $sesi->sesiKriterias()->with('kriteria')->orderBy('id')->get();
        $totalBobot = (float) $sesiKriterias->sum('bobot');
        $suggestedCode = 'C' . ($sesiKriterias->count() + 1);

        return view('kriterias.index', compact('sesi', 'sesiKriterias', 'totalBobot', 'suggestedCode'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:45', 'unique:kriterias,kode'],
            'deskripsi' => ['required', 'string', 'max:500'],
            'enum' => ['required', Rule::in(['benefit', 'cost'])],
            'bobot' => ['required', 'numeric', 'min:0.01'],
        ]);

        $sesi = SesiKeputusan::current();

        DB::transaction(function () use ($data, $sesi): void {
            $kriteria = Kriteria::create([
                'kode' => $data['kode'],
                'deskripsi' => $data['deskripsi'],
                'enum' => $data['enum'],
            ]);

            $sk = SesiKriteria::create([
                'bobot' => $data['bobot'],
                'tipe' => $data['enum'],
                'sesi_keputusans_id' => $sesi->id,
                'kriterias_id' => $kriteria->id,
            ]);

            foreach ($sesi->sesiAlternatifs as $sa) {
                Penilaian::firstOrCreate(
                    [
                        'sesi_kriterias_id' => $sk->id,
                        'sesi_alternatif_id' => $sa->id,
                    ],
                    ['nilai' => 2]
                );
            }
        });

        return redirect()->route('kriterias.index')
            ->with('success', 'Kriteria ' . $data['kode'] . ' beserta bobotnya berhasil ditambahkan.');
    }

    public function update(Request $request, Kriteria $kriteria): RedirectResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:45', Rule::unique('kriterias', 'kode')->ignore($kriteria->id)],
            'deskripsi' => ['required', 'string', 'max:500'],
            'enum' => ['required', Rule::in(['benefit', 'cost'])],
            'bobot' => ['required', 'numeric', 'min:0.01'],
        ]);

        $sesi = SesiKeputusan::current();

        DB::transaction(function () use ($data, $kriteria, $sesi): void {
            $kriteria->update([
                'kode' => $data['kode'],
                'deskripsi' => $data['deskripsi'],
                'enum' => $data['enum'],
            ]);

            SesiKriteria::updateOrCreate(
                [
                    'sesi_keputusans_id' => $sesi->id,
                    'kriterias_id' => $kriteria->id,
                ],
                [
                    'bobot' => $data['bobot'],
                    'tipe' => $data['enum'],
                ]
            );
        });

        return redirect()->route('kriterias.index')
            ->with('success', 'Kriteria ' . $kriteria->kode . ' & bobotnya berhasil diperbarui.');
    }

    public function destroy(Kriteria $kriteria): RedirectResponse
    {
        $kode = $kriteria->kode;
        $kriteria->delete();

        return redirect()->route('kriterias.index')
            ->with('success', 'Kriteria ' . $kode . ' berhasil dihapus.');
    }
}
