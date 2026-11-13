<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AlternatifController extends Controller
{
    public function index(): View
    {
        $alternatifs = Alternatif::withCount('sesiAlternatifs')->orderBy('id')->paginate(15);
        $nextNumber = Alternatif::count() + 1;
        $suggestedCode = 'A' . $nextNumber;

        return view('alternatifs.index', compact('alternatifs', 'suggestedCode'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:45', 'unique:alternatifs,kode'],
            'nama' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        Alternatif::create($data);

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

