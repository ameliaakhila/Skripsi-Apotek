<?php

namespace Database\Seeders;

use App\Models\Admin\Obat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObatMasukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama supaya tidak dobel
        DB::table('tb_obat_masuk')->delete();

        $obats = DB::table('tb_obat')->get();

        $data = [];

        DB::transaction(function () use ($obats, &$data) {

            foreach ($obats as $obat) {

                $stokMasuk = rand(100, 200);
                $stokKeluar = rand(10, $stokMasuk); // supaya tidak negatif
                $tanggal_masuk = now();
                $nomor_urut_terakhir = Obat::max('id_obat') ?? 0;
                $nomor_urut_terakhir++;
                $nomor_urut_format = str_pad($nomor_urut_terakhir, 3, '0');
                $kodeBatch = 'BAC' . now()->format('dm') . 'SF' . $nomor_urut_format;
                $data[] = [
                    // kalau sebenarnya tidak perlu, bisa dihapus kolom ini
                    'kode_obat' => 'SF' . uniqid(),

                    'kode_batch' => $kodeBatch,
                    'tanggal_masuk' => $tanggal_masuk,
                    'tanggal_kedaluwarsa' => now()->copy()->addYears(rand(2, 5)),
                    'stok_masuk' => $stokMasuk,
                    'stok_keluar' => $stokKeluar,
                    'stok_sisa' => $stokMasuk - $stokKeluar,

                    'harga_obat' => rand(10000, 20000),

                    'id_satuan' => $obat->id_satuan,
                    'id_obat' => $obat->id_obat,
                    'id_kategori' => $obat->id_kategori,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        });

        // Insert per 500 data biar ringan
        foreach (array_chunk($data, 500) as $chunk) {
            DB::table('tb_obat_masuk')->insert($chunk);
        }
    }
}
