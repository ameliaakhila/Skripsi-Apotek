<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MutasiStokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ↓ STEP 1: Hapus semua data lama di tabel tb_mutasi_stok
        DB::table('tb_mutasi_stok')->delete();

        // ↓ STEP 2: Ambil semua id_cabang dari tabel tb_cabang (return collection)
        // Contoh: [1, 2, 3, 4, 5]
        $cabangs = DB::table('tb_cabang')->pluck('id_cabang');

        // ↓ STEP 3: Ambil semua id_stok_batch dari tabel tb_stok_batch
        // Contoh: [1, 2, 3, ..., 50]
        $stok_batches = DB::table('tb_stok_batch')->pluck('id_stok_batch');

        // ↓ STEP 4: Array kosong untuk menampung data mutasi yang akan dibuat
        $mutasi_stoks = [];

        // ↓ STEP 5: Loop 20 kali untuk membuat 20 record dummy
        for ($i = 0; $i < 20; $i++) {

            // ↓ Cabang asal SELALU cabang pertama (id_cabang = 1)
            $id_cabang_asal = $cabangs->first();

            // ↓ Cabang tujuan = random dari cabang yang TIDAK sama dengan asal
            // diff() = menghilangkan cabang asal dari collection
            // random() = ambil satu secara random
            $id_cabang_tujuan = $cabangs->diff([$id_cabang_asal])->random();

            // ↓ Ambil stok_masuk dari stok_batch random untuk menentukan jumlah_mutasi
            $stok_masuk = DB::table('tb_stok_batch')->inRandomOrder()->value('stok_masuk');

            // ↓ Jumlah mutasi = random antara 10 sampai stok_masuk maksimum (untuk memastikan tidak melebihi stok yang ada)
            $jumlah_mutasi = rand(1, $stok_masuk);


            // ↓ Tanggal mutasi = tanggal random antara hari ini sampai 30 hari lalu
            $tanggal_mutasi = now()->subDays(rand(0, 30));

            // ↓ Keterangan = text deskriptif mutasi
            $keterangan = 'Mutasi stok dari cabang ' . $id_cabang_asal . ' ke cabang ' . $id_cabang_tujuan;

            // ↓ STEP 6: Tambahkan satu record ke array $mutasi_stoks
            $mutasi_stoks[] = [
                'id_stok_batch' => $stok_batches->random(),  // Batch random
                'id_cabang_asal' => $id_cabang_asal,         // Cabang asal (selalu sama)
                'id_cabang_tujuan' => $id_cabang_tujuan,     // Cabang tujuan (random)
                'tanggal_mutasi' => $tanggal_mutasi,         // Tanggal random
                'jumlah_mutasi' => $jumlah_mutasi,           // Jumlah random
                'keterangan' => $keterangan,                 // Deskripsi
                'created_at' => now(),                       // Tanggal/waktu sekarang
                'updated_at' => now(),                       // Tanggal/waktu sekarang
            ];
        }

        // ↓ STEP 7: Batch insert - split array menjadi chunk 500 records
        // Contoh: 20 records dibagi per 500 = 1 chunk
        foreach (array_chunk($mutasi_stoks, 500) as $chunk) {
            // ↓ Insert chunk ke database
            DB::table('tb_mutasi_stok')->insert($chunk);
        }
    }
}
