<?php

namespace Tests\Feature;

use App\Models\Alternatif;
use App\Models\DetailPerhitungan;
use App\Models\HasilPerhitungan;
use App\Models\IdealPerhitungan;
use App\Models\Kriteria;
use App\Models\RiwayatPerhitungan;
use App\Models\SesiKeputusan;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpkTopsisTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_produces_exact_journal_topsis_ranking_with_ct_icon_rank_one(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('kriterias', 6);
        $this->assertDatabaseCount('alternatifs', 21);
        $this->assertDatabaseCount('sesi_keputusans', 1);
        $this->assertDatabaseCount('riwayat_perhitungans', 1);
        $this->assertDatabaseCount('hasil_perhitungans', 21);
        $this->assertDatabaseCount('ideal_perhitungans', 6);
        $this->assertDatabaseCount('detail_perhitungans', 21 * 6);

        $riwayat = RiwayatPerhitungan::firstOrFail();
        $top = HasilPerhitungan::where('riwayat_perhitungans_id', $riwayat->id)
            ->where('ranking', 1)
            ->with('sesiAlternatif.alternatif')
            ->firstOrFail();

        $this->assertSame('A1', $top->sesiAlternatif->alternatif->kode);
        $this->assertSame('CT Icon', $top->sesiAlternatif->alternatif->nama);
        $this->assertSame(0.7440, round($top->nilai_preferensi, 4));

        // Verify all 21 rankings are strictly ordered A1..A21
        $hasils = HasilPerhitungan::where('riwayat_perhitungans_id', $riwayat->id)
            ->orderBy('ranking')
            ->with('sesiAlternatif.alternatif')
            ->get();

        foreach ($hasils as $idx => $hasil) {
            $this->assertSame($idx + 1, $hasil->ranking);
            $this->assertSame('A' . ($idx + 1), $hasil->sesiAlternatif->alternatif->kode);
        }
    }

    public function test_user_can_dynamically_add_new_alternative_with_scores_and_recalculate_topsis(): void
    {
        $this->seed(DatabaseSeeder::class);

        $sesi = SesiKeputusan::with('sesiKriterias')->firstOrFail();
        $nilaiInput = [];
        foreach ($sesi->sesiKriterias as $sk) {
            // Give ideal values (1 for cost, 3 for benefit) to make A22 a strong contender
            $nilaiInput[$sk->id] = $sk->tipe === 'cost' ? 1 : 3;
        }

        $response = $this->post(route('sesi-keputusans.alternatifs.add-new', $sesi), [
            'kode' => 'A22',
            'nama' => 'Thrift Baru Nusantara',
            'deskripsi' => 'Supplier baru dinamis',
            'nilai' => $nilaiInput,
        ]);

        $response->assertRedirect(route('sesi-keputusans.show', $sesi));
        $this->assertDatabaseHas('alternatifs', [
            'kode' => 'A22',
            'nama' => 'Thrift Baru Nusantara',
        ]);

        $calcResponse = $this->post(route('sesi-keputusans.hitung', $sesi));
        $latestRiwayat = RiwayatPerhitungan::latest('id')->firstOrFail();
        $calcResponse->assertRedirect(route('riwayat-perhitungans.show', $latestRiwayat));

        $this->assertSame(22, HasilPerhitungan::where('riwayat_perhitungans_id', $latestRiwayat->id)->count());
        $this->assertSame(22 * 6, DetailPerhitungan::where('riwayat_perhitungans_id', $latestRiwayat->id)->count());
        $this->assertSame(6, IdealPerhitungan::where('riwayat_perhitungans_id', $latestRiwayat->id)->count());

        $top = HasilPerhitungan::where('riwayat_perhitungans_id', $latestRiwayat->id)
            ->where('ranking', 1)
            ->with('sesiAlternatif.alternatif')
            ->firstOrFail();

        $this->assertSame('A22', $top->sesiAlternatif->alternatif->kode);
        $this->assertEquals(1.0, round($top->nilai_preferensi, 4));
    }

    public function test_all_main_pages_and_crud_routes_work(): void
    {
        $this->seed(DatabaseSeeder::class);

        $sesi = SesiKeputusan::firstOrFail();
        $riwayat = RiwayatPerhitungan::firstOrFail();

        $this->get(route('dashboard'))->assertOk();
        $this->get(route('proposal'))->assertOk();
        $this->get(route('kriterias.index'))->assertOk();
        $this->get(route('alternatifs.index'))->assertOk();
        $this->get(route('sesi-keputusans.index'))->assertOk();
        $this->get(route('sesi-keputusans.show', $sesi))->assertOk();
        $this->get(route('riwayat-perhitungans.index'))->assertOk();
        $this->get(route('riwayat-perhitungans.show', $riwayat))->assertOk();

        // Create & delete a criterion
        $this->post(route('kriterias.store'), [
            'kode' => 'C7',
            'nama' => 'Kecepatan Kirim',
            'deskripsi' => '1-3 hari',
            'enum' => 'benefit',
            'bobot_default' => 2,
        ])->assertRedirect(route('kriterias.index'));

        $c7 = Kriteria::where('kode', 'C7')->firstOrFail();
        $this->delete(route('kriterias.destroy', $c7))->assertRedirect(route('kriterias.index'));

        // Create & delete a master alternative
        $this->post(route('alternatifs.store'), [
            'kode' => 'A99',
            'nama' => 'Supplier Test',
            'deskripsi' => 'Test',
        ])->assertRedirect(route('alternatifs.index'));

        $a99 = Alternatif::where('kode', 'A99')->firstOrFail();
        $this->delete(route('alternatifs.destroy', $a99))->assertRedirect(route('alternatifs.index'));
    }
}

