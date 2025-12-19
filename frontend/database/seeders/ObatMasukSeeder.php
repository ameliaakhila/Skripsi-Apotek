<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ObatMasukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $obatList = [
            ['nama' => 'Paracetamol 500mg', 'kategori' => 'Analgesik'],
            ['nama' => 'Amoxicillin 500mg', 'kategori' => 'Antibiotik'],
            ['nama' => 'Cefixime 200mg', 'kategori' => 'Antibiotik'],
            // ['nama' => 'Vitamin C 500mg', 'kategori' => 'Vitamin'],
            // ['nama' => 'Ibuprofen 400mg', 'kategori' => 'Analgesik'],
            // ['nama' => 'Cetirizine 10mg', 'kategori' => 'Antihistamin'],
            // ['nama' => 'Ranitidine 150mg', 'kategori' => 'Antasida'],
            // ['nama' => 'Metformin 500mg', 'kategori' => 'Antidiabetik'],
            // ['nama' => 'Amlodipine 5mg', 'kategori' => 'Hipertensi'],
            // ['nama' => 'Simvastatin 10mg', 'kategori' => 'Kolesterol'],
            // ['nama' => 'Loratadine 10mg', 'kategori' => 'Antihistamin'],
            // Tambahkan sisa obat sampai 50 data sesuai kebutuhan
        ];

        $data = [];

        foreach ($obatList as $index => $obat) {
            $stokMasuk = rand(100, 200);
            $stokKeluar = rand(10, 100);
            $stokSisa = $stokMasuk - $stokKeluar;
            $kodeObat = 'SF' . str_pad($index + 1, 5, '0', STR_PAD_LEFT);

            $idObat = DB::table('tb_obat')->where('nama_obat', $obat['nama'])->value('id_obat');
            $idKategori = DB::table('tb_kategori_obat')->where('nama_kategori_obat', $obat['kategori'])->value('id_kategori_obat');

            $data[] = [
                'kode_obat' => $kodeObat,
                'tanggal_masuk' => now(),
                'tanggal_kadaluarsa' => now()->addYears(rand(2, 5))->format('Y-m-d'),
                'tanggal_keluar' => now()->addYear()->format('Y-m-d'),
                'stok_masuk' => $stokMasuk,
                'stok_keluar' => $stokKeluar,
                'stok_sisa' => $stokSisa,
                'satuan_obat' => 'Strip',
                'harga_obat' => rand(10000, 20000),
                'created_at' => now(),
                'updated_at' => now(),
                'id_obat' => $idObat,
                'id_kategori_obat' => $idKategori,
            ];
        }

        DB::table('tb_obat_masuk')->insert($data);

        // DB::table('tb_obat_masuk')->insert([
        //     [
        //         'kode_obat' => 'SF10002',
        //         'tanggal_masuk' => now(),
        //         'tanggal_kadaluarsa' => '2028-02-24',
        //         'tanggal_keluar' => '2026-01-29',
        //         'stok_masuk' => 146,
        //         'stok_keluar' => 73,
        //         'stok_sisa' => 73,
        //         'satuan_obat' => 'Strip',
        //         'harga_obat' => 14000,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //         'id_obat' => DB::table('tb_obat')->where('nama_obat', 'Paracetamol 500mg')->value('id_obat'),
        //         'id_kategori_obat' => DB::table('tb_kategori_obat')->where('nama_kategori_obat', 'Analgesik')->value('id_kategori_obat')
        //     ]
        // ]);

    }
}

