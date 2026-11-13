<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Penilaian;
use App\Models\SesiAlternatif;
use App\Models\SesiKeputusan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AlternatifController extends Controller
{
    public function index(): View
    {
        $sesi = SesiKeputusan::current();
        $sesiKriterias = $sesi->sesiKriterias()->with('kriteria')->orderBy('id')->get();
        $alternatifs = Alternatif::orderBy('id')->get();
        $suggestedCode = 'A' . ($alternatifs->count() + 1);

        return view('alternatifs.index', compact('alternatifs', 'sesiKriterias', 'suggestedCode'));
    }

    public function store(Request $request): RedirectResponse
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

        return redirect()->route('alternatifs.index')
            ->with('success', 'Alternatif ' . $data['nama'] . ' (' . $data['kode'] . ') berhasil ditambahkan.');
    }

    public function update(Request $request, Alternatif $alternatif): RedirectResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:45', Rule::unique('alternatifs', 'kode')->ignore($alternatif->id)],
            'nama' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $alternatif->update($data);

        return redirect()->route('alternatifs.index')
            ->with('success', 'Alternatif ' . $alternatif->nama . ' berhasil diperbarui.');
    }

    public function destroy(Alternatif $alternatif): RedirectResponse
    {
        $nama = $alternatif->nama;
        $alternatif->delete();

        return redirect()->route('alternatifs.index')
            ->with('success', 'Alternatif ' . $nama . ' berhasil dihapus.');
    }
}
