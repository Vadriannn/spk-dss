<?php

namespace App\Services;

use App\Models\DetailPerhitungan;
use App\Models\HasilPerhitungan;
use App\Models\IdealPerhitungan;
use App\Models\Penilaian;
use App\Models\RiwayatPerhitungan;
use App\Models\SesiKeputusan;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TopsisService
{
    /**
     * Execute TOPSIS calculation for a decision session and persist full results.
     */
    public function calculate(SesiKeputusan $sesi, ?int $userId = null): RiwayatPerhitungan
    {
        $sesiKriterias = $sesi->sesiKriterias()->with('kriteria')->orderBy('id')->get();
        $sesiAlternatifs = $sesi->sesiAlternatifs()->with('alternatif')->orderBy('id')->get();

        if ($sesiKriterias->isEmpty()) {
            throw new InvalidArgumentException('Sesi keputusan harus memiliki minimal 1 kriteria.');
        }

        if ($sesiAlternatifs->isEmpty()) {
            throw new InvalidArgumentException('Sesi keputusan harus memiliki minimal 1 alternatif.');
        }

        $penilaians = Penilaian::query()
            ->whereIn('sesi_alternatif_id', $sesiAlternatifs->pluck('id'))
            ->whereIn('sesi_kriterias_id', $sesiKriterias->pluck('id'))
            ->get()
            ->keyBy(fn (Penilaian $p): string => "{$p->sesi_alternatif_id}_{$p->sesi_kriterias_id}");

        $totalBobot = (float) $sesiKriterias->sum('bobot');
        if ($totalBobot <= 0.0) {
            throw new InvalidArgumentException('Total bobot kriteria harus lebih besar dari 0.');
        }

        $weights = [];
        foreach ($sesiKriterias as $sk) {
            $weights[$sk->id] = (float) $sk->bobot / $totalBobot;
        }

        // Step 1: Decision Matrix X_ij & Sum of Squares per Criterion
        $matrixX = [];
        $divisors = [];

        foreach ($sesiKriterias as $sk) {
            $sumSquares = 0.0;
            foreach ($sesiAlternatifs as $sa) {
                $key = "{$sa->id}_{$sk->id}";
                $val = isset($penilaians[$key]) ? (float) $penilaians[$key]->nilai : 0.0;
                $matrixX[$sa->id][$sk->id] = $val;
                $sumSquares += $val * $val;
            }
            $divisors[$sk->id] = $sumSquares > 0.0 ? sqrt($sumSquares) : 1.0;
        }

        // Step 2 & 3: Normalized Matrix R_ij & Weighted Normalized Matrix Y_ij
        $matrixR = [];
        $matrixY = [];

        foreach ($sesiAlternatifs as $sa) {
            foreach ($sesiKriterias as $sk) {
                $r = $matrixX[$sa->id][$sk->id] / $divisors[$sk->id];
                $y = $r * $weights[$sk->id];
                $matrixR[$sa->id][$sk->id] = $r;
                $matrixY[$sa->id][$sk->id] = $y;
            }
        }

        // Step 4: Positive Ideal Solution (A+) and Negative Ideal Solution (A-)
        $idealPositif = [];
        $idealNegatif = [];

        foreach ($sesiKriterias as $sk) {
            $columnValues = [];
            foreach ($sesiAlternatifs as $sa) {
                $columnValues[] = $matrixY[$sa->id][$sk->id];
            }

            if ($sk->tipe === 'cost') {
                $idealPositif[$sk->id] = min($columnValues);
                $idealNegatif[$sk->id] = max($columnValues);
            } else {
                $idealPositif[$sk->id] = max($columnValues);
                $idealNegatif[$sk->id] = min($columnValues);
            }
        }

        // Step 5 & 6: Euclidean Distances (D+, D-) and Preference Value (V)
        $results = [];
        foreach ($sesiAlternatifs as $sa) {
            $sumPos = 0.0;
            $sumNeg = 0.0;

            foreach ($sesiKriterias as $sk) {
                $y = $matrixY[$sa->id][$sk->id];
                $sumPos += ($y - $idealPositif[$sk->id]) ** 2;
                $sumNeg += ($y - $idealNegatif[$sk->id]) ** 2;
            }

            $dPos = sqrt($sumPos);
            $dNeg = sqrt($sumNeg);
            $denom = $dPos + $dNeg;
            $preferensi = $denom > 0.0 ? $dNeg / $denom : 0.0;

            $results[] = [
                'sesi_alternatif_id' => $sa->id,
                'jarak_positif' => $dPos,
                'jarak_negatif' => $dNeg,
                'nilai_preferensi' => $preferensi,
            ];
        }

        usort($results, static function (array $a, array $b): int {
            $cmp = $b['nilai_preferensi'] <=> $a['nilai_preferensi'];
            if ($cmp !== 0) {
                return $cmp;
            }

            return $a['sesi_alternatif_id'] <=> $b['sesi_alternatif_id'];
        });

        // Step 7: Persist to Database inside a Transaction
        return DB::transaction(function () use (
            $sesi,
            $userId,
            $sesiKriterias,
            $sesiAlternatifs,
            $matrixX,
            $matrixR,
            $matrixY,
            $idealPositif,
            $idealNegatif,
            $results
        ): RiwayatPerhitungan {
            $now = now();

            $riwayat = RiwayatPerhitungan::create([
                'waktu' => $now,
                'users_id' => $userId ?? $sesi->users_id,
                'sesi_keputusans_id' => $sesi->id,
            ]);

            $idealRows = [];
            foreach ($sesiKriterias as $sk) {
                $idealRows[] = [
                    'ideal_positif' => round($idealPositif[$sk->id], 6),
                    'ideal_negatif' => round($idealNegatif[$sk->id], 6),
                    'sesi_kriterias_id' => $sk->id,
                    'riwayat_perhitungans_id' => $riwayat->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            IdealPerhitungan::insert($idealRows);

            $detailRows = [];
            foreach ($sesiAlternatifs as $sa) {
                foreach ($sesiKriterias as $sk) {
                    $detailRows[] = [
                        'nilai_awal' => round($matrixX[$sa->id][$sk->id], 6),
                        'nilai_normalisasi' => round($matrixR[$sa->id][$sk->id], 6),
                        'nilai_terbobot' => round($matrixY[$sa->id][$sk->id], 6),
                        'riwayat_perhitungans_id' => $riwayat->id,
                        'sesi_kriterias_id' => $sk->id,
                        'sesi_alternatif_id' => $sa->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
            DetailPerhitungan::insert($detailRows);

            $hasilRows = [];
            foreach ($results as $index => $row) {
                $hasilRows[] = [
                    'jarak_positif' => round($row['jarak_positif'], 6),
                    'jarak_negatif' => round($row['jarak_negatif'], 6),
                    'nilai_preferensi' => round($row['nilai_preferensi'], 6),
                    'ranking' => $index + 1,
                    'riwayat_perhitungans_id' => $riwayat->id,
                    'sesi_alternatif_id' => $row['sesi_alternatif_id'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            HasilPerhitungan::insert($hasilRows);

            $sesi->update(['status' => 'selesai']);

            return $riwayat;
        });
    }
}

