<?php

namespace Database\Seeders;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\SesiAlternatif;
use App\Models\SesiKeputusan;
use App\Models\SesiKriteria;
use App\Models\User;
use App\Services\TopsisService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@faency.id'],
            [
                'nama' => 'Admin Faency.id',
                'password' => Hash::make('password'),
            ]
        );

        $kriteriaData = [
            [
                'kode' => 'C1',
                'nama' => 'Harga',
                'deskripsi' => 'Rp 10.000 - Rp 120.000 per pcs (1: Murah / < Rp 45.000, 2: Sedang / Rp 45.000 - Rp 80.000, 3: Mahal / > Rp 80.000)',
                'enum' => 'cost',
                'bobot_default' => 3,
            ],
            [
                'kode' => 'C2',
                'nama' => 'Kualitas Produk',
                'deskripsi' => 'Grade A hingga Grade C (1: Grade C, 2: Grade B, 3: Grade A)',
                'enum' => 'benefit',
                'bobot_default' => 3,
            ],
            [
                'kode' => 'C3',
                'nama' => 'Jumlah Barang Terjual',
                'deskripsi' => '< 100 hingga 200 barang per bulan (1: < 100 pcs, 2: 100 - 200 pcs, 3: > 200 pcs)',
                'enum' => 'benefit',
                'bobot_default' => 1,
            ],
            [
                'kode' => 'C4',
                'nama' => 'Ketersediaan Stok',
                'deskripsi' => 'Rendah hingga Tinggi (1: Rendah, 2: Sedang, 3: Tinggi)',
                'enum' => 'benefit',
                'bobot_default' => 2,
            ],
            [
                'kode' => 'C5',
                'nama' => 'Layanan',
                'deskripsi' => 'Kurang hingga Baik (1: Kurang, 2: Cukup, 3: Baik)',
                'enum' => 'benefit',
                'bobot_default' => 1,
            ],
            [
                'kode' => 'C6',
                'nama' => 'Reputasi Toko',
                'deskripsi' => 'Kurang hingga Baik (1: Kurang, 2: Cukup, 3: Baik)',
                'enum' => 'benefit',
                'bobot_default' => 2,
            ],
        ];

        $kriterias = [];
        foreach ($kriteriaData as $row) {
            $kriterias[$row['kode']] = Kriteria::updateOrCreate(['kode' => $row['kode']], $row);
        }

        $alternatifData = [
            ['kode' => 'A1',  'nama' => 'CT Icon',                 'scores' => [2, 3, 3, 3, 3, 3]],
            ['kode' => 'A2',  'nama' => 'Raja',                    'scores' => [2, 3, 3, 2, 2, 3]],
            ['kode' => 'A3',  'nama' => 'Kanaya Collection',       'scores' => [2, 2, 3, 3, 2, 3]],
            ['kode' => 'A4',  'nama' => 'Boneh Store',             'scores' => [1, 2, 3, 3, 2, 1]],
            ['kode' => 'A5',  'nama' => 'Kinan Collection',        'scores' => [2, 3, 2, 3, 3, 1]],
            ['kode' => 'A6',  'nama' => 'Karambia',                'scores' => [2, 3, 3, 3, 2, 1]],
            ['kode' => 'A7',  'nama' => 'Indah Collection',        'scores' => [1, 2, 3, 1, 1, 3]],
            ['kode' => 'A8',  'nama' => 'Rahma Collection',        'scores' => [2, 3, 3, 1, 1, 3]],
            ['kode' => 'A9',  'nama' => 'Fava Blues Collection',   'scores' => [1, 2, 1, 1, 1, 3]],
            ['kode' => 'A10', 'nama' => 'Shevin.collection',       'scores' => [1, 3, 1, 1, 1, 1]],
            ['kode' => 'A11', 'nama' => 'Ria Almira Collection',   'scores' => [2, 3, 3, 3, 1, 1]],
            ['kode' => 'A12', 'nama' => 'Bursa Collection',        'scores' => [2, 2, 2, 3, 1, 3]],
            ['kode' => 'A13', 'nama' => 'Toko Rasa Icha Pakaian',  'scores' => [2, 2, 3, 1, 1, 3]],
            ['kode' => 'A14', 'nama' => 'Toko Zona Rodi',          'scores' => [2, 2, 3, 3, 1, 1]],
            ['kode' => 'A15', 'nama' => 'Ardam Thrifting',         'scores' => [3, 2, 3, 3, 1, 3]],
            ['kode' => 'A16', 'nama' => 'Syukuri Rempita, Beji',   'scores' => [2, 1, 3, 3, 2, 2]],
            ['kode' => 'A17', 'nama' => 'Riko Pasar Senen',        'scores' => [3, 1, 3, 3, 3, 3]],
            ['kode' => 'A18', 'nama' => 'Thrift IrwanMonza',       'scores' => [2, 2, 3, 1, 1, 1]],
            ['kode' => 'A19', 'nama' => 'Cassanovathrift',         'scores' => [3, 2, 3, 2, 1, 2]],
            ['kode' => 'A20', 'nama' => 'BN Second Brand',         'scores' => [3, 1, 3, 3, 1, 2]],
            ['kode' => 'A21', 'nama' => 'Rtr_riterisecond',        'scores' => [3, 2, 3, 2, 1, 1]],
        ];

        $sesi = SesiKeputusan::create([
            'nama' => 'Evaluasi Supplier Pakaian Thrift Faency.id (Studi Kasus Jurnal)',
            'deskripsi' => 'Seleksi 21 alternatif supplier pakaian bekas (thrift shop) pada Faency.id menggunakan metode TOPSIS berdasarkan 6 kriteria utama.',
            'status' => 'aktif',
            'users_id' => $admin->id,
        ]);

        $sesiKriterias = [];
        foreach ($kriterias as $kode => $kriteria) {
            $sesiKriterias[$kode] = SesiKriteria::create([
                'bobot' => $kriteria->bobot_default,
                'tipe' => $kriteria->enum,
                'sesi_keputusans_id' => $sesi->id,
                'kriterias_id' => $kriteria->id,
            ]);
        }

        $kriteriaCodes = ['C1', 'C2', 'C3', 'C4', 'C5', 'C6'];

        foreach ($alternatifData as $item) {
            $alternatif = Alternatif::updateOrCreate(
                ['kode' => $item['kode']],
                [
                    'nama' => $item['nama'],
                    'deskripsi' => 'Supplier pakaian thrift (' . $item['kode'] . ' - ' . $item['nama'] . ')',
                ]
            );

            $sesiAlternatif = SesiAlternatif::create([
                'sesi_keputusans_id' => $sesi->id,
                'alternatifs_id' => $alternatif->id,
            ]);

            foreach ($kriteriaCodes as $idx => $kode) {
                Penilaian::create([
                    'nilai' => $item['scores'][$idx],
                    'sesi_kriterias_id' => $sesiKriterias[$kode]->id,
                    'sesi_alternatif_id' => $sesiAlternatif->id,
                ]);
            }
        }

        app(TopsisService::class)->calculate($sesi, $admin->id);
    }
}
