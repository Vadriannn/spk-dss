<?php

namespace Database\Seeders;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\SesiAlternatif;
use App\Models\SesiKeputusan;
use App\Models\SesiKriteria;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with initial criteria, weights, alternatives, and Tabel A scores.
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

        $sesi = SesiKeputusan::firstOrCreate(
            ['nama' => 'Evaluasi Supplier Pakaian Thrift Faency.id'],
            [
                'deskripsi' => 'Input alternatif, bobot kriteria, dan nilai alternatif terhadap kriteria (Tabel A).',
                'status' => 'aktif',
                'users_id' => $admin->id,
            ]
        );

        $kriteriaData = [
            [
                'kode' => 'C1',
                'deskripsi' => 'Harga (Rp 10.000 - Rp 120.000 per pcs)',
                'enum' => 'cost',
                'bobot' => 3,
            ],
            [
                'kode' => 'C2',
                'deskripsi' => 'Kualitas Produk (Grade A hingga Grade C)',
                'enum' => 'benefit',
                'bobot' => 3,
            ],
            [
                'kode' => 'C3',
                'deskripsi' => 'Jumlah Barang Terjual (< 100 hingga 200 barang/bln)',
                'enum' => 'benefit',
                'bobot' => 1,
            ],
            [
                'kode' => 'C4',
                'deskripsi' => 'Ketersediaan Stok (Rendah hingga Tinggi)',
                'enum' => 'benefit',
                'bobot' => 2,
            ],
            [
                'kode' => 'C5',
                'deskripsi' => 'Layanan (Kurang hingga Baik)',
                'enum' => 'benefit',
                'bobot' => 1,
            ],
            [
                'kode' => 'C6',
                'deskripsi' => 'Reputasi Toko (Kurang hingga Baik)',
                'enum' => 'benefit',
                'bobot' => 2,
            ],
        ];

        $sesiKriterias = [];
        foreach ($kriteriaData as $row) {
            $kriteria = Kriteria::updateOrCreate(
                ['kode' => $row['kode']],
                [
                    'deskripsi' => $row['deskripsi'],
                    'enum' => $row['enum'],
                ]
            );

            $sesiKriterias[$row['kode']] = SesiKriteria::firstOrCreate(
                [
                    'sesi_keputusans_id' => $sesi->id,
                    'kriterias_id' => $kriteria->id,
                ],
                [
                    'bobot' => $row['bobot'],
                    'tipe' => $row['enum'],
                ]
            );
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

        $kriteriaCodes = ['C1', 'C2', 'C3', 'C4', 'C5', 'C6'];

        foreach ($alternatifData as $item) {
            $alternatif = Alternatif::updateOrCreate(
                ['kode' => $item['kode']],
                [
                    'nama' => $item['nama'],
                    'deskripsi' => 'Supplier pakaian thrift (' . $item['kode'] . ' - ' . $item['nama'] . ')',
                ]
            );

            $sesiAlternatif = SesiAlternatif::firstOrCreate([
                'sesi_keputusans_id' => $sesi->id,
                'alternatifs_id' => $alternatif->id,
            ]);

            foreach ($kriteriaCodes as $idx => $kode) {
                Penilaian::firstOrCreate(
                    [
                        'sesi_kriterias_id' => $sesiKriterias[$kode]->id,
                        'sesi_alternatif_id' => $sesiAlternatif->id,
                    ],
                    [
                        'nilai' => $item['scores'][$idx],
                    ]
                );
            }
        }
    }
}
