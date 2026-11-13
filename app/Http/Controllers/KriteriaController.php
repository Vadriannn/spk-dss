<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KriteriaController extends Controller
{
    public function index(): View
    {
        $kriterias = Kriteria::orderBy('id')->get();

        return view('kriterias.index', compact('kriterias'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:45', 'unique:kriterias,kode'],
            'nama' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'enum' => ['required', Rule::in(['benefit', 'cost'])],
            'bobot_default' => ['required', 'numeric', 'min:0.01'],
        ]);

        Kriteria::create($data);

        return redirect()->route('kriterias.index')
            ->with('success', 'Kriteria ' . $data['kode'] . ' berhasil ditambahkan.');
    }

    public function update(Request $request, Kriteria $kriteria): RedirectResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:45', Rule::unique('kriterias', 'kode')->ignore($kriteria->id)],
            'nama' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'enum' => ['required', Rule::in(['benefit', 'cost'])],
            'bobot_default' => ['required', 'numeric', 'min:0.01'],
        ]);

        $kriteria->update($data);

        return redirect()->route('kriterias.index')
            ->with('success', 'Kriteria ' . $kriteria->kode . ' berhasil diperbarui.');
    }

    public function destroy(Kriteria $kriteria): RedirectResponse
    {
        $kode = $kriteria->kode;
        $kriteria->delete();

        return redirect()->route('kriterias.index')
            ->with('success', 'Kriteria ' . $kode . ' berhasil dihapus.');
    }
}

