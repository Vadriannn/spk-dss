<?php

namespace Tests\Feature;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\SesiAlternatif;
use App\Models\SesiKeputusan;
use App\Models\SesiKriteria;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpkTopsisTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_populates_criteria_weights_alternatives_and_tabel_a_without_calculation(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('kriterias', 6);
        $this->assertDatabaseCount('sesi_kriterias', 6);
        $this->assertDatabaseCount('alternatifs', 21);
        $this->assertDatabaseCount('sesi_alternatifs', 21);
        $this->assertDatabaseCount('penilaians', 21 * 6);

        // No calculation should be run at this stage
        $this->assertDatabaseCount('riwayat_perhitungans', 0);
        $this->assertDatabaseCount('hasil_perhitungans', 0);
    }

    public function test_user_can_input_alternative_criteria_weight_and_update_tabel_a(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('dashboard'))->assertOk();
        $this->get(route('kriterias.index'))->assertOk();
        $this->get(route('alternatifs.index'))->assertOk();

        // 1. Add new criterion + weight
        $this->post(route('kriterias.store'), [
            'kode' => 'C7',
            'deskripsi' => 'Kecepatan Pengiriman (1-3)',
            'enum' => 'benefit',
            'bobot' => 2.5,
        ])->assertRedirect(route('kriterias.index'));

        $c7 = Kriteria::where('kode', 'C7')->firstOrFail();
        $this->assertDatabaseHas('sesi_kriterias', [
            'kriterias_id' => $c7->id,
            'bobot' => 2.5,
            'tipe' => 'benefit',
        ]);

        // Update criterion weight
        $this->put(route('kriterias.update', $c7), [
            'kode' => 'C7',
            'deskripsi' => 'Kecepatan Pengiriman Update',
            'enum' => 'benefit',
            'bobot' => 4,
        ])->assertRedirect(route('kriterias.index'));

        $this->assertDatabaseHas('sesi_kriterias', [
            'kriterias_id' => $c7->id,
            'bobot' => 4,
        ]);

        // 2. Add new alternative with values via Tabel A quick-add
        $sesi = SesiKeputusan::current();
        $sesiKriterias = $sesi->sesiKriterias()->orderBy('id')->get();
        $nilaiInput = [];
        foreach ($sesiKriterias as $sk) {
            $nilaiInput[$sk->id] = 3;
        }

        $this->post(route('tabel-a.quick-add'), [
            'kode' => 'A22',
            'nama' => 'Thrift Baru Nusantara',
            'deskripsi' => 'Supplier baru',
            'nilai' => $nilaiInput,
        ])->assertRedirect(route('dashboard'));

        $a22 = Alternatif::where('kode', 'A22')->firstOrFail();
        $sa22 = SesiAlternatif::where('alternatifs_id', $a22->id)->firstOrFail();
        $firstSk = $sesiKriterias->first();

        // 3. Update Tabel A cell value for A22
        $this->put(route('tabel-a.update'), [
            'nilai' => [
                $sa22->id => [
                    $firstSk->id => 1,
                ],
            ],
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('penilaians', [
            'sesi_alternatif_id' => $sa22->id,
            'sesi_kriterias_id' => $firstSk->id,
            'nilai' => 1,
        ]);

        // 4. Delete test criterion and alternative
        $this->delete(route('kriterias.destroy', $c7))->assertRedirect(route('kriterias.index'));
        $this->delete(route('alternatifs.destroy', $a22))->assertRedirect(route('alternatifs.index'));
    }

    public function test_seed_button_loads_initial_data_when_database_is_empty(): void
    {
        $this->get(route('dashboard'))->assertOk();
        $this->post(route('tabel-a.seed'))->assertRedirect(route('dashboard'));

        $this->assertDatabaseCount('kriterias', 6);
        $this->assertDatabaseCount('alternatifs', 21);
        $this->assertDatabaseCount('penilaians', 126);
    }
}