// [
//             ['kode_obat' => 'OB001', 'nama_obat' => 'Paracetamol 500mg', 'pabrik_obat' => 'Kalbe', 'kategori_obat' => 'Analgesik', 'tanggal_masuk' => '2025-12-14', 'tanggal_kadaluarsa' => '2028-02-24', 'tanggal_keluar' => '2026-01-29', 'stok_masuk' => 146, 'stok_keluar' => 73, 'stok_sisa' => 73, 'satuan_obat' => 'Tablet', 'harga_obat' => 14000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB002', 'nama_obat' => 'Amoxicillin 500mg', 'pabrik_obat' => 'Dexa', 'kategori_obat' => 'Antibiotik', 'tanggal_masuk' => '2025-12-28', 'tanggal_kadaluarsa' => '2028-04-25', 'tanggal_keluar' => '2026-01-16', 'stok_masuk' => 188, 'stok_keluar' => 94, 'stok_sisa' => 94, 'satuan_obat' => 'Kapsul', 'harga_obat' => 9000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB003', 'nama_obat' => 'Cefixime 200mg', 'pabrik_obat' => 'Novell', 'kategori_obat' => 'Antibiotik', 'tanggal_masuk' => '2025-12-04', 'tanggal_kadaluarsa' => '2028-04-30', 'tanggal_keluar' => '2026-01-19', 'stok_masuk' => 112, 'stok_keluar' => 56, 'stok_sisa' => 56, 'satuan_obat' => 'Kapsul', 'harga_obat' => 10000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB004', 'nama_obat' => 'Vitamin C 500mg', 'pabrik_obat' => 'Kimia Farma', 'kategori_obat' => 'Vitamin', 'tanggal_masuk' => '2025-12-08', 'tanggal_kadaluarsa' => '2028-09-08', 'tanggal_keluar' => '2026-01-06', 'stok_masuk' => 123, 'stok_keluar' => 62, 'stok_sisa' => 62, 'satuan_obat' => 'Tablet', 'harga_obat' => 13000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB005', 'nama_obat' => 'Ibuprofen 400mg', 'pabrik_obat' => 'Kalbe', 'kategori_obat' => 'Analgesik', 'tanggal_masuk' => '2025-12-29', 'tanggal_kadaluarsa' => '2028-08-12', 'tanggal_keluar' => '2026-01-04', 'stok_masuk' => 125, 'stok_keluar' => 63, 'stok_sisa' => 63, 'satuan_obat' => 'Tablet', 'harga_obat' => 6000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB006', 'nama_obat' => 'Cetirizine 10mg', 'pabrik_obat' => 'Dexa', 'kategori_obat' => 'Antihistamin', 'tanggal_masuk' => '2025-12-22', 'tanggal_kadaluarsa' => '2028-11-14', 'tanggal_keluar' => '2026-01-28', 'stok_masuk' => 199, 'stok_keluar' => 100, 'stok_sisa' => 100, 'satuan_obat' => 'Tablet', 'harga_obat' => 11000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB007', 'nama_obat' => 'Ranitidine 150mg', 'pabrik_obat' => 'Novell', 'kategori_obat' => 'Antasida', 'tanggal_masuk' => '2025-12-01', 'tanggal_kadaluarsa' => '2028-12-14', 'tanggal_keluar' => '2026-01-12', 'stok_masuk' => 146, 'stok_keluar' => 73, 'stok_sisa' => 73, 'satuan_obat' => 'Tablet', 'harga_obat' => 11000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB008', 'nama_obat' => 'Metformin 500mg', 'pabrik_obat' => 'Kalbe', 'kategori_obat' => 'Diabetes', 'tanggal_masuk' => '2025-12-12', 'tanggal_kadaluarsa' => '2028-05-02', 'tanggal_keluar' => '2026-01-20', 'stok_masuk' => 155, 'stok_keluar' => 78, 'stok_sisa' => 78, 'satuan_obat' => 'Tablet', 'harga_obat' => 6000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB009', 'nama_obat' => 'Amlodipine 5mg', 'pabrik_obat' => 'Dexa', 'kategori_obat' => 'Hipertensi', 'tanggal_masuk' => '2025-12-07', 'tanggal_kadaluarsa' => '2028-09-13', 'tanggal_keluar' => '2026-01-12', 'stok_masuk' => 172, 'stok_keluar' => 86, 'stok_sisa' => 86, 'satuan_obat' => 'Tablet', 'harga_obat' => 6000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB010', 'nama_obat' => 'Simvastatin 10mg', 'pabrik_obat' => 'Kimia Farma', 'kategori_obat' => 'Kolesterol', 'tanggal_masuk' => '2025-12-08', 'tanggal_kadaluarsa' => '2028-01-10', 'tanggal_keluar' => '2026-01-26', 'stok_masuk' => 100, 'stok_keluar' => 50, 'stok_sisa' => 50, 'satuan_obat' => 'Tablet', 'harga_obat' => 5000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB011', 'nama_obat' => 'Loratadine 10mg', 'pabrik_obat' => 'Novell', 'kategori_obat' => 'Antihistamin', 'tanggal_masuk' => '2025-12-03', 'tanggal_kadaluarsa' => '2028-02-20', 'tanggal_keluar' => '2026-01-30', 'stok_masuk' => 116, 'stok_keluar' => 58, 'stok_sisa' => 58, 'satuan_obat' => 'Tablet', 'harga_obat' => 12000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB012', 'nama_obat' => 'Prednisone 5mg', 'pabrik_obat' => 'Kalbe', 'kategori_obat' => 'Kortikosteroid', 'tanggal_masuk' => '2025-12-10', 'tanggal_kadaluarsa' => '2028-10-24', 'tanggal_keluar' => '2026-01-13', 'stok_masuk' => 185, 'stok_keluar' => 93, 'stok_sisa' => 93, 'satuan_obat' => 'Tablet', 'harga_obat' => 15000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB013', 'nama_obat' => 'Omeprazole 20mg', 'pabrik_obat' => 'Dexa', 'kategori_obat' => 'Antasida', 'tanggal_masuk' => '2025-12-30', 'tanggal_kadaluarsa' => '2028-11-30', 'tanggal_keluar' => '2026-01-07', 'stok_masuk' => 171, 'stok_keluar' => 86, 'stok_sisa' => 86, 'satuan_obat' => 'Kapsul', 'harga_obat' => 5000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB014', 'nama_obat' => 'Salbutamol 2mg', 'pabrik_obat' => 'Kimia Farma', 'kategori_obat' => 'Bronkodilator', 'tanggal_masuk' => '2025-12-27', 'tanggal_kadaluarsa' => '2028-01-04', 'tanggal_keluar' => '2026-01-02', 'stok_masuk' => 135, 'stok_keluar' => 68, 'stok_sisa' => 68, 'satuan_obat' => 'Tablet', 'harga_obat' => 12000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB015', 'nama_obat' => 'Furosemide 40mg', 'pabrik_obat' => 'Novell', 'kategori_obat' => 'Diuretik', 'tanggal_masuk' => '2025-12-20', 'tanggal_kadaluarsa' => '2028-09-15', 'tanggal_keluar' => '2026-01-11', 'stok_masuk' => 148, 'stok_keluar' => 74, 'stok_sisa' => 74, 'satuan_obat' => 'Tablet', 'harga_obat' => 13000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB016', 'nama_obat' => 'Captopril 25mg', 'pabrik_obat' => 'Kalbe', 'kategori_obat' => 'Hipertensi', 'tanggal_masuk' => '2025-12-12', 'tanggal_kadaluarsa' => '2028-10-07', 'tanggal_keluar' => '2026-01-26', 'stok_masuk' => 159, 'stok_keluar' => 80, 'stok_sisa' => 80, 'satuan_obat' => 'Tablet', 'harga_obat' => 9000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB017', 'nama_obat' => 'Hydrochlorothiazide 25mg', 'pabrik_obat' => 'Dexa', 'kategori_obat' => 'Diuretik', 'tanggal_masuk' => '2025-12-29', 'tanggal_kadaluarsa' => '2028-01-01', 'tanggal_keluar' => '2026-01-17', 'stok_masuk' => 119, 'stok_keluar' => 60, 'stok_sisa' => 60, 'satuan_obat' => 'Tablet', 'harga_obat' => 6000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB018', 'nama_obat' => 'Clindamycin 300mg', 'pabrik_obat' => 'Novell', 'kategori_obat' => 'Antibiotik', 'tanggal_masuk' => '2025-12-07', 'tanggal_kadaluarsa' => '2028-05-21', 'tanggal_keluar' => '2026-01-28', 'stok_masuk' => 143, 'stok_keluar' => 72, 'stok_sisa' => 72, 'satuan_obat' => 'Kapsul', 'harga_obat' => 10000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB019', 'nama_obat' => 'Dexamethasone 0.5mg', 'pabrik_obat' => 'Kalbe', 'kategori_obat' => 'Kortikosteroid', 'tanggal_masuk' => '2025-12-20', 'tanggal_kadaluarsa' => '2028-04-14', 'tanggal_keluar' => '2026-01-19', 'stok_masuk' => 197, 'stok_keluar' => 99, 'stok_sisa' => 99, 'satuan_obat' => 'Tablet', 'harga_obat' => 9000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB020', 'nama_obat' => 'Metronidazole 500mg', 'pabrik_obat' => 'Dexa', 'kategori_obat' => 'Antibiotik', 'tanggal_masuk' => '2025-12-16', 'tanggal_kadaluarsa' => '2028-02-11', 'tanggal_keluar' => '2026-01-22', 'stok_masuk' => 147, 'stok_keluar' => 74, 'stok_sisa' => 74, 'satuan_obat' => 'Tablet', 'harga_obat' => 8000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB021', 'nama_obat' => 'Aspirin 100mg', 'pabrik_obat' => 'Kimia Farma', 'kategori_obat' => 'Analgesik', 'tanggal_masuk' => '2025-12-14', 'tanggal_kadaluarsa' => '2028-09-11', 'tanggal_keluar' => '2026-01-19', 'stok_masuk' => 149, 'stok_keluar' => 75, 'stok_sisa' => 75, 'satuan_obat' => 'Tablet', 'harga_obat' => 9000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB022', 'nama_obat' => 'Naproxen 250mg', 'pabrik_obat' => 'Novell', 'kategori_obat' => 'Analgesik', 'tanggal_masuk' => '2025-12-14', 'tanggal_kadaluarsa' => '2028-01-30', 'tanggal_keluar' => '2026-01-11', 'stok_masuk' => 166, 'stok_keluar' => 83, 'stok_sisa' => 83, 'satuan_obat' => 'Tablet', 'harga_obat' => 9000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB023', 'nama_obat' => 'Levocetirizine 5mg', 'pabrik_obat' => 'Kalbe', 'kategori_obat' => 'Antihistamin', 'tanggal_masuk' => '2025-12-25', 'tanggal_kadaluarsa' => '2028-05-17', 'tanggal_keluar' => '2026-01-30', 'stok_masuk' => 114, 'stok_keluar' => 57, 'stok_sisa' => 57, 'satuan_obat' => 'Tablet', 'harga_obat' => 13000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB024', 'nama_obat' => 'Montelukast 10mg', 'pabrik_obat' => 'Dexa', 'kategori_obat' => 'Antiasma', 'tanggal_masuk' => '2025-12-07', 'tanggal_kadaluarsa' => '2028-09-18', 'tanggal_keluar' => '2026-01-06', 'stok_masuk' => 128, 'stok_keluar' => 64, 'stok_sisa' => 64, 'satuan_obat' => 'Tablet', 'harga_obat' => 14000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB025', 'nama_obat' => 'Diltiazem 60mg', 'pabrik_obat' => 'Kimia Farma', 'kategori_obat' => 'Hipertensi', 'tanggal_masuk' => '2025-12-15', 'tanggal_kadaluarsa' => '2028-03-10', 'tanggal_keluar' => '2026-01-28', 'stok_masuk' => 178, 'stok_keluar' => 89, 'stok_sisa' => 89, 'satuan_obat' => 'Tablet', 'harga_obat' => 7000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB026', 'nama_obat' => 'Pantoprazole 40mg', 'pabrik_obat' => 'Novell', 'kategori_obat' => 'Antasida', 'tanggal_masuk' => '2025-12-19', 'tanggal_kadaluarsa' => '2028-03-02', 'tanggal_keluar' => '2026-01-14', 'stok_masuk' => 165, 'stok_keluar' => 83, 'stok_sisa' => 83, 'satuan_obat' => 'Kapsul', 'harga_obat' => 13000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB027', 'nama_obat' => 'Tramadol 50mg', 'pabrik_obat' => 'Kalbe', 'kategori_obat' => 'Analgesik', 'tanggal_masuk' => '2025-12-14', 'tanggal_kadaluarsa' => '2028-12-30', 'tanggal_keluar' => '2026-01-26', 'stok_masuk' => 192, 'stok_keluar' => 96, 'stok_sisa' => 96, 'satuan_obat' => 'Tablet', 'harga_obat' => 12000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB028', 'nama_obat' => 'Gabapentin 100mg', 'pabrik_obat' => 'Dexa', 'kategori_obat' => 'Neuropati', 'tanggal_masuk' => '2025-12-19', 'tanggal_kadaluarsa' => '2028-12-09', 'tanggal_keluar' => '2026-01-27', 'stok_masuk' => 161, 'stok_keluar' => 81, 'stok_sisa' => 81, 'satuan_obat' => 'Kapsul', 'harga_obat' => 5000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB029', 'nama_obat' => 'Captopril 12.5mg', 'pabrik_obat' => 'Kimia Farma', 'kategori_obat' => 'Hipertensi', 'tanggal_masuk' => '2025-12-04', 'tanggal_kadaluarsa' => '2028-02-25', 'tanggal_keluar' => '2026-01-07', 'stok_masuk' => 139, 'stok_keluar' => 70, 'stok_sisa' => 70, 'satuan_obat' => 'Tablet', 'harga_obat' => 5000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB030', 'nama_obat' => 'Simvastatin 20mg', 'pabrik_obat' => 'Novell', 'kategori_obat' => 'Kolesterol', 'tanggal_masuk' => '2025-12-18', 'tanggal_kadaluarsa' => '2028-09-20', 'tanggal_keluar' => '2026-01-11', 'stok_masuk' => 126, 'stok_keluar' => 63, 'stok_sisa' => 63, 'satuan_obat' => 'Tablet', 'harga_obat' => 14000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB031', 'nama_obat' => 'Lisinopril 10mg', 'pabrik_obat' => 'Kalbe', 'kategori_obat' => 'Hipertensi', 'tanggal_masuk' => '2025-12-02', 'tanggal_kadaluarsa' => '2028-11-08', 'tanggal_keluar' => '2026-01-15', 'stok_masuk' => 184, 'stok_keluar' => 92, 'stok_sisa' => 92, 'satuan_obat' => 'Tablet', 'harga_obat' => 11000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB032', 'nama_obat' => 'Atenolol 50mg', 'pabrik_obat' => 'Dexa', 'kategori_obat' => 'Hipertensi', 'tanggal_masuk' => '2025-12-25', 'tanggal_kadaluarsa' => '2028-02-27', 'tanggal_keluar' => '2026-01-09', 'stok_masuk' => 111, 'stok_keluar' => 56, 'stok_sisa' => 56, 'satuan_obat' => 'Tablet', 'harga_obat' => 8000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB033', 'nama_obat' => 'Losartan 50mg', 'pabrik_obat' => 'Kimia Farma', 'kategori_obat' => 'Hipertensi', 'tanggal_masuk' => '2025-12-19', 'tanggal_kadaluarsa' => '2028-06-08', 'tanggal_keluar' => '2026-01-06', 'stok_masuk' => 110, 'stok_keluar' => 55, 'stok_sisa' => 55, 'satuan_obat' => 'Tablet', 'harga_obat' => 5000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB034', 'nama_obat' => 'Clarithromycin 250mg', 'pabrik_obat' => 'Novell', 'kategori_obat' => 'Antibiotik', 'tanggal_masuk' => '2025-12-04', 'tanggal_kadaluarsa' => '2028-07-23', 'tanggal_keluar' => '2026-01-03', 'stok_masuk' => 112, 'stok_keluar' => 56, 'stok_sisa' => 56, 'satuan_obat' => 'Kapsul', 'harga_obat' => 7000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB035', 'nama_obat' => 'Azithromycin 500mg', 'pabrik_obat' => 'Kalbe', 'kategori_obat' => 'Antibiotik', 'tanggal_masuk' => '2025-12-05', 'tanggal_kadaluarsa' => '2028-04-17', 'tanggal_keluar' => '2026-01-16', 'stok_masuk' => 158, 'stok_keluar' => 79, 'stok_sisa' => 79, 'satuan_obat' => 'Tablet', 'harga_obat' => 5000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB036', 'nama_obat' => 'Metformin 1000mg', 'pabrik_obat' => 'Dexa', 'kategori_obat' => 'Diabetes', 'tanggal_masuk' => '2025-12-30', 'tanggal_kadaluarsa' => '2028-03-28', 'tanggal_keluar' => '2026-01-12', 'stok_masuk' => 192, 'stok_keluar' => 96, 'stok_sisa' => 96, 'satuan_obat' => 'Tablet', 'harga_obat' => 7000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB037', 'nama_obat' => 'Glimepiride 2mg', 'pabrik_obat' => 'Kimia Farma', 'kategori_obat' => 'Diabetes', 'tanggal_masuk' => '2025-12-20', 'tanggal_kadaluarsa' => '2028-11-06', 'tanggal_keluar' => '2026-01-16', 'stok_masuk' => 108, 'stok_keluar' => 54, 'stok_sisa' => 54, 'satuan_obat' => 'Tablet', 'harga_obat' => 12000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB038', 'nama_obat' => 'Insulin NPH', 'pabrik_obat' => 'Novell', 'kategori_obat' => 'Diabetes', 'tanggal_masuk' => '2025-12-30', 'tanggal_kadaluarsa' => '2028-01-03', 'tanggal_keluar' => '2026-01-01', 'stok_masuk' => 116, 'stok_keluar' => 58, 'stok_sisa' => 58, 'satuan_obat' => 'Ampul', 'harga_obat' => 7000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB039', 'nama_obat' => 'Insulin Regular', 'pabrik_obat' => 'Kalbe', 'kategori_obat' => 'Diabetes', 'tanggal_masuk' => '2025-12-04', 'tanggal_kadaluarsa' => '2028-08-31', 'tanggal_keluar' => '2026-01-20', 'stok_masuk' => 117, 'stok_keluar' => 59, 'stok_sisa' => 59, 'satuan_obat' => 'Ampul', 'harga_obat' => 7000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB040', 'nama_obat' => 'Prednisolone 5mg', 'pabrik_obat' => 'Dexa', 'kategori_obat' => 'Kortikosteroid', 'tanggal_masuk' => '2025-12-12', 'tanggal_kadaluarsa' => '2028-03-12', 'tanggal_keluar' => '2026-01-25', 'stok_masuk' => 161, 'stok_keluar' => 81, 'stok_sisa' => 81, 'satuan_obat' => 'Tablet', 'harga_obat' => 12000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB041', 'nama_obat' => 'Hydrocortisone Cream', 'pabrik_obat' => 'Kimia Farma', 'kategori_obat' => 'Kortikosteroid', 'tanggal_masuk' => '2025-12-20', 'tanggal_kadaluarsa' => '2028-10-22', 'tanggal_keluar' => '2026-01-03', 'stok_masuk' => 160, 'stok_keluar' => 80, 'stok_sisa' => 80, 'satuan_obat' => 'Tube', 'harga_obat' => 15000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB042', 'nama_obat' => 'Fluticasone Nasal Spray', 'pabrik_obat' => 'Novell', 'kategori_obat' => 'Kortikosteroid', 'tanggal_masuk' => '2025-12-17', 'tanggal_kadaluarsa' => '2028-08-09', 'tanggal_keluar' => '2026-01-16', 'stok_masuk' => 165, 'stok_keluar' => 83, 'stok_sisa' => 83, 'satuan_obat' => 'Botol', 'harga_obat' => 12000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB043', 'nama_obat' => 'Salbutamol Inhaler', 'pabrik_obat' => 'Kalbe', 'kategori_obat' => 'Bronkodilator', 'tanggal_masuk' => '2025-12-28', 'tanggal_kadaluarsa' => '2028-05-27', 'tanggal_keluar' => '2026-01-26', 'stok_masuk' => 106, 'stok_keluar' => 53, 'stok_sisa' => 53, 'satuan_obat' => 'Botol', 'harga_obat' => 8000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB044', 'nama_obat' => 'Montelukast 5mg', 'pabrik_obat' => 'Dexa', 'kategori_obat' => 'Antiasma', 'tanggal_masuk' => '2025-12-06', 'tanggal_kadaluarsa' => '2028-02-09', 'tanggal_keluar' => '2026-01-05', 'stok_masuk' => 102, 'stok_keluar' => 51, 'stok_sisa' => 51, 'satuan_obat' => 'Tablet', 'harga_obat' => 12000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB045', 'nama_obat' => 'Ranitidine Syrup 150ml', 'pabrik_obat' => 'Kimia Farma', 'kategori_obat' => 'Antasida', 'tanggal_masuk' => '2025-12-24', 'tanggal_kadaluarsa' => '2028-03-29', 'tanggal_keluar' => '2026-01-24', 'stok_masuk' => 111, 'stok_keluar' => 56, 'stok_sisa' => 56, 'satuan_obat' => 'Botol', 'harga_obat' => 7000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB046', 'nama_obat' => 'Omeprazole 40mg', 'pabrik_obat' => 'Novell', 'kategori_obat' => 'Antasida', 'tanggal_masuk' => '2025-12-28', 'tanggal_kadaluarsa' => '2028-04-03', 'tanggal_keluar' => '2026-01-22', 'stok_masuk' => 119, 'stok_keluar' => 60, 'stok_sisa' => 60, 'satuan_obat' => 'Kapsul', 'harga_obat' => 14000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB047', 'nama_obat' => 'Acetaminophen 500mg', 'pabrik_obat' => 'Kalbe', 'kategori_obat' => 'Analgesik', 'tanggal_masuk' => '2025-12-17', 'tanggal_kadaluarsa' => '2028-01-27', 'tanggal_keluar' => '2026-01-04', 'stok_masuk' => 129, 'stok_keluar' => 65, 'stok_sisa' => 65, 'satuan_obat' => 'Tablet', 'harga_obat' => 15000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB048', 'nama_obat' => 'Ibuprofen 200mg', 'pabrik_obat' => 'Dexa', 'kategori_obat' => 'Analgesik', 'tanggal_masuk' => '2025-12-19', 'tanggal_kadaluarsa' => '2028-09-14', 'tanggal_keluar' => '2026-01-18', 'stok_masuk' => 174, 'stok_keluar' => 87, 'stok_sisa' => 87, 'satuan_obat' => 'Tablet', 'harga_obat' => 15000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB049', 'nama_obat' => 'Diclofenac 50mg', 'pabrik_obat' => 'Kimia Farma', 'kategori_obat' => 'Analgesik', 'tanggal_masuk' => '2025-12-24', 'tanggal_kadaluarsa' => '2028-12-30', 'tanggal_keluar' => '2026-01-23', 'stok_masuk' => 113, 'stok_keluar' => 57, 'stok_sisa' => 57, 'satuan_obat' => 'Tablet', 'harga_obat' => 6000, 'created_at' => now(), 'updated_at' => now()],
//             ['kode_obat' => 'OB050', 'nama_obat' => 'Loperamide 2mg', 'pabrik_obat' => 'Novell', 'kategori_obat' => 'Antidiare', 'tanggal_masuk' => '2025-12-28', 'tanggal_kadaluarsa' => '2028-10-22', 'tanggal_keluar' => '2026-01-15', 'stok_masuk' => 139, 'stok_keluar' => 70, 'stok_sisa' => 70, 'satuan_obat' => 'Tablet', 'harga_obat' => 6000, 'created_at' => now(), 'updated_at' => now()],
//         ]
