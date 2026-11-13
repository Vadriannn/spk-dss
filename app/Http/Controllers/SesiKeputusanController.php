<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\SesiAlternatif;
use App\Models\SesiKeputusan;
use App\Models\SesiKriteria;
use App\Models\User;
use App\Services\TopsisService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SesiKeputusanController extends Controller
{
    public function index(): View
    {
        $sesis = SesiKeputusan::with(['user', 'latestRiwayat.hasilPerhitungans' => fn ($q) => $q->orderBy('ranking')->with('sesiAlternatif.alternatif')])
            ->withCount(['sesiKriterias', 'sesiAlternatifs', 'riwayatPerhitungans'])
            ->latest('id')
            ->get();

        $kriterias = Kriteria::orderBy('id')->get();
        $alternatifs = Alternatif::orderBy('id')->get();

        return view('sesi_keputusans.index', compact('sesis', 'kriterias', 'alternatifs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'kriteria_ids' => ['nullable', 'array'],
            'kriteria_ids.*' => ['integer', 'exists:kriterias,id'],
            'alternatif_ids' => ['nullable', 'array'],
            'alternatif_ids.*' => ['integer', 'exists:alternatifs,id'],
        ]);

        $userId = Auth::id() ?? User::first()?->id;
        if (! $userId) {
            $userId = User::create([
                'nama' => 'Admin Faency.id',
                'email' => 'admin@faency.id',
                'password' => bcrypt('password'),
            ])->id;
        }

        $sesi = DB::transaction(function () use ($data, $userId): SesiKeputusan {
            $sesi = SesiKeputusan::create([
                'nama' => $data['nama'],
                'deskripsi' => $data['deskripsi'] ?? null,
                'status' => 'aktif',
                'users_id' => $userId,
            ]);

            $kriterias = ! empty($data['kriteria_ids'])
                ? Kriteria::whereIn('id', $data['kriteria_ids'])->orderBy('id')->get()
                : Kriteria::orderBy('id')->get();

            $sesiKriterias = [];
            foreach ($kriterias as $kriteria) {
                $sesiKriterias[] = SesiKriteria::create([
                    'bobot' => $kriteria->bobot_default,
                    'tipe' => $kriteria->enum,
                    'sesi_keputusans_id' => $sesi->id,
                    'kriterias_id' => $kriteria->id,
                ]);
            }

            $alternatifs = ! empty($data['alternatif_ids'])
                ? Alternatif::whereIn('id', $data['alternatif_ids'])->orderBy('id')->get()
                : Alternatif::orderBy('id')->get();

            foreach ($alternatifs as $alternatif) {
                $sa = SesiAlternatif::create([
                    'sesi_keputusans_id' => $sesi->id,
                    'alternatifs_id' => $alternatif->id,
                ]);

                foreach ($sesiKriterias as $sk) {
                    Penilaian::create([
                        'nilai' => 2,
                        'sesi_kriterias_id' => $sk->id,
                        'sesi_alternatif_id' => $sa->id,
                    ]);
                }
            }

            return $sesi;
        });

        return redirect()->route('sesi-keputusans.show', $sesi)
            ->with('success', 'Sesi keputusan baru berhasil dibuat. Silakan sesuaikan bobot dan nilai penilaian alternatif.');
    }

    public function show(SesiKeputusan $sesiKeputusan): View
    {
        $sesiKeputusan->load([
            'user',
            'sesiKriterias' => fn ($q) => $q->orderBy('id')->with('kriteria'),
            'sesiAlternatifs' => fn ($q) => $q->orderBy('id')->with('alternatif'),
            'riwayatPerhitungans' => fn ($q) => $q->latest('id')->with('user'),
        ]);

        $penilaians = Penilaian::query()
            ->whereIn('sesi_alternatif_id', $sesiKeputusan->sesiAlternatifs->pluck('id'))
            ->whereIn('sesi_kriterias_id', $sesiKeputusan->sesiKriterias->pluck('id'))
            ->get()
            ->keyBy(fn (Penilaian $p): string => "{$p->sesi_alternatif_id}_{$p->sesi_kriterias_id}");

        $existingKriteriaIds = $sesiKeputusan->sesiKriterias->pluck('kriterias_id');
        $availableKriterias = Kriteria::whereNotIn('id', $existingKriteriaIds)->orderBy('id')->get();

        $existingAlternatifIds = $sesiKeputusan->sesiAlternatifs->pluck('alternatifs_id');
        $availableAlternatifs = Alternatif::whereNotIn('id', $existingAlternatifIds)->orderBy('id')->get();

        $suggestedCode = 'A' . (Alternatif::count() + 1);

        $latestRiwayat = $sesiKeputusan->riwayatPerhitungans->first();
        if ($latestRiwayat) {
            $latestRiwayat->load([
                'hasilPerhitungans' => fn ($q) => $q->orderBy('ranking')->with('sesiAlternatif.alternatif'),
            ]);
        }

        return view('sesi_keputusans.show', compact(
            'sesiKeputusan',
            'penilaians',
            'availableKriterias',
            'availableAlternatifs',
            'suggestedCode',
            'latestRiwayat'
        ));
    }

    public function update(Request $request, SesiKeputusan $sesiKeputusan): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'aktif', 'selesai'])],
        ]);

        $sesiKeputusan->update($data);

        return redirect()->route('sesi-keputusans.show', $sesiKeputusan)
            ->with('success', 'Informasi sesi keputusan berhasil diperbarui.');
    }

    public function updateKriterias(Request $request, SesiKeputusan $sesiKeputusan): RedirectResponse
    {
        $data = $request->validate([
            'kriterias' => ['required', 'array'],
            'kriterias.*.bobot' => ['required', 'numeric', 'min:0.01'],
            'kriterias.*.tipe' => ['required', Rule::in(['benefit', 'cost'])],
        ]);

        foreach ($data['kriterias'] as $sesiKriteriaId => $row) {
            SesiKriteria::where('id', $sesiKriteriaId)
                ->where('sesi_keputusans_id', $sesiKeputusan->id)
                ->update([
                    'bobot' => $row['bobot'],
                    'tipe' => $row['tipe'],
                ]);
        }

        return redirect()->route('sesi-keputusans.show', $sesiKeputusan)
            ->with('success', 'Bobot dan tipe kriteria pada sesi ini berhasil diperbarui.');
    }

    public function addKriteria(Request $request, SesiKeputusan $sesiKeputusan): RedirectResponse
    {
        $data = $request->validate([
            'kriterias_id' => ['required', 'integer', 'exists:kriterias,id'],
            'bobot' => ['nullable', 'numeric', 'min:0.01'],
            'tipe' => ['nullable', Rule::in(['benefit', 'cost'])],
        ]);

        $kriteria = Kriteria::findOrFail($data['kriterias_id']);

        $exists = SesiKriteria::where('sesi_keputusans_id', $sesiKeputusan->id)
            ->where('kriterias_id', $kriteria->id)
            ->exists();

        if (! $exists) {
            $sk = SesiKriteria::create([
                'bobot' => $data['bobot'] ?? $kriteria->bobot_default,
                'tipe' => $data['tipe'] ?? $kriteria->enum,
                'sesi_keputusans_id' => $sesiKeputusan->id,
                'kriterias_id' => $kriteria->id,
            ]);

            foreach ($sesiKeputusan->sesiAlternatifs as $sa) {
                Penilaian::firstOrCreate(
                    [
                        'sesi_kriterias_id' => $sk->id,
                        'sesi_alternatif_id' => $sa->id,
                    ],
                    ['nilai' => 2]
                );
            }
        }

        return redirect()->route('sesi-keputusans.show', $sesiKeputusan)
            ->with('success', 'Kriteria ' . $kriteria->kode . ' berhasil ditambahkan ke sesi.');
    }

    public function removeKriteria(SesiKeputusan $sesiKeputusan, SesiKriteria $sesiKriteria): RedirectResponse
    {
        if ($sesiKriteria->sesi_keputusans_id === $sesiKeputusan->id) {
            $sesiKriteria->delete();
        }

        return redirect()->route('sesi-keputusans.show', $sesiKeputusan)
            ->with('success', 'Kriteria berhasil dihapus dari sesi ini.');
    }

    public function addExistingAlternatif(Request $request, SesiKeputusan $sesiKeputusan): RedirectResponse
    {
        $data = $request->validate([
            'alternatifs_id' => ['required', 'integer', 'exists:alternatifs,id'],
            'nilai' => ['nullable', 'array'],
            'nilai.*' => ['required', 'numeric', 'min:0.0001'],
        ]);

        $alternatif = Alternatif::findOrFail($data['alternatifs_id']);

        $sa = SesiAlternatif::firstOrCreate([
            'sesi_keputusans_id' => $sesiKeputusan->id,
            'alternatifs_id' => $alternatif->id,
        ]);

        foreach ($sesiKeputusan->sesiKriterias as $sk) {
            $val = $data['nilai'][$sk->id] ?? 2;
            Penilaian::updateOrCreate(
                [
                    'sesi_kriterias_id' => $sk->id,
                    'sesi_alternatif_id' => $sa->id,
                ],
                ['nilai' => $val]
            );
        }

        return redirect()->route('sesi-keputusans.show', $sesiKeputusan)
            ->with('success', 'Alternatif ' . $alternatif->nama . ' berhasil ditambahkan ke sesi beserta nilainya.');
    }

    public function addNewAlternatif(Request $request, SesiKeputusan $sesiKeputusan): RedirectResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:45', 'unique:alternatifs,kode'],
            'nama' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'nilai' => ['required', 'array'],
            'nilai.*' => ['required', 'numeric', 'min:0.0001'],
        ]);

        DB::transaction(function () use ($data, $sesiKeputusan): void {
            $alternatif = Alternatif::create([
                'kode' => $data['kode'],
                'nama' => $data['nama'],
                'deskripsi' => $data['deskripsi'] ?? null,
            ]);

            $sa = SesiAlternatif::create([
                'sesi_keputusans_id' => $sesiKeputusan->id,
                'alternatifs_id' => $alternatif->id,
            ]);

            foreach ($sesiKeputusan->sesiKriterias as $sk) {
                Penilaian::create([
                    'nilai' => $data['nilai'][$sk->id] ?? 2,
                    'sesi_kriterias_id' => $sk->id,
                    'sesi_alternatif_id' => $sa->id,
                ]);
            }
        });

        return redirect()->route('sesi-keputusans.show', $sesiKeputusan)
            ->with('success', 'Alternatif baru ' . $data['nama'] . ' (' . $data['kode'] . ') berhasil dibuat dan dinilai pada sesi ini!');
    }

    public function removeAlternatif(SesiKeputusan $sesiKeputusan, SesiAlternatif $sesiAlternatif): RedirectResponse
    {
        if ($sesiAlternatif->sesi_keputusans_id === $sesiKeputusan->id) {
            $sesiAlternatif->delete();
        }

        return redirect()->route('sesi-keputusans.show', $sesiKeputusan)
            ->with('success', 'Alternatif berhasil dihapus dari sesi ini.');
    }

    public function updatePenilaian(Request $request, SesiKeputusan $sesiKeputusan): RedirectResponse
    {
        $data = $request->validate([
            'nilai' => ['required', 'array'],
            'nilai.*' => ['required', 'array'],
            'nilai.*.*' => ['required', 'numeric', 'min:0.0001'],
        ]);

        DB::transaction(function () use ($data, $sesiKeputusan): void {
            $validAltIds = $sesiKeputusan->sesiAlternatifs()->pluck('id')->flip();
            $validKritIds = $sesiKeputusan->sesiKriterias()->pluck('id')->flip();

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

        return redirect()->route('sesi-keputusans.show', $sesiKeputusan)
            ->with('success', 'Matriks nilai penilaian berhasil disimpan.');
    }

    public function hitung(SesiKeputusan $sesiKeputusan, TopsisService $topsisService): RedirectResponse
    {
        $userId = Auth::id() ?? $sesiKeputusan->users_id;
        $riwayat = $topsisService->calculate($sesiKeputusan, $userId);

        return redirect()->route('riwayat-perhitungans.show', $riwayat)
            ->with('success', 'Perhitungan TOPSIS selesai! Berikut hasil pemeringkatan lengkap dan rincian matriksnya.');
    }

    public function destroy(SesiKeputusan $sesiKeputusan): RedirectResponse
    {
        $nama = $sesiKeputusan->nama;
        $sesiKeputusan->delete();

        return redirect()->route('sesi-keputusans.index')
            ->with('success', 'Sesi keputusan "' . $nama . '" berhasil dihapus.');
    }
}

